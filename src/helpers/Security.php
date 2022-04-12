<?php

namespace percipiolondon\staff\helpers;

use Craft;
use GraphQL\Type\Definition\ResolveInfo;

class Security
{
    public static function encrypt(string|null $data): string|null
    {
        if ($data === '' || $data === null) {
            return $data;
        }

        return utf8_encode(Craft::$app->getSecurity()->encryptByKey($data), );
    }

    public static function decrypt(string|null $data, $type = 'string'): string|float|int|bool|null
    {
        if ($data === '' || $data === null) {
            return $data;
        }

        $data = Craft::$app->getSecurity()->decryptByKey(utf8_decode($data));

        return match ($type) {
            'int' => (int) $data,
            default => (string) $data,
        };
    }

    public static function resolve($source, ResolveInfo $resolveInfo, $type = 'string'): string|null
    {
        $fieldName = $resolveInfo->fieldName;
        $value = self::decrypt($source[$fieldName] ?? '', $type = 'string');
        return empty($value) ?
             null : $value;
    }
}
