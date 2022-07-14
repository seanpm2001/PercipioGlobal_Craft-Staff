<?php

namespace percipiolondon\staff\helpers\requests;

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
    public function create(string $json): string
    {
        $data = json_decode($json);

        $address = [];
        $address['line1'] = $data->line1 ?? '';
        $address['line2'] = $data->line2 ?? '';
        $address['line3'] = $data->line3 ?? '';
        $address['postCode'] = $data->postCode ?? '';
        $address['country'] = $data->country ?? '';

        $personalDetails = [];
        $personalDetails['address'] = $address;

        $objPersonalDetails = new \stdClass();
        $objPersonalDetails->personalDetails = $personalDetails;

        return json_encode($objPersonalDetails);
    }
}