<?php

namespace percipiolondon\staff\helpers;

use Craft;

class Security
{
    public static function encrypt(string $data): string
    {
        return utf8_encode(Craft::$app->getSecurity()->encryptByKey($data ?? null));
    }

    public static function decrypt(string $data): string
    {
        return Craft::$app->getSecurity()->decryptByKey(utf8_decode($data ?? null));
    }
}

