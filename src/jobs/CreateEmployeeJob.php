<?php

namespace percipiolondon\craftstaff\jobs;

use Craft;
use craft\elements\User;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\craftstaff\Craftstaff;
use percipiolondon\craftstaff\records\Employee as EmployeeRecord;
use percipiolondon\craftstaff\elements\Employee;
use percipiolondon\craftstaff\records\Permission;
use yii\db\Exception;

class CreateEmployeeJob extends BaseJob
{
    public $headers;
    public $endpoint;
    public $employer;

    public function execute($queue): void
    {
        // FETCH DETAILED EMPLOYEE
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($this->endpoint, $this->headers);

            $employee = $response->getBody()->getContents();

            if ($employee) {
                $employee = Json::decodeIfJson($employee, true);
                $employeeRecord = EmployeeRecord::findOne(['staffologyId' => $employee['id']]);

                var_dump($employee);

                // check if employee doesn't exist
                if (!$employeeRecord) {

                    $employeeRecord = new Employee();

                    $employeeRecord->employerId = $this->employer['id'];
                    $employeeRecord->staffologyId = $employee['id'];
                    $employeeRecord->siteId = Craft::$app->getSites()->currentSite->id;
                    $employeeRecord->personalDetails = $employee['personalDetails'] ?? null;
                    $employeeRecord->employmentDetails = $employee['employmentDetails'] ?? null;
                    $employeeRecord->autoEnrolment = $employee['autoEnrolment'] ?? null;
                    $employeeRecord->leaveSettings = $employee['leaveSettings'] ?? null;
                    $employeeRecord->rightToWork = $employee['rightToWork'] ?? null;
                    $employeeRecord->bankDetails = $employee['bankDetails'] ?? null;
                    $employeeRecord->status = $employee['status'] ?? '';
                    $employeeRecord->aeNotEnroledWarning = $employee['aeNotEnroledWarning'] ?? null;
                    $employeeRecord->sourceSystemId = $employee['sourceSystemId'] ?? null;
                    $employeeRecord->niNumber = $employee['personalDetails']['niNumber'] ?? null;
                    $employeeRecord->userId = null;

                    // save new employee
                    $elementsService = Craft::$app->getElements();
                    $elementsService->saveElement($employeeRecord);

                    //assign permissions to employee
                    $permissions = [Permission::findOne(['name' => 'access:employer'])];
                    Craftstaff::$plugin->userPermissions->createPermissions($permissions, $employeeRecord->userId, $employeeRecord->id);
                }
            }
        } catch (\Exception $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        } catch (\Throwable $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }
    }
}
