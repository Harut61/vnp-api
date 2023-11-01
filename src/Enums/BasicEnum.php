<?php

namespace App\Enums;

use ReflectionClass;

/**
 * Class Abstract BasicEnum.
 */
abstract class BasicEnum
{
    /**
     * @var array
     */
    private static $constCacheArray = [];

    /**
     * @throws \ReflectionException
     */
    public static function getConstants()
    {
        $calledClass = \get_called_class();
        if (!\array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }
}
