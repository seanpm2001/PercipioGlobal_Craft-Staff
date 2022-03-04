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
use craft\helpers\Json;
use craft\helpers\Queue;

use percipiolondon\staff\console\controllers\FetchController;
use percipiolondon\staff\jobs\CreateEmployeeJob;
use percipiolondon\staff\Staff;
use percipiolondon\staff\jobs\FetchEmployeesListJob;
use percipiolondon\staff\helpers\Logger;

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
    public function fetchEmployeesByEmployer(array $employer, array $progress = [])
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchEmployeesListJob([
            'description' => 'Fetch the employees',
            'criteria' => [
                'employer' => $employer,
                'progress' => $progress
            ]
        ]));

    }

    public function fetchEmployee(array $employee, string $progress = "")
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreateEmployeeJob([
            'description' => 'Save employees',
            'criteria' => [
                'employee' => $employee,
                'progress' => $progress
            ]
        ]));
    }

    public function saveEmployee(array $employee, string $employeeName, string $progress = "")
    {
        $logger = new Logger();
        $logger->stdout($progress."✓ Save employee " .$employeeName . '...', $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

        //@TODO save

//        $employeeRecord = EmployeeRecord::findOne(['staffologyId' => $employee['id']]);
//
//                // check if employee doesn't exist
//                if (!$employeeRecord) {
//
//                    $employeeRecord = new Employee();
//
//                    $employeeRecord->employerId = $this->employer['id'];
//                    $employeeRecord->staffologyId = $employee['id'];
//                    $employeeRecord->siteId = Craft::$app->getSites()->currentSite->id;
//                    $employeeRecord->personalDetails = $employee['personalDetails'] ?? null;
//                    $employeeRecord->employmentDetails = $employee['employmentDetails'] ?? null;
//                    $employeeRecord->autoEnrolment = $employee['autoEnrolment'] ?? null;
//                    $employeeRecord->leaveSettings = $employee['leaveSettings'] ?? null;
//                    $employeeRecord->rightToWork = $employee['rightToWork'] ?? null;
//                    $employeeRecord->bankDetails = $employee['bankDetails'] ?? null;
//                    $employeeRecord->status = $employee['status'] ?? '';
//                    $employeeRecord->aeNotEnroledWarning = $employee['aeNotEnroledWarning'] ?? null;
//                    $employeeRecord->sourceSystemId = $employee['sourceSystemId'] ?? null;
//                    $employeeRecord->niNumber = $employee['personalDetails']['niNumber'] ?? null;
//                    $employeeRecord->userId = null;
//                    $employeeRecord->isDirector = $this->isDirector ?? false;
//
//                    // save new employee
//                    $elementsService = Craft::$app->getElements();
//                    $elementsService->saveElement($employeeRecord);
//                }
    }

//    /**
//     * This function can literally be anything you want, and you can have as many service
//     * functions as you want
//     *
//     * From any other plugin file, call it like this:
//     *
//     *     Staff::$plugin->employees->exampleService()
//     *
//     * @return mixed
//     */
//    public function fetch2()
//    {
//        $apiKey = \Craft::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
//
//        if ($apiKey) {
//
//            $credentials = base64_encode('staff:' . $apiKey);
//            $headers = [
//                'headers' => [
//                    'Authorization' => 'Basic ' . $credentials,
//                ],
//            ];
//
//            // GET EMPLOYERS
//            $employers = Employer::find()->all();
//
//            foreach($employers as $employer) {
//
//                $base_url = 'https://api.staffology.co.uk/employers/' . $employer->staffologyId . '/employees';
//                $client = new \GuzzleHttp\Client();
//
//                //GET LIST OF EMPLOYEES INSIDE OF EMPLOYER
//                try {
//
//                    $response = $client->get($base_url, $headers);
//                    $results = Json::decodeIfJson($response->getBody()->getContents(), true);
//
//                    // LOOP THROUGH LIST WITH COMPANIES
//                    foreach ($results as $result) {
//
//                        echo "test";
//
//                        Queue::push(new CreateEmployeeJob([
//                            'headers' => $headers,
//                            'employer' => $employer,
//                            'isDirector' => $result['metadata']['isDirector'] ?? false,
//                            'endpoint' => $result['url'],
//                        ]));
//
//                    }
//                } catch (\Throwable $e) {
//                    echo "---- error -----\n";
//                    var_dump($e->getMessage());
//                    echo "\n---- end error ----";
//                }
//            }
//        }
//    }
}
