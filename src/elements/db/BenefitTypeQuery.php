<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * Class EmployeeQuery
 *
 * @package percipiolondon\staff\elements\db
 */
class BenefitTypeQuery extends ElementQuery
{
    /**
     * @return bool
     */
    protected function beforePrepare(): bool
    {

        return parent::beforePrepare();
    }
}
