<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\Employee as EmployeeElement;
use percipiolondon\staff\gql\interfaces\elements\Employee as EmployeeInterface;

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

        return match ($fieldName) {
            'personalDetails' => Json::decodeIfJson($source->personalDetails),
            'employmentDetails' => Json::decodeIfJson($source->employmentDetails),
            'autoEnrolment' => Json::decodeIfJson($source->autoEnrolment),
            'rightToWork' => Json::decodeIfJson($source->rightToWork),
            'leaveSettings' => Json::decodeIfJson($source->leaveSettings),
            'bankDetails' => Json::decodeIfJson($source->bankDetails),
            'payOptions' => Json::decodeIfJson($source->payOptions),
            default => parent::resolve($source, $arguments, $context, $resolveInfo),
        };
    }
}
