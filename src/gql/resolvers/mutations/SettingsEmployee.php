<?php

namespace percipiolondon\staff\gql\resolvers\mutations;

use Craft;
use craft\gql\base\ElementMutationResolver;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\SettingsEmployee as SettingsEmployeeElement;
use percipiolondon\staff\records\Settings;
use percipiolondon\staff\Staff;

class SettingsEmployee extends ElementMutationResolver
{
    protected $immutableAttributes = ['id', 'uid'];

    public function setSettingsEmployee($source, array $arguments, $context, ResolveInfo $resolveInfo): array
    {
        return Staff::$plugin->staffSettings->setSettingsEmployee($arguments['settings'], $arguments['employeeId']);
    }
}