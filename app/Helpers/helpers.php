<?php

if (! function_exists('toKebabCase')) {
    function toKebabCase(string $value): string
    {
        return str_replace(' ', '-', strtolower($value));
    }
}
