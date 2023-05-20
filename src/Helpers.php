<?php

namespace PainlessPHP\Package\Boilerplate\Core;

use Exception;

class Helpers
{
    public static function nestedArrayKeysExist(array $haystack, array $needles, string|null $nestingIndicator = null) : bool
    {
        foreach($needles as $needle) {

            $explodedNeedle = $nestingIndicator ? explode($nestingIndicator, $needle, 2) : [$needle];
            $topLevelAccessor = $explodedNeedle[0];

            if(! array_key_exists($topLevelAccessor, $haystack)) {
                return false;
            }

            if($topLevelAccessor === $needle) {
                continue;
            }

            if(! static::nestedArrayKeysExist($haystack[$topLevelAccessor], [$explodedNeedle[1]])) {
                return false;
            }
        }

        return true;
    }

    public static function arrayGetNested(array $haystack, string $needle, string|null $nestingIndicator = null) : mixed
    {
        $explodedNeedle = $nestingIndicator ? explode($nestingIndicator, $needle, 2) : [$needle];
        $topLevelAccessor = $explodedNeedle[0];

        if(! array_key_exists($topLevelAccessor, $haystack)) {
            throw new Exception("Array key '$needle' does not exist");
        }

        $haystack = $haystack[$topLevelAccessor];
        $needle = $explodedNeedle[1] ?? null;

        if($needle === null) {
            return $haystack;
        }

        return self::arrayGetNested($haystack, $needle, $nestingIndicator);
    }

    public static function arraySetNested(array $data, array $newData, string $needle, string|null $nestingIndicator = null)
    {
        $orignalNewData = $newData;
        $originalNeedle = $needle;
        $explodedNeedle = $nestingIndicator ? explode($nestingIndicator, $needle, 2) : [$needle];
        $topLevelAccessor = $explodedNeedle[0];

        if(! array_key_exists($topLevelAccessor, $data)) {
            throw new Exception("Array key '$needle' does not exist");
        }

        $newData = $data[$topLevelAccessor];
        $needle = $explodedNeedle[1] ?? null;

        if($needle === null) {
            return [...$data, $originalNeedle => $orignalNewData];
        }

        return self::arraySetNested($data, $newData, $needle, $nestingIndicator);
    }
}
