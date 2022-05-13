<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\Employer as EmployerElement;
use percipiolondon\staff\gql\interfaces\elements\Employer as EmployerInterface;

/**
 * Class Employer
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Employer extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            EmployerInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var EmployerElement $source */
        $fieldName = $resolveInfo->fieldName;

        return match ($fieldName) {
            'address' => Json::decodeIfJson($source->address),
            'hmrcDetails' => Json::decodeIfJson($source->hmrcDetails),
            'defaultPayOptions' => Json::decodeIfJson($source->defaultPayOptions),
            default => parent::resolve($source, $arguments, $context, $resolveInfo),
        };
    }
}
