<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\services;

use craft\helpers\App;
use percipiolondon\attendees\helpers\Log;
use percipiolondon\staff\db\Table;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\Staff;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\jobs\FetchEmployersJob;

use Craft;
use craft\base\Component;
use craft\helpers\Json;
use yii\db\Exception;
use yii\db\Query;

/**
 * Employers Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */
class Employers extends Component
{
    // Public Methods
    // =========================================================================


    /* GETTERS */
    public function getEmployers(): array
    {
        $query = new Query();
        $query->from(Table::EMPLOYERS)
            ->all();
        $command = $query->createCommand();
        $employersQuery = $command->queryAll();

        $employers = [];

        if (!$employersQuery) {
            return [];
        }

        foreach ($employersQuery as $employer) {
            $employers[] = $this->_parseEmployer($employer);
        }

        return $employers;
    }

    public function getEmployerById(int $employerId): array
    {
        $query = new Query();
        $query->from(Table::EMPLOYERS)
            ->where('id = '.$employerId)
            ->one();
        $command = $query->createCommand();
        $employerQuery = $command->queryOne();

        if (!$employerQuery) {
            return [];
        }

        $employer = $this->_parseEmployer($employerQuery);

        $query = new Query();
        $query->from(Table::PAY_OPTIONS)
            ->where('id = '.$employer['defaultPayOptionsId'])
            ->one();
        $command = $query->createCommand();
        $payOptions = $command->queryOne();

        if($payOptions){
            $employer['defaultPayOptions'] = Staff::$plugin->payRuns->parsePayOptions($payOptions);
        }

        return $employer;
    }



    /* FETCHES */
    public function fetchEmployerList(): array
    {
        $logger = new Logger();
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);

        if(!$api) {

            $logger->stdout("There is no staffology API key set" . PHP_EOL, $logger::FG_RED);
            Craft::error("There is no staffology API key set");

        }else{

            // connection props
            $base_url = 'https://api.staffology.co.uk/employers';
            $credentials = base64_encode('staff:'.$api);
            $headers = [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ];

            $client = new \GuzzleHttp\Client();

            $logger->stdout("Start fetching employer list" . PHP_EOL, $logger::RESET);

            // FETCH THE EMPLOYERS LIST
            try {

                $response = $client->get($base_url, $headers);

                $employers = Json::decodeIfJson($response->getBody()->getContents(), true);

                if(count($employers) > 0){

                    $logger->stdout("End fetching list of " . count($employers) . " employers " . PHP_EOL, $logger::RESET);

                    return $employers;

                }

                $logger->stdout("There are no employers found on Staffology" . PHP_EOL, $logger::FG_RED);
                Craft::error("There are no employers found on Staffology");


            } catch (\Exception $e) {

                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);

            }

        }

        return [];
    }

    public function fetchEmployers(array $employers)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchEmployersJob([
            'description' => 'Fetching employers',
            'criteria' => [
                'employers' => $employers,
            ]
        ]));
    }





    /* SAVES */
    public function saveEmployer(array $employer)
    {
        $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer['id']]);

        $logger = new Logger();
        $logger->stdout("✓ Save employer " . $employer['name'] ?? null . '...', $logger::RESET);

        try {

            if (!$employerRecord) {
                $employerRecord = new EmployerRecord();
            }

            //foreign keys
            $addressId = $employerRecord->addressId ?? null;
            $defaultPayOptionsId = $employerRecord->defaultPayOptionsId ?? null;

            // Attach the foreign keys
            $address = $employer['address'] ? Staff::$plugin->addresses->saveAddress($employer['address'], $addressId) : null;
            $payOptions = $employer['defaultPayOptions'] ? Staff::$plugin->payRuns->savePayOptions($employer['defaultPayOptions'], $defaultPayOptionsId) : null;

            //save
            $employerRecord->defaultPayOptionsId = $payOptions->id ?? null;
            $employerRecord->addressId = $address->id ?? null;
            $employerRecord->slug = SecurityHelper::encrypt((strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->name ?? ''), '-'))));
            $employerRecord->staffologyId = $employer['id'] ?? null;
            $employerRecord->name = SecurityHelper::encrypt($employer['name'] ?? '');
            $employerRecord->crn = SecurityHelper::encrypt($employer['crn'] ?? '');
            $employerRecord->logoUrl = SecurityHelper::encrypt($employer['logoUrl'] ?? '');
            $employerRecord->startYear = $employer['startYear'] ?? null;
            $employerRecord->currentYear = $employer['currentYear'] ?? null;
            $employerRecord->employeeCount = $employer['employeeCount'] ?? null;

            $success = $employerRecord->save(false);

            if($success){
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                $this->saveEmployerElement($employer);
            }else{
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach($employerRecord->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($employerRecord->errors, __METHOD__);
            }

        } catch (\Exception $e) {
            $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }

    }





    /* PARSE SECURITY VALUES */
    private function _parseEmployer(array $employer) :array
    {
        $employer['name'] = SecurityHelper::decrypt($employer['name'] ?? '');
        $employer['crn'] = SecurityHelper::decrypt($employer['crn'] ?? '');
        $employer['slug'] = SecurityHelper::decrypt($employer['slug'] ?? '');
        $employer['logoUrl'] = SecurityHelper::decrypt($employer['logoUrl'] ?? '');

        return $employer;
    }





    /* SAVES ELEMENTS */
    public function saveEmployerElement(array $employer): bool
    {
        $emp = new Employer();
        $elementsService = Craft::$app->getElements();
        return $elementsService->saveElement($emp);

    }
}
