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
    public string|null $name = null;

    /**
     * @param $value
     * @return $this
     */
    public function name($value): BenefitProviderQuery
    {
        $this->name = $value;
        return $this;
    }

    /**
     * @return bool
     */
    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_benefit_providers');

        $this->query->select([
            'staff_benefit_providers.name',
            'staff_benefit_providers.url',
            'staff_benefit_providers.logo',
            'staff_benefit_providers.content',
        ]);

        if ($this->name) {
            $this->subQuery->andWhere(Db::parseParam('staff_benefit_providers.name', $this->name));
        }

        return parent::beforePrepare();
    }
}
