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
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
//        $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer->id]);
//
//        if (!$employerRecord) {
//
//            $employerRecord = new Employer();
//
//            $employerRecord->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $employer->name), '-'));
//            $employerRecord->siteId = Craft::$app->getSites()->currentSite->id;
//            $employerRecord->staffologyId = $employer->id;
//            $employerRecord->name = $employer->name ?? null;
//            //TODO: logo --> AssetHelper::fetchRemoteImage($employer->logoUrl)
//            $employerRecord->crn = $employer->crn ?? null;
//            $employerRecord->address = Json::encode($employer->address);
//            $employerRecord->hmrcDetails = Json::encode($employer->hmrcDetails);
//            $employerRecord->startYear = $employer->startYear ?? null;
//            $employerRecord->currentYear = $employer->currentYear ?? null;
//            $employerRecord->employeeCount = $employer->employeeCount ?? 0;
//            $employerRecord->defaultPayOptions = Json::encode($employer->defaultPayOptions);
//
//            $elementsService = Craft::$app->getElements();
//            $elementsService->saveElement($employerRecord);
//        }
    }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Staff::$plugin->employers->exampleService()
     *
     * @return mixed
     */
    public function fetch2()
    {
        $api = Craft::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);

        if(!$api) {
            Craft::error("There is no staffology API key set");
        }

        if ($api) {

            // connection props
            $base_url = 'https://api.staffology.co.uk/employers';
            $credentials = base64_encode('staff:'.$api);
            $headers = [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ];

            $client = new \GuzzleHttp\Client();

            // FETCH THE EMPLOYERS LIST
            try {

                $response = $client->get($base_url, $headers);

                $results = Json::decodeIfJson($response->getBody()->getContents(), false);

                // LOOP THROUGH LIST WITH EMPLOYERS
                foreach($results as $entry) {

                    // FETCH DETAILED EMPLOYER INFO
                    try {
                        $response = $client->get($entry->url, $headers);
                        $employer = $response->getBody()->getContents();

                        if ($employer) {
                            $employer = Json::decodeIfJson($employer, false);
                            $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer->id]);

                            if (!$employerRecord) {

                                $employerRecord = new Employer();

                                $employerRecord->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $employer->name), '-'));
                                $employerRecord->siteId = Craft::$app->getSites()->currentSite->id;
                                $employerRecord->staffologyId = $employer->id;
                                $employerRecord->name = $employer->name ?? null;
                                //TODO: logo --> AssetHelper::fetchRemoteImage($employer->logoUrl)
                                $employerRecord->crn = $employer->crn ?? null;
                                $employerRecord->address = Json::encode($employer->address);
                                $employerRecord->hmrcDetails = Json::encode($employer->hmrcDetails);
                                $employerRecord->startYear = $employer->startYear ?? null;
                                $employerRecord->currentYear = $employer->currentYear ?? null;
                                $employerRecord->employeeCount = $employer->employeeCount ?? 0;
                                $employerRecord->defaultPayOptions = Json::encode($employer->defaultPayOptions);

                                $elementsService = Craft::$app->getElements();
                                $elementsService->saveElement($employerRecord);
                            }
                        }
                    } catch (\Exception $e) {

                        echo "---- error -----\n";
                        var_dump($e->getMessage());
                        Craft::error($e->getMessage(), __METHOD__);
                        echo "\n---- end error ----";
                    }

                }

                return "success";

            } catch (\Exception $e) {

                echo "---- error -----\n";
                var_dump($e->getMessage());
                Craft::error($e->getMessage(), __METHOD__);
                echo "\n---- end error ----";

            }

        }
    }
}
