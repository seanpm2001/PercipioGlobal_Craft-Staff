<?php

namespace percipiolondon\staff\gql\base;

use Craft;
use craft\gql\base\ElementArguments;
use craft\gql\types\QueryArgument;
use GraphQL\Type\Definition\Type;

class HardingArguments extends ElementArguments
{
    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {

        $arguments = parent::getArguments();
        unset(
            $arguments['archived'],
            $arguments['draftCreator'],
            $arguments['draftId'],
            $arguments['draftOf'],
            $arguments['drafts'],
            $arguments['enabledForSite'],
            $arguments['fixedOrder'],
            $arguments['preferSites'],
            $arguments['provisionalDrafts'],
            $arguments['ref'],
            $arguments['relatedTo'],
            $arguments['relatedToAll'],
            $arguments['relatedToAssets'],
            $arguments['relatedToCategories'],
            $arguments['relatedToEntries'],
            $arguments['relatedToTags'],
            $arguments['relatedToUsers'],
            $arguments['revisionCreator'],
            $arguments['revisionId'],
            $arguments['revisionOf'],
            $arguments['revisions'],
            $arguments['search'],
            $arguments['site'],
            $arguments['siteId'],
            $arguments['siteSettingsId'],
            $arguments['status'],
            $arguments['title'],
            $arguments['trashed'],
            $arguments['uri'],
        );

        return $arguments;
    }
}