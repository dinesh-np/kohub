<?php

namespace DP0\Kohub\Enums;

enum KOHUBEnvironment: string
{
    case STAGING = 'staging';
    case PRODUCTION = 'production';

    public static function toArray(): array
    {
        return [
            self::STAGING->value => self::STAGING->value,
            self::PRODUCTION->value => self::PRODUCTION->value,
        ];
    }
}