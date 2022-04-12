<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

use percipiolondon\staff\elements\Employer as EmployerElement;
use percipiolondon\staff\gql\types\generators\EmployerGenerator;
use percipiolondon\staff\helpers\Security as SecurityHelper;

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
    public static function getTypeGenerator(): string
    {
        return EmployerGenerator::class;
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
            'resolveType' => function(EmployerElement $value) {
                return $value->getGqlTypeName();
            },
        ]));

        EmployerGenerator::generateTypes();

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
        $parentFields = parent::getFieldDefinitions();
        unset($parentFields['slug']);

        $securedFields = [
            'crn' => [
                'name' => 'crn',
                'type' => Type::id(),
                'description' => 'The company registration number.',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo);
                },
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The company name.',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo);
                },
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::nonNull(Type::string()),
                'description' => 'The company slug.',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo);
                },
            ],
        ];

        $fields = [
            'address' => [
              'name' => 'address',
              'type' => Type::string(),
            ],
            'addressId' => [
                'name' => 'addressId',
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
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::nonNull(Type::id()),
                'description' => 'The employer id from staffology, needed for API calls.',
            ],
            'startYear' => [
                'name' => 'startYear',
                'type' => Type::string(),
            ],
            'logoUrl' => [
                'name' => 'logoUrl',
                'type' => Type::string(),
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo);
                },
            ],
            'currentPayRun' => [
                'name' => 'currentPayRun',
                'type' => PayRun::getType(),
                'description' => 'Current open pay run',
            ],
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $securedFields, $fields), self::getName());
    }
}
