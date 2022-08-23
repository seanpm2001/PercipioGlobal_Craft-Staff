<?php

namespace percipiolondon\staff\helpers\requests;


/**
 * Class CreateAddressRequest
 *
 * @package percipiolondon\staff\helpers
 */
class ParseAddress
{
    /**
     * @param string $json
     * @return string
     */
    public function parse(array $address): array
    {
        //remap the address for Staffology
        $address['line1'] = $address['address1'];
        $address['line2'] = $address['address2'];
        $address['line3'] = $address['address3'];
        $address['line4'] = $address['address4'];
        $address['postCode'] = $address['zipCode'];

        unset($address['address1']);
        unset($address['address2']);
        unset($address['address3']);
        unset($address['address4']);
        unset($address['zipCode']);

        return $address;
    }
}