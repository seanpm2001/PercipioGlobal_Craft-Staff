<?php

namespace percipiolondon\craftstaff\jobs;

use craft\queue\BaseJob;
use percipiolondon\craftstaff\records\Employee;
use percipiolondon\craftstaff\records\Employer;

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
                $employee = json_decode($employee, false);

                $employeeRecord = Employee::findOne(['staffologyId' => $employee->id]);

                if (!$employeeRecord) {

                    $employeeRecord = new Employee();
                    $employeeRecord->employerId = $this->employer->id;
                    $employeeRecord->staffologyId = $employee->id;
                    $employeeRecord->userId = 1;
                    $employeeRecord->siteId = \Craft::$app->getSites()->currentSite->id;
                    $employeeRecord->personalDetails = $employee->personalDetails ?? '';
                    $employeeRecord->employmentDetails = $employee->employmentDetails ?? '';
                    $employeeRecord->autoEnrolment = $employee->autoEnrolment ?? '';
                    $employeeRecord->leaveSettings = $employee->leaveSettings ?? '';
                    $employeeRecord->rightToWork = $employee->rightToWork ?? '';
                    $employeeRecord->bankDetails = $employee->bankDetails ?? '';
                    $employeeRecord->status = $employee->status ?? '';
                    $employeeRecord->aeNotEnroledWarning = $employee->aeNotEnroledWarning ?? '';
                    $employeeRecord->sourceSystemId = $employee->sourceSystemId ?? '';

                    //TODO: check if email adress exists --> create user
                }

                $employeeRecord->save(false);
            }
        } catch (\Exception $e) {
            \Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }
    }
}
