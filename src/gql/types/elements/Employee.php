<?php

namespace percipiolondon\craftstaff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\craftstaff\elements\Employee as EmployeeElement;
use percipiolondon\craftstaff\gql\interfaces\elements\Employee as EmployeeInterface;

/**
 * Class Employee
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Employee extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            EmployeeInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var EmployeeElement $source */
        $fieldName = $resolveInfo->fieldName;

        switch($fieldName) {
            case 'personalDetails':
                return Json::decodeIfJson($source->personalDetails);

            case 'employmentDetails':
                return Json::decodeIfJson($source->employmentDetails);

            case 'autoEnrolment':
                return Json::decodeIfJson($source->autoEnrolment);

            case 'rightToWork':
                return Json::decodeIfJson($source->rightToWork);

            case 'leaveSettings':
                return Json::decodeIfJson($source->leaveSettings);

            case 'bankDetails':
                return Json::decodeIfJson($source->bankDetails);

            case 'payOptions':
                return Json::decodeIfJson($source->payOptions);
        }

        return parent::resolve($source, $arguments, $context, $resolveInfo);
    }
}