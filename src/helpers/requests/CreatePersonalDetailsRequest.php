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

    public function parse(string $json): ?string
    {
        $data = json_decode($json);
        $personalDetails = new \stdClass();

        if($data->personalDetails->title ?? null) {
            $personalDetails->title = $data->personalDetails->title;
        }
        if($data->personalDetails->firstName ?? null) {
            $personalDetails->firstName = $data->personalDetails->firstName;
        }
        if($data->personalDetails->middleName ?? null) {
            $personalDetails->middleName = $data->personalDetails->middleName;
        }
        if($data->personalDetails->lastName ?? null) {
            $personalDetails->lastName = $data->personalDetails->lastName;
        }
        if($data->personalDetails->maritalStatus ?? null) {
            $personalDetails->maritalStatus = $data->personalDetails->maritalStatus;
        }

        return json_encode($personalDetails);
    }

    public function current(int $id): ?string
    {
        $personalDetails = Staff::$plugin->employees->getPersonalDetailsByEmployee($id);
        $personalDetails = Staff::$plugin->employees->parsePersonalDetails($personalDetails);

        $current = [];
        $current['title'] = $personalDetails['title'] ?? '';
        $current['firstName'] = $personalDetails['firstName'] ?? '';
        $current['lastName'] = $personalDetails['lastName'] ?? '';
        $current['middleName'] = $personalDetails['middleName'] ?? '';
        $current['maritalStatus'] = $personalDetails['maritalStatus'] ?? '';

        return json_encode($current);
    }
}