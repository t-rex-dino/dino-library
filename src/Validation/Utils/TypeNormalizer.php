<?php

namespace Dino\Validation\Utils;

class TypeNormalizer
{
    public static function normalize(string $type): string
    {
        $map = [
            'int' => 'integer',
            'bool' => 'boolean',
            'float' => 'double',
            'string' => 'string',
            'array' => 'array',
            'object' => 'object',
            'null' => 'NULL',
        ];
        return $map[$type] ?? $type;
    }

    public static function denormalize(string $type): string
    {
        $map = [
            'integer' => 'int',
            'boolean' => 'bool',
            'double' => 'float',
            'string' => 'string',
            'array' => 'array',
            'object' => 'object',
            'NULL' => 'null',
        ];
        return $map[$type] ?? $type;
    }
}
