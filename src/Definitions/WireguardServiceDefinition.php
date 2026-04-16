<?php

namespace App\Definitions;

use App\Adapters\Wireguard\FileToServer;
use App\Services\ServerLocalManage;
use App\Services\ServerManageInterface;
use App\Services\ServerWGManage;
use Psr\Container\ContainerInterface;

final class WireguardServiceDefinition
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function create(
        ContainerInterface $container,
        FileToServer $adapterServer,
    ): ServerManageInterface {
        if ($container->get('config')->get('env') === 'production') {
            return new ServerWGManage(
                $adapterServer,
                $container,
            );
        }
        return new ServerLocalManage(
            $adapterServer,
            $container,
        );
    }
}
