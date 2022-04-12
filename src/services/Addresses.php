<?php

namespace percipiolondon\staff\services;

use craft\base\Component;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\Address;
use percipiolondon\staff\records\Countries;
use yii\db\Exception;

class Addresses extends Component
{
    public function saveAddress(array $address, int $addressId = null): Address
    {
        if ($addressId) {
            $record = $this->getAddressById($addressId);

            if (!$record) {
                throw new Exception('Invalid address ID: ' . $addressId);
            }
        } else {
            $record = new Address();
        }

        $countryName = $address['country'] ?? 'England';

        $country = Countries::find()
            ->where(['name' => $countryName])
            ->one();

        $record->countryId = $country->id ?? null;
        $record->address1 = SecurityHelper::encrypt($address['line1'] ?? '');
        $record->address2 = SecurityHelper::encrypt($address['line2'] ?? '');
        $record->address3 = SecurityHelper::encrypt($address['line3'] ?? '');
        $record->address4 = SecurityHelper::encrypt($address['line4'] ?? '');
        $record->address5 = SecurityHelper::encrypt($address['line5'] ?? '');
        $record->zipCode = SecurityHelper::encrypt($address['postCode'] ?? '');

        $record->save();

        return $record;
    }
    
    public function getAddressById(int $id): Address
    {
        return Address::findOne($id);
    }
}
