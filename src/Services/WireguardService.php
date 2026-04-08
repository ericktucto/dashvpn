<?php

namespace App\Services;

use App\Adapters\Wireguard\FileToPeer;
use App\Adapters\Wireguard\FileToServer;
use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Server;
use Exception;

class WireguardService implements WireguardServiceInterface
{
    public function __construct(
        protected FileToServer $adapterServer,
        protected FileToPeer $adapterPeer,
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

        return $this->adapterServer->parse($keys, $server);
    }

    public function peers(): array
    {
        $peers = $this->wrapper->getPeers();
        return array_map(
            fn ($peer) => $this->adapterPeer->parse($peer['keys'], $peer['data']),
            $peers
        );
    }

    public function createServer(
        Ip $ip,
        Ip $address,
        int $listenPort,
        Ip|null $dns,
    ): Server {
        $keys = $this->wrapper->generateKeys();

        $server = new Server(
            $address->getValue(),
            $ip->getValue(),
            $listenPort,
            $dns->getValue(),
        );

        $server->setKeys(
            $keys['publicKey'],
            $keys['privateKey'],
            $keys['presharedKey'],
        );

        if (!$this->wrapper->createServer($server)) {
            throw new Exception('Cant create server');
        }

        return $server;
    }
}
