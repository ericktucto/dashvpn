<?php

namespace App;

class Helper
{
    public static function getProperty(string $line, string $property): ?string
    {
        if (str_contains($line, "$property = ")) {
            return (string) explode("$property = ", $line)[1];
        }
        return null;
    }
}
