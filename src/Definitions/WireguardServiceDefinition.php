<?php

namespace App\Definitions;

use App\Services\WireguardWrapper;
use App\Services\WireguardWrapperInterface;

class WireguardServiceDefinition
{
    public static function create(): WireguardWrapperInterface
    {
        return new WireguardWrapper();
    }
}
