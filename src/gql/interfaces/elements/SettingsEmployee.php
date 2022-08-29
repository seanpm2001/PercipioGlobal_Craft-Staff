<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\elements\SettingsEmployee as SettingsEmployeeElement;
use percipiolondon\staff\gql\types\generators\SettingsEmployeeGenerator;

class SettingsEmployee extends Element
{
    public static function getTypeGenerator(): string
    {
        return SettingsEmployeeGenerator::class;
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
            'description' => 'This is the interface implemented by all settings of an employee.',
            'resolveType' => function(SettingsEmployeeElement $value) {
                return $value->getGqlTypeName();
            },
        ]));

        SettingsEmployeeGenerator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'SettingsEmployeeInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();

        $fields = [
            'settingsId' => [
                'name' => 'settingsId',
                'type' => Type::int(),
                'description' => 'Settings id',
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::int(),
                'description' => 'Employee id',
            ],
            'setting' => [
                'name' => 'setting',
                'type' => Type::string(),
                'description' => 'Setting name',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    //pass the translation 'en' (hardcoded, can be an argument in the future)
                    return \Craft::t('staff-management', $source->setting, 'en');
                }
            ],
//            'settings' => [
//                'name' => 'settings',
//                'type' => Type::listOf(Type::string()),
//                'description' => 'Get all available settings',
//                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
//                    //pass the translation 'en' (hardcoded, can be an argument in the future)
//                    $settings = [];
//
//                    foreach($source->settings as $setting) {
//                        $settings[] = \Craft::t('staff-management', $setting->name, 'en');
//                    }
//
//                    return $settings;
//                }
//            ]
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $fields), self::getName());
    }
}