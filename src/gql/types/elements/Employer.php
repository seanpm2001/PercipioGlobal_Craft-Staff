<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace percipiolondon\craftstaff\gql\types\elements;

use craft\gql\types\elements\Element;
use percipiolondon\craftstaff\elements\Employer as EmployerElement;
use percipiolondon\craftstaff\gql\interfaces\elements\Employer as EmployerInterface;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Class Entry
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.3.0
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

        switch($fieldName) {
            case 'name':
                return $source->name;
            case 'crn':
                return $source->crn;
        }

        return parent::resolve($source, $arguments, $context, $resolveInfo);
    }
}