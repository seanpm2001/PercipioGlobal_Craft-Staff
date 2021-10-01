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
use percipiolondon\craftstaff\records\Employer as EmployerRecord;
use percipiolondon\craftstaff\elements\Employer;

use Craft;
use craft\base\Component;
use craft\helpers\Json;

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
            Craft::error("There is no staffology API key set");
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
