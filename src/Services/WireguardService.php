<?php

namespace App\Services;

use App\Domain\Wireguard\Server;
use App\Helper;
use Exception;
use Psr\Container\ContainerInterface;

class WireguardService implements WireguardServiceInterface
{
    public function __construct(
        protected ContainerInterface $container,
        protected WireguardWrapperInterface $wrapper,
    ) {
    }

    public function server(): Server
    {
        $keys = $this->wrapper->getServerKeys();
        if (!$keys) {
            throw new Exception('No keys');
        }

        $server = $this->wrapper->getServer();
        if (!$server) {
            throw new Exception('No initizated server config');
        }

        $address = '';
        $listenPort = '';
        foreach ($server as $line) {
            $property = Helper::getProperty($line, 'Address');
            if ($property) {
                $address = $property;
            }

            $property = Helper::getProperty($line, 'ListenPort');
            if ($property) {
                $listenPort = $property;
            }
            if (str_contains($line, '[Peer]')) {
                break;
            }
        }

        $server = new Server(
            $address,
            $this->container->get('config')->get('data')['ip'],
            $listenPort,
            $this->container->get('config')->get('data')['dns'],
        );
        $server->setKeys(
            $keys['publicKey'],
            $keys['privateKey'],
            $keys['presharedKey'],
        );
        return $server;
    }

    public function peers(): array
    {
        return [];
    }
}
