<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\craftstaff\services;

use percipiolondon\craftstaff\helpers\AssetHelper;
use percipiolondon\craftstaff\Craftstaff;


use Craft;
use craft\base\Component;
use percipiolondon\craftstaff\records\Employer as EmployerRecord;
use percipiolondon\craftstaff\elements\Employer;

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
 * @package   Craftstaff
 * @since     1.0.0-alpha.1
 */
class Employers extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Craftstaff::$plugin->employers->exampleService()
     *
     * @return mixed
     */
    public function fetch()
    {
        $api = Craft::parseEnv(Craftstaff::$plugin->getSettings()->staffologyApiKey);

        if(!$api) {
            var_dump("There's no staffoligy API key set");
            Craft::error("There's no staffoligy API key set");
        }

        if ($api) {

            // connection props
            $base_url = 'https://api.staffology.co.uk/employers';
            $credentials = base64_encode("craftstaff:".$api);
            $headers = [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ];

            $client = new \GuzzleHttp\Client();


            // FETCH THE EMPLOYERS LIST
            try {

                $response = $client->get($base_url, $headers);

                $results = json_decode($response->getBody()->getContents());

                // LOOP THROUGH LIST WITH EMPLOYERS
                foreach($results as $entry) {

                    // FETCH DETAILED EMPLOYER INFO
                    try {
                        $response = $client->get($entry->url, $headers);

                        $employer = $response->getBody()->getContents();

                        if ($employer) {
                            $employer = json_decode($employer);

                            $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer->id]);

//                            var_dump($employer->id);

                            if (!$employerRecord) {

                                $employerRecord = new Employer();

                                $employerRecord->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $employer->name), '-'));
                                $employerRecord->siteId = Craft::$app->getSites()->currentSite->id;
                                $employerRecord->staffologyId = $employer->id ?? "";
                                $employerRecord->name = $employer->name ?? '';
                                //TODO: logo --> AssetHelper::fetchRemoteImage($employer->logoUrl);
                                $employerRecord->crn = $employer->crn ?? '';
                                $employerRecord->address = json_encode($employer->address) ?? '';
                                $employerRecord->hmrcDetails = json_encode($employer->hmrcDetails) ?? '';
                                $employerRecord->startYear = $employer->startYear ?? '';
                                $employerRecord->currentYear = $employer->currentYear ?? '';
                                $employerRecord->employeeCount = $employer->employeeCount ?? 0;
                                $employerRecord->defaultPayOptions = json_encode($employer->defaultPayOptions) ?? '';

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

//if (Craftstaff::$plugin->getSettings()->staffologyApiKey) {
//
//    $api = Craft::parseEnv(Craftstaff::$plugin->getSettings()->staffologyApiKey);
//
//    $base_url = 'https://api.staffology.co.uk/employers';
//    $credentials = base64_encode("craftstaff:".$result);
//    $headers = [
//        'headers' => [
//            'Authorization' => 'Basic ' . $credentials,
//        ],
//    ];
//
//    $client = new \GuzzleHttp\Client();
//
//    try {
//
//        $response = $client->get($base_url, $headers);
//
//        return $response->getBody();
//
//    } catch (\Exception $e) {
//
////                return [
////                    'error' => true,
////                    'reason' => $e->getMessage()
////                ];
//
//        return "error";
//
//    }
//}
