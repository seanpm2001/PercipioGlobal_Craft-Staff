<?php

namespace percipiolondon\staff\services;

use craft\base\Component;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\Address;
use percipiolondon\staff\records\Countries;
use yii\db\Exception;

/**
 * Class Addresses
 *
 * @package percipiolondon\staff\services
 */
class Addresses extends Component
{
    /**
     * @param array $address
     * @param int|null $employerId
     * @return Address
     */
    public function saveAddressByEmployer(array $address, int $employerId = null): Address
    {
        $record = $this->getAddressByEmployer($employerId);

        if (!$record) {
            $record = new Address();
        }

        $record->employerId = $employerId;

        return $this->_saveRecord($record, $address);
    }
    /**
     * @param array $address
     * @param int|null $employeeId
     * @return Address
     */
    public function saveAddressByEmployee(array $address, int $employeeId = null): Address
    {
        $record = $this->getAddressByEmployee($employeeId);

        if (!$record) {
            $record = new Address();
        }

        $record->employeeId = $employeeId;

        return $this->_saveRecord($record, $address);
    }

    /**
     * @param int $id
     * @return Address|null
     */
    public function getAddressByEmployer(int $id): ?Address
    {
        return Address::findOne(['employerId' => $id]);
    }

    /**
     * @param int $id
     * @return Address|null
     */
    public function getAddressByEmployee(int $id): ?Address
    {
        return Address::findOne(['employeeId' => $id]);
    }

    /**
     * @param int $id
     * @return Address|null
     */
    public function getAddressById(int $id): ?Address
    {
        return Address::findOne($id);
    }

    /**
     * @param Address $record
     * @param array $address
     * @return Address|null
     */
    private function _saveRecord(Address $record, array $address): ?Address
    {
        $countryName = $address['country'] ?? 'England';

        $country = Countries::find()
            ->where(['name' => $countryName])
            ->one();

        $record->countryId = $country->id ?? null;

        $record->address1 = SecurityHelper::encrypt($address['line1'] ?? '');
        $record->address2 = SecurityHelper::encrypt($address['line2'] ?? '');
        $record->address3 = SecurityHelper::encrypt($address['line3'] ?? '');
        $record->address4 = SecurityHelper::encrypt($address['line4'] ?? '');
        $record->zipCode = SecurityHelper::encrypt($address['postCode'] ?? '');

        $record->save();

        return $record;
    }

    public function parseAddress(array $address): array
    {
        $address['address1'] = SecurityHelper::decrypt($address['address1']);
        $address['address2'] = SecurityHelper::decrypt($address['address2']);
        $address['address3'] = SecurityHelper::decrypt($address['address3']);
        $address['address4'] = SecurityHelper::decrypt($address['address4']);
        $address['zipCode'] = SecurityHelper::decrypt($address['zipCode']);

        return $address;
    }
}
