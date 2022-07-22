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
class CreateAddressRequest
{
    /**
     * @param string $json
     * @return string
     */
    public function create(string $json, int $employeeId): string
    {
        $data = json_decode($json);

        $address = [];

        // get personal details of employee and check which fields got changed
        if($employeeId) {
            $personalDetails = Staff::$plugin->employees->getPersonalDetailsByEmployee($employeeId);
            $savedAddress = $personalDetails['address'] ?? null;

            // save line1 if a change has happened
            if(($savedAddress['line1'] ?? '') !== $data->line1) {
                $address['line1'] = $data->line1;
            }

            // save line2 if a change has happened
            if(($savedAddress['line2'] ?? '') !== $data->line2) {
                $address['line2'] = $data->line2;
            }

            // save line3 if a change has happened
            if(($savedAddress['line3'] ?? '') !== $data->line3) {
                $address['line3'] = $data->line3;
            }

            // save postCode if a change has happened
            if(($savedAddress['postCode'] ?? '') !== $data->postCode) {
                $address['postCode'] = $data->postCode;
            }

            // save country if a change has happened
            if(($savedAddress['country'] ?? '') !== $data->country) {
                $address['country'] = $data->country;
            }
        }

        $personalDetails = [];
        $personalDetails['address'] = $address;

        $objPersonalDetails = new \stdClass();
        $objPersonalDetails->personalDetails = $personalDetails;

        return json_encode($objPersonalDetails);
    }
}