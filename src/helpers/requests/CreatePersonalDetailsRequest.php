<?php

namespace percipiolondon\staff\helpers\requests;

use Craft;
use percipiolondon\staff\services\Employees;
use percipiolondon\staff\Staff;

/**
 * Class CreateAddressRequest
 *
 * @package percipiolondon\staff\helpers
 */
class CreatePersonalDetailsRequest
{
    /**
     * @param string $json
     * @return string
     */
    public function create(string $json, int $employeeId): string
    {
        $data = json_decode($json);

        $details = [];

        // get personal details of employee and check which fields got changed
        if($employeeId) {
            $personalDetails = Staff::$plugin->employees->getPersonalDetailsByEmployee($employeeId);
            $savedPersonalDetails = $personalDetails ?? null;

            // save title if a change has happened
            if(($data->title ?? null) && ($savedPersonalDetails['title'] ?? '') !== $data->title) {
                $details['title'] = $data->title;
            }

            // save firstName if a change has happened
            if(($data->firstName ?? null) && ($savedPersonalDetails['firstName'] ?? '') !== $data->firstName) {
                $details['firstName'] = $data->firstName;
            }

            // save middleName if a change has happened
            if(($data->middleName ?? null) && ($savedPersonalDetails['middleName'] ?? '') !== $data->middleName) {
                $details['middleName'] = $data->middleName;
            }

            // save lastName if a change has happened
            if(($data->lastName ?? null) && ($savedPersonalDetails['lastName'] ?? '') !== $data->lastName) {
                $details['lastName'] = $data->lastName;
            }

            // save maritalStatus if a change has happened
            if(($data->maritalStatus ?? null) && ($savedPersonalDetails['maritalStatus'] ?? '') !== $data->maritalStatus) {
                $details['maritalStatus'] = $data->maritalStatus;
            }
        }

        $personalDetails = [];

        $objPersonalDetails = new \stdClass();
        $objPersonalDetails->personalDetails = $details;

        return json_encode($objPersonalDetails);
    }
}