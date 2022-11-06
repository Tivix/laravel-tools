<?php

namespace Kellton\Tools\Helpers;

use stdClass;

/**
 * Class Json handles helpers related to the manipulation of JSON.
 */
class Json
{
    /**
     * Returns the JSON representation of a value.
     *
     * @param mixed $value
     * @param int $flags
     * @param int $depth
     *
     * @return string
     */
    public static function encode(mixed $value, int $flags = JSON_THROW_ON_ERROR, int $depth = 512): string
    {
        return json_encode($value, $flags, $depth);
    }

    /**
     * Returns the value represented by a JSON string.
     *
     * @param string $json
     * @param bool|null $associative
     * @param int $depth
     * @param int $flags
     *
     * @return array|stdClass
     */
    public static function decode(
        string $json,
        ?bool $associative = true,
        int $depth = 512,
        int $flags = JSON_THROW_ON_ERROR
    ): array|stdClass {
        if ($json === '') {
            $json = '[]';
        }

        return json_decode($json, $associative, $depth, $flags);
    }
}
