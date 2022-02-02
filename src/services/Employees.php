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
use craft\elements\User;
use craft\helpers\Json;
use craft\helpers\Queue;

use percipiolondon\staff\Staff;
use percipiolondon\staff\jobs\CreateEmployeeJob;
use percipiolondon\staff\records\Employee;
use percipiolondon\staff\records\Employer;
use percipiolondon\staff\records\Permission;
use percipiolondon\staff\records\UserPermission;

use yii\base\BaseObject;
use yii\db\Exception;

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
 * @package   Staff
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
     *     Staff::$plugin->employees->exampleService()
     *
     * @return mixed
     */
    public function fetch()
    {
        $apiKey = \Craft::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);

        if ($apiKey) {

            $credentials = base64_encode('staff:' . $apiKey);
            $headers = [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ];

            // GET EMPLOYERS
            $employers = Employer::find()->all();

            foreach($employers as $employer) {

                $base_url = 'https://api.staffology.co.uk/employers/' . $employer->staffologyId . '/employees';
                $client = new \GuzzleHttp\Client();

                //GET LIST OF EMPLOYEES INSIDE OF EMPLOYER
                try {

                    $response = $client->get($base_url, $headers);
                    $results = Json::decodeIfJson($response->getBody()->getContents(), true);

                    // LOOP THROUGH LIST WITH COMPANIES
                    foreach ($results as $result) {


                        Queue::push(new CreateEmployeeJob([
                            'headers' => $headers,
                            'employer' => $employer,
                            'isDirector' => $result['metadata']['isDirector'] ?? false,
                            'endpoint' => $result['url'],
                        ]));

                    }
                } catch (\Throwable $e) {
                    echo "---- error -----\n";
                    var_dump($e->getMessage());
                    echo "\n---- end error ----";
                }
            }
        }
    }
}
