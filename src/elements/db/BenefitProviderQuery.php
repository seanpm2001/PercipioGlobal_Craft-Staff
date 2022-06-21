<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * Class EmployeeQuery
 *
 * @package percipiolondon\staff\elements\db
 */
class BenefitProviderQuery extends ElementQuery
{
    /**
     * @var
     */
    public $providerId;

    /**
     * @param $value
     * @return $this
     */
    public function providerId($value)
    {
        $this->providerId = $value;
        return $this;
    }

    /**
     * @param string|string[]|null $value
     * @return $this|ElementQuery|EmployeeQuery
     */
    public function status($value): EmployeeQuery|ElementQuery|static
    {
        $this->status = $value;
        return $this;
    }


    /**
     * @return bool
     */
    protected function beforePrepare(): bool
    {
        return parent::beforePrepare();
    }
}
