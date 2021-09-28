<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace percipiolondon\craftstaff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;

use percipiolondon\craftstaff\gql\types\generators\EmployerType;
use percipiolondon\craftstaff\gql\types\Address;
use percipiolondon\craftstaff\gql\types\HmrcDetails;
use percipiolondon\craftstaff\gql\types\PayOptions;

use craft\helpers\Gql;
use craft\helpers\Json;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * Class Asset
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.3.0
 */
class Employer extends Element
{
    /**
     * @inheritdoc
     */
    public static function getTypeGenerator(): string
    {
        return EmployerType::class;
    }

    /**
     * @inheritdoc
     */
    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all employers.',
            'resolveType' => self::class . '::resolveElementTypeName',
        ]));

        EmployerType::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'EmployerInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        return TypeManager::prepareFieldDefinitions(array_merge(parent::getFieldDefinitions(), self::getConditionalFields(), [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The company name.',
            ],
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::string(),
                'description' => 'The employer id from staffology, needed for API calls.'
            ],
            'crn' => [
                'name' => 'crn',
                'type' => Type::string(),
                'description' => 'The company registration number.',
            ],
            'address' => [
                'name' => 'address',
                'type' => Address::getType(),
                'description' => 'The address object.',
            ],
            'hmrcDetails' => [
                'name' => 'hmrcDetails',
                'type' => HmrcDetails::getType(),
                'description' => 'Get the HMRC Details.',
            ],
            'startYear' => [
                'name' => 'startYear',
                'type' => Type::string(),
            ],
            'currentYear' => [
                'name' => 'currentYear',
                'type' => Type::string(),
            ],
            'employeeCount' => [
                'name' => 'employeeCount',
                'type' => Type::int(),
            ],
            'defaultPayOptions' => [
                'name' => 'defaultPayOptions',
                'type' => PayOptions::getType(),
                'description' => 'Get the default pay options',
            ],

        ]), self::getName());
    }

    /**
     * @inheritdoc
     */
    protected static function getConditionalFields(): array
    {
        return [];
    }

}