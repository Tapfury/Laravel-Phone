<?php namespace Propaganistas\LaravelPhone\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use libphonenumber\PhoneNumberType;
use ReflectionClass;

trait ParsesTypes
{
    /**
     * Array of available phone types.
     *
     * @var array
     */
    protected static $types;

    /**
     * Determine whether the given type is valid.
     *
     * @param string $type
     * @return bool
     */
    public static function isValidType($type)
    {
        return ! is_null(static::parseTypes($type));
    }

    /**
     * Parse a phone type.
     *
     * @param string|array $types
     * @return array
     */
    protected static function parseTypes($types)
    {
        static::loadTypes();

        return Collection::make(is_array($types) ? $types : func_get_args())
                         ->map(function ($type) {
                             // If the type equals a constant's value, just return it.
                             if (in_array($type, static::$types, true)) {
                                 return $type;
                             }

                             // Otherwise we'll assume the type is the constant's name.
                             return Arr::get(static::$types, strtoupper($type));
                         })
                         ->reject(function ($value) {
                             return is_null($value) || $value === false;
                         })->toArray();
    }

    /**
     * Parse a phone type into its string representation.
     *
     * @param string|array $types
     * @return array
     */
    protected static function parseTypesAsStrings($types)
    {
        static::loadTypes();

        return Collection::make(is_array($types) ? $types : func_get_args())
                         ->map(function ($type) {
                             $type = strtoupper($type);

                             // If the type equals a constant's name, just return it.
                             if (isset(static::$types[$type])) {
                                 return $type;
                             }

                             // Otherwise we'll assume the type is the constant's value.
                             return array_search($type, static::$types);
                         })
                         ->reject(function ($value) {
                             return is_null($value) || $value === false;
                         })->toArray();
    }

    /**
     * Load all available formats once.
     */
    private static function loadTypes()
    {
        if (! static::$types) {
            static::$types = with(new ReflectionClass(PhoneNumberType::class))->getConstants();
        }
    }
}