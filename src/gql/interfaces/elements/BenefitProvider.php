<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\elements\BenefitProvider as BenefitProviderElement;
use percipiolondon\staff\gql\types\generators\BenefitProviderGenerator;

/**
 * Class BenefitProvider
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class BenefitProvider extends Element
{
    /**
     * @inheritdoc
     */
    public static function getTypeGenerator(): string
    {
        return BenefitProviderGenerator::class;
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
            'description' => 'This is the interface implemented by all benefit providers.',
            'resolveType' => function(BenefitProviderElement $value) {
                return $value->getGqlTypeName();
            },
        ]));

        BenefitProviderGenerator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'EmployeeInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();

        $fields = [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The benefit provider.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'The benefit provider website.'
            ],
            'logo' => [
                'name' => 'logo',
                'type' => Type::id(),
                'description' => 'The ID of the logo asset.'
            ],
            'content' => [
                'name' => 'content',
                'type' => Type::string(),
                'description' => 'The provider description.'
            ],
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $fields), self::getName());
    }
}
