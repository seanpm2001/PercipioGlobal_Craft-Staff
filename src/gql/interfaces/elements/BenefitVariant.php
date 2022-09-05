<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\elements\BenefitVariant as CraftElement;
use percipiolondon\staff\gql\types\generators\BenefitVariantGenerator as Generator;
use percipiolondon\staff\gql\types\TotalRewardsStatement;
use percipiolondon\staff\gql\types\Policy;

class BenefitVariant extends Element
{
    public static function getTypeGenerator(): string
    {
        return Generator::class;
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
            'description' => 'This is the interface implemented by all benefit variants.',
            'resolveType' => function(CraftElement $value) {
                return $value->getGqlTypeName();
            },
        ]));

        Generator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'BenefitVariantInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();

        $fields = [
            'trsId' => [
                'name' => 'trsId',
                'type' => Type::int(),
                'description' => 'Total Rewards Statement id',
            ],
            'policyId' => [
                'name' => 'policyId',
                'type' => Type::int(),
                'description' => 'Policy id',
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'Get the admin name that handled the request',
            ],
            'totalRewardsStatement' => [
                'name' => 'totalRewardsStatement',
                'type' => TotalRewardsStatement::getType(),
                'description' => 'The total rewards statement'
            ],
            'policy' => [
                'name' => 'policy',
                'type' => Policy::getType(),
                'description' => 'Get policy information'
            ]
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $fields), self::getName());
    }
}