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
class CreateTelephoneRequest
{
    /**
     * @param string $json
     * @return string
     */
    public function create(string $json, int $employeeId): string
    {
        // get personal details of employee and check which fields got changed
        if ($employeeId) {
            $data = json_decode($json);
            $dbPersonalDetails = Staff::$plugin->employees->getPersonalDetailsByEmployee($employeeId, true);
            $savedPersonalDetails = $dbPersonalDetails ?? null;
            $details = $savedPersonalDetails;

            //remap the address for Staffology
            $addressHelper = new ParseAddress();
            $details['address'] = $addressHelper->parse($dbPersonalDetails['address']);

            // save telephone if a change has happened
            if (($data->telephone ?? null) && ($savedPersonalDetails['telephone'] ?? '') !== $data->telephone) {
                $details['telephone'] = $data->telephone;
            }

            // save mobile if a change has happened
            if (($data->mobile ?? null) && ($savedPersonalDetails['mobile'] ?? '') !== $data->mobile) {
                $details['mobile'] = $data->mobile;
            }

            if (!is_null($details['dateOfBirth'])) {
                $dateOfBirth = new \DateTime($details['dateOfBirth']);
                $details['dateOfBirth'] = $dateOfBirth->format('Y-m-d');
            }

            $objPersonalDetails = new \stdClass();
            $objPersonalDetails->personalDetails = $details;

            return json_encode($objPersonalDetails);
        }

        return '';
    }

    public function parse(string $json): ?string
    {
        $data = json_decode($json);
        $personalDetails = new \stdClass();

        if ($data->personalDetails->telephone ?? null) {
            $personalDetails->telephone = $data->personalDetails->telephone;
        }
        if ($data->personalDetails->mobile ?? null) {
            $personalDetails->mobile = $data->personalDetails->mobile;
        }

        return json_encode($personalDetails);
    }

    public function current(int $id): ?string
    {
        $personalDetails = Staff::$plugin->employees->getPersonalDetailsByEmployee($id);
        $personalDetails = Staff::$plugin->employees->parsePersonalDetails($personalDetails);

        $current = [];
        $current['telephone'] = $personalDetails['telephone'] ?? '';
        $current['mobile'] = $personalDetails['mobile'] ?? '';

        return json_encode($current);
    }
}