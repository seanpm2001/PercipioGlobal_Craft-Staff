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
            if(($data->line1 ?? null) && ($savedAddress['line1'] ?? '') !== $data->line1) {
                $address['line1'] = $data->line1;
            }

            // save line2 if a change has happened
            if(($data->line2 ?? null) && ($savedAddress['line2'] ?? '') !== $data->line2) {
                $address['line2'] = $data->line2;
            }

            // save line3 if a change has happened
            if(($data->line3 ?? null) && ($savedAddress['line3'] ?? '') !== $data->line3) {
                $address['line3'] = $data->line3;
            }

            // save line4 if a change has happened
            if(($data->line4 ?? null) && ($savedAddress['line4'] ?? '') !== $data->line4) {
                $address['line4'] = $data->line4;
            }

            // save postCode if a change has happened
            if(($data->postCode ?? null) && ($savedAddress['postCode'] ?? '') !== $data->postCode) {
                $address['postCode'] = $data->postCode;
            }

            // save country if a change has happened
            if(($data->country ?? null) && ($savedAddress['country'] ?? '') !== $data->country) {
                $address['country'] = $data->country;
            }
        }

        $personalDetails = [];
        $personalDetails['address'] = $address;

        $objPersonalDetails = new \stdClass();
        $objPersonalDetails->personalDetails = $personalDetails;

        return json_encode($objPersonalDetails);
    }

    public function parse(string $json): ?string
    {
        $data = json_decode($json);
        $address = new \stdClass();

        if($data->personalDetails->address->line1 ?? null) {
            $address->line1 = $data->personalDetails->address->line1;
        }
        if($data->personalDetails->address->line2 ?? null) {
            $address->line2 = $data->personalDetails->address->line2;
        }
        if($data->personalDetails->address->line3 ?? null) {
            $address->line3 = $data->personalDetails->address->line3;
        }
        if($data->personalDetails->address->line4 ?? null) {
            $address->line4 = $data->personalDetails->address->line4;
        }
        if($data->personalDetails->address->postCode ?? null) {
            $address->postCode = $data->personalDetails->address->postCode;
        }
        if($data->personalDetails->address->country ?? null) {
            $address->country = $data->personalDetails->address->country;
        }

        return json_encode($address);
    }

    public function current(int $id): ?string
    {
        $address = Staff::$plugin->addresses->getAddressByEmployee($id)->toArray();
        $address = Staff::$plugin->addresses->parseAddress($address);

        $current = [];
        $current['line1'] = $address['address1'] ?? '';
        $current['line2'] = $address['address2'] ?? '';
        $current['line3'] = $address['address3'] ?? '';
        $current['line4'] = $address['address4'] ?? '';
        $current['postCode'] = $address['zipCode'] ?? '';
        $current['country'] = Staff::$plugin->addresses->getCountryById($address['countryId']);

        return json_encode($current);
    }
}