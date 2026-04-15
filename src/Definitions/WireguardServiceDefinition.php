<?php

namespace App\Definitions;

use App\Adapters\Wireguard\FileToServer;
use App\Services\WireguardLocalWrapper;
//use App\Services\WireguardWrapper;
use App\Services\WireguardWrapperInterface;
use Psr\Container\ContainerInterface;

final class WireguardServiceDefinition
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function create(
        ContainerInterface $container,
        FileToServer $adapterServer,
    ): WireguardWrapperInterface {
        /*
        if ($container->get('config')->get('env') === 'production') {
            return new WireguardWrapper();
        }
        */
        return new WireguardLocalWrapper(
            $adapterServer,
            $container->get('config')->get('data.config_dir'),
            $container,
        );
    }
}
