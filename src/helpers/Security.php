<?php

namespace percipiolondon\staff\helpers;

use Craft;
use GraphQL\Type\Definition\ResolveInfo;

class Security
{
    public static function encrypt(string|null $data): string
    {
        if($data === '' || $data === null){
            return $data;
        }

        return utf8_encode(Craft::$app->getSecurity()->encryptByKey($data));
    }

    public static function decrypt(string|null $data): string
    {
        if($data === '' || $data === null){
            return $data;
        }

        return Craft::$app->getSecurity()->decryptByKey(utf8_decode($data));
    }

    public static function resolve($source, ResolveInfo $resolveInfo): string|null
    {
        $fieldName = $resolveInfo->fieldName;
        $value = self::decrypt($source[$fieldName] ?? '');
        return empty($value) ?
             null : $value;
    }
}

