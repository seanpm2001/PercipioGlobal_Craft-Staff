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

use Craft;
use craft\base\Component;
use craft\helpers\App;
use craft\helpers\Json;
use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\helpers\Logger;

use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\FetchEmployersJob;
use percipiolondon\staff\Staff;
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
            $employers[] = $this->parseEmployer($employer);
        }

        return $employers;
    }

    public function getEmployerById(int $employerId): array
    {
        $query = new Query();
        $query->from(Table::EMPLOYERS)
            ->where('id = ' . $employerId)
            ->one();
        $command = $query->createCommand();
        $employerQuery = $command->queryOne();

        if (!$employerQuery) {
            return [];
        }

        $employer = $this->parseEmployer($employerQuery);

        $query = new Query();
        $query->from(Table::PAY_OPTIONS)
            ->where('id = ' . $employer['defaultPayOptionsId'])
            ->one();
        $command = $query->createCommand();
        $payOptions = $command->queryOne();

        if ($payOptions) {
            $employer['defaultPayOptions'] = Staff::$plugin->payRuns->parsePayOptions($payOptions);
        }

        return $employer;
    }

    public function getEmployerNameById(int $employerId): string
    {
        $employer = Employer::findOne($employerId);

        if ($employer) {
            return SecurityHelper::decrypt($employer['name']);
        }

        return '';
    }



    /* FETCHES */
    public function fetchEmployerList(): array
    {
        $logger = new Logger();
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);

        if (!$api) {
            $logger->stdout("There is no staffology API key set" . PHP_EOL, $logger::FG_RED);
            Craft::error("There is no staffology API key set");
        } else {

            // connection props
            $base_url = 'https://api.staffology.co.uk/employers';
            $credentials = base64_encode('staff:' . $api);
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

                //TESTING PURPOSE
                if (App::parseEnv('$HUB_DEV_MODE') && App::parseEnv('$HUB_DEV_MODE') == 1) {
                    $employers = array_filter($employers, function($emp) {
                        return $emp['name'] == 'Acme Limited (Demo)' || $emp['name'] == 'Harding Financial Ltd';
                    });
                }

                if (count($employers) > 0) {
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
            ],
        ]));
    }





    /* SAVES */
    public function saveEmployer(array $employer)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save employer " . $employer['name'] ?? null . '...', $logger::RESET);

        $emp = Employer::findOne(['staffologyId' => $employer['id']]);

        if (!$emp) {
            $emp = new Employer();
        }

        $emp->siteId = Craft::$app->getSites()->currentSite->id;
        $emp->staffologyId = $employer['id'];
        $emp->name = $employer['name'] ?? null;
        $emp->title = $employer['name'] ?? null;
        $emp->logoUrl = $employer['logoUrl'] ?? null;
        $emp->crn = $employer['crn'] ?? null;
        $emp->startYear = $employer['startYear'] ?? null;
        $emp->currentYear = $employer['currentYear'] ?? null;
        $emp->employeeCount = $employer['employeeCount'] ?? null;

        $elementsService = Craft::$app->getElements();
        $success = $elementsService->saveElement($emp);

        if ($success) {
            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            //Save relations (FKs)
            if ($employer['address'] ?? null) {
                Staff::$plugin->addresses->saveAddressByEmployer($employer['address'], $emp->id);
            }

            if ($employer['defaultPayOptions'] ?? null) {
                Staff::$plugin->payOptions->savePayOptionsByEmployer($employer['defaultPayOptions'], $emp->id);
            }

        } else {
            $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

            $errors = "";

            foreach ($emp->errors as $err) {
                $errors .= implode(',', $err);
            }

            $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
            Craft::error($emp->errors, __METHOD__);
        }
    }





    /* PARSE SECURITY VALUES */
    public function parseEmployer(array $employer): array
    {
        $employer['name'] = SecurityHelper::decrypt($employer['name'] ?? '');
        $employer['crn'] = SecurityHelper::decrypt($employer['crn'] ?? '');
        $employer['slug'] = SecurityHelper::decrypt($employer['slug'] ?? '');
        $employer['logoUrl'] = SecurityHelper::decrypt($employer['logoUrl'] ?? '');

        return $employer;
    }
}
