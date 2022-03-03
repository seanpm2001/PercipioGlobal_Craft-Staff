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
use percipiolondon\staff\console\controllers\FetchController;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\jobs\FetchEmployersJob;

use Craft;
use craft\base\Component;
use craft\helpers\Json;
use yii\helpers\Console;

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
    public function fetchEmployerList(): array
    {
        $logger = new Logger();
        $api = App::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);

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

    public function saveEmployer(array $employer){
        $logger = new Logger();

        $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer['id']]);

        if (!$employerRecord) {

            $logger->stdout("✓ Save employer " . $employer['name'] ?? null . '...', $logger::RESET);

            $emp = new Employer();

            $emp->siteId = Craft::$app->getSites()->currentSite->id;
            $emp->staffologyId = $employer['id'];
            $emp->name = $employer['name'] ?? null;
            $emp->title = $employer['name'] ?? null;
            $emp->logoUrl = $employer['logoUrl'] ?? null;
            $emp->crn = $employer['crn'] ?? null;
            $emp->address = $employer['address'] ?? null;
            $emp->startYear = $employer['startYear'] ?? null;
            $emp->currentYear = $employer['currentYear'] ?? null;
            $emp->employeeCount = $employer['employeeCount'] ?? null;

            $elementsService = Craft::$app->getElements();
            $success = $elementsService->saveElement($emp);

            if($success){
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
            }else{
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);
            }
        }


    }

}
