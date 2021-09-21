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

use craft\helpers\Queue;
use percipiolondon\craftstaff\Craftstaff;

use Craft;
use craft\base\Component;
use percipiolondon\craftstaff\jobs\CreateEmployeeJob;
use percipiolondon\craftstaff\records\Employer;

/**
 * Employees Service
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
class Employees extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Craftstaff::$plugin->employees->exampleService()
     *
     * @return mixed
     */
    public function fetch()
    {
        $api = \Craft::parseEnv(Craftstaff::$plugin->getSettings()->staffologyApiKey);
        $credentials = base64_encode("craftstaff:".$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];

        if($api) {

            // GET EMPLOYERS
            $employers = Employer::find()->all();

            foreach($employers as $employer) {

                $base_url = 'https://api.staffology.co.uk/employers/' . $employer->staffologyId . '/employees';

                $client = new \GuzzleHttp\Client();

                //GET LIST OF EMPLOYEES INSIDE OF EMPLOYER
                try {

                    $response = $client->get($base_url, $headers);

                    $results = json_decode($response->getBody()->getContents(), true);

                    // LOOP THROUGH LIST WITH COMPANIES
                    foreach ($results as $i => $entry) {

                        Craft::$app->getQueue()->push(new CreateEmployeeJob([
                            'headers' => $headers,
                            'employer' => $employer,
                            'endpoint' => $entry['url'],
                        ]));

                    }
                } catch (\Throwable $e) {

                    \Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
                }
            }

            Craft::$app->getQueue()->run(0);
        }
    }
}
