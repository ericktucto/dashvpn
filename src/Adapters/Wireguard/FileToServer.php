<?php

namespace App\Adapters\Wireguard;

use App\Domain\Wireguard\Server;
use App\Helper;
use Psr\Container\ContainerInterface;

class FileToServer
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected ContainerInterface $container,
    ) {
    }

    /**
     * @param string[] $keys
     * @param string[] $lines
     */
    public function parse(
        array $keys,
        array $lines,
    ): Server {
        $address = '';
        $listenPort = '';
        foreach ($lines as $line) {
            $property = Helper::getProperty($line, 'Address');
            if ($property) {
                $address = preg_replace('/\/24/', '', $property);
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
}
