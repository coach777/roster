<?php

namespace App\Enums;

enum ActivityType: string
{
    case CHECK_IN = 'CI';
    case CHECK_OUT = 'CO';
    case FLIGHT = 'FLT';
    case DAY_OFF = 'DO';
    case STAND_BY = 'SBY';
    case UNKNOWN = 'UNK';
    //CAR - pilot is driving to/from airport by car

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
