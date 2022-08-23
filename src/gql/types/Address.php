<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\helpers\Security;

/**
 * Class Address
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Address
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'Address';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'address1' => [
                'name' => 'address1',
                'type' => Type::string(),
                'description' => 'Line 1 of the address type',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'address2' => [
                'name' => 'address2',
                'type' => Type::string(),
                'description' => 'Line 2 of the address type',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'address3' => [
                'name' => 'address3',
                'type' => Type::string(),
                'description' => 'Line 3 of the address type',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'address4' => [
                'name' => 'address4',
                'type' => Type::string(),
                'description' => 'Line 4 of the address type',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'zipCode' => [
                'name' => 'zipCode',
                'type' => Type::string(),
                'description' => 'Post code of the address type',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'country' => [
                'name' => 'country',
                'type' => Type::string(),
                'description' => 'Country of the address type',
            ],
        ];
    }
}
