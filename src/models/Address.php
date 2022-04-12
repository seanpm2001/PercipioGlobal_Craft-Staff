<?php

namespace percipiolondon\staff\models;

use craft\base\model;

class Address extends Model
{
    /**
     * @var int|null ID
     */
    public $id;

    /**
     * @var int|null Section ID
     */
    public $countryId;

    /**
     * @var string|null Name
     */
    public $address1;

    /**
     * @var string|null Name
     */
    public $address2;

    /**
     * @var string|null Name
     */
    public $address3;

    /**
     * @var string|null Name
     */
    public $address4;

    /**
     * @var string|null Name
     */
    public $address5;

    /**
     * @var string|null Name
     */
    public $zipCode;

    /**
     * @var string UID
     */
    public $uid;
}
