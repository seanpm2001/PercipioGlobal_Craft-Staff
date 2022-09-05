<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use Craft;
use craft\db\Query;
use craft\gql\base\ElementResolver;

use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\BenefitVariant as Element;
use percipiolondon\staff\helpers\Gql as GqlHelper;
use percipiolondon\staff\records\BenefitEmployeeVariant;
use yii\base\BaseObject;

class BenefitVariant extends ElementResolver
{
    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if ($source === null) {
            $employeeId = $arguments['employeeId'] ?? null;

            if (is_null($employeeId)) {
                $query = Element::find();
            } else {
                $employees = (new Query())
                    ->from(Table::BENEFIT_EMPLOYEES_VARIANTS)
                    ->select('*')
                    ->where('employeeId = '.$employeeId)
                    ->all();

                $variantIds = [];
                foreach ($employees as $employee) {
                    $variantIds[] = $employee['variantId'];
                }

                $query = Element::findAll($variantIds);
            }
        } else {
            $query = $source->$fieldName;
        }

        if (is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        if (!GqlHelper::canQueryGroupBenefits()) {
            return [];
        }

        return $query;
    }
}
