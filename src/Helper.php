<?php

namespace App;

final class Helper
{
    public static function getProperty(string $line, string $property): ?string
    {
        if (str_contains($line, "$property = ")) {
            $exploded = explode("$property = ", $line);
            if (count($exploded) === 2) {
                return $exploded[1];
            }
        }
        return null;
    }

    public static function outputFirstLine(string $command): string|false
    {
        $output = [];
        exec($command, $output);
        /** @var list<string>|false $output */
        if ($output === false || count($output) === 0) {
            return false;
        }
        return $output[0];
    }
}
