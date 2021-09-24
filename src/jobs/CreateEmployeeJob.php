<?php

namespace percipiolondon\craftstaff\jobs;

use Craft;
use craft\elements\User;
use craft\queue\BaseJob;
use percipiolondon\craftstaff\Craftstaff;
use percipiolondon\craftstaff\records\Employee;
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
                $employee = json_decode($employee, true);
                $employeeRecord = Employee::findOne(['staffologyId' => $employee['id']]);

                // check if employee doesn't exist
                if (!$employeeRecord) {

                    $employeeRecord = new Employee();
                    $employeeRecord->employerId = $this->employer['id'];
                    $employeeRecord->staffologyId = $employee['id'];
                    $employeeRecord->siteId = Craft::$app->getSites()->currentSite->id;
                    $employeeRecord->personalDetails = $employee['personalDetails'] ?? '';
                    $employeeRecord->employmentDetails = $employee['employmentDetails'] ?? '';
                    $employeeRecord->autoEnrolment = $employee['autoEnrolment'] ?? '';
                    $employeeRecord->leaveSettings = $employee['leaveSettings'] ?? '';
                    $employeeRecord->rightToWork = $employee['rightToWork'] ?? '';
                    $employeeRecord->bankDetails = $employee['bankDetails'] ?? '';
                    $employeeRecord->status = $employee['status'] ?? '';
                    $employeeRecord->aeNotEnroledWarning = $employee['aeNotEnroledWarning'] ?? '';
                    $employeeRecord->sourceSystemId = $employee['sourceSystemId'] ?? '';
                    $employeeRecord->niNumber = $employee['personalDetails']['niNumber'] ?? '';
                    $employeeRecord->userId = null;

                    //If email adress exists --> create user
                    if(array_key_exists('email', $employee['personalDetails'])) {

                        $user = User::findOne(['email' => $employee['personalDetails']['email']]);

                        // check if user exists, if so, skip this step
                        if(!$user) {


                            //create user
                            $user = new User();
                            $user->firstName = $employee['personalDetails']['firstName'];
                            $user->lastName = $employee['personalDetails']['lastName'];
                            $user->username = $employee['personalDetails']['email'];
                            $user->email = $employee['personalDetails']['email'];

                            $success = Craft::$app->elements->saveElement($user, true);

                            if(!$success){
                                throw new Exception("The user couldn't be created");
                            }

                            //assign the userId to the employee record
                            $employeeRecord->userId = $user->id;

                            Craft::info("Craft Staff: new user creation: ".$user->id);

                            // assign user to group
                            $group = Craft::$app->getUserGroups()->getGroupByHandle('hardingUsers');
                            Craft::$app->getUsers()->assignUserToGroups($user->id, [$group->id]);
                        }
                    }

                    // save new employee
                    Craft::info($employeeRecord->userId);
                    $employeeRecord->save(false);

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
