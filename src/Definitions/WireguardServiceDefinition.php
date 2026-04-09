<?php

namespace App\Definitions;

use App\Adapters\Wireguard\FileToPeer;
use App\Adapters\Wireguard\FileToServer;
use App\Services\WireguardLocalWrapper;
use App\Services\WireguardWrapper;
use App\Services\WireguardWrapperInterface;
use Psr\Container\ContainerInterface;

class WireguardServiceDefinition
{
    public static function create(
        ContainerInterface $container,
        FileToPeer $adapterPeer,
        FileToServer $adapterServer,
    ): WireguardWrapperInterface {
        if ($container->get('config')->get('env') === 'production') {
            return new WireguardWrapper();
        }
        return new WireguardLocalWrapper(
            $adapterServer,
            $adapterPeer,
            $container->get('path'),
        );
    }
}
