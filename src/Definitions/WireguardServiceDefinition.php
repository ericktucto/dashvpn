<?php

namespace App\Definitions;

use App\Services\WireguardLocalWrapper;
use App\Services\WireguardWrapper;
use App\Services\WireguardWrapperInterface;
use Psr\Container\ContainerInterface;

class WireguardServiceDefinition
{
    public static function create(
        ContainerInterface $container,
    ): WireguardWrapperInterface {
        if ($container->get('config')->get('env') === 'production') {
            return new WireguardWrapper();
        }
        return new WireguardLocalWrapper(
            $container->get('path'),
        );
    }
}
