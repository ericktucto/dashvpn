<?php

namespace App\Services;

use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;
use Exception;

final class WireguardService
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected WireguardWrapper $wrapper,
    ) {
    }

    public function server(): Server
    {
        return $this->wrapper->getServer() ?? throw new Exception('Server not found');
    }

    /**
     * @return Peer[]
     */
    public function peers(): array
    {
        $peers = $this->wrapper->getPeers();
        return is_array($peers) ? $peers : [];
    }

    public function createServer(
        Ip $address,
        int $listenPort,
    ): Server {
        return $this->wrapper->createServer(
            $address,
            $listenPort,
        );
    }

    public function createPeer(
        string $name,
    ): Peer {
        $peer = $this->wrapper->createPeer(
            $name,
        );

        if (!$peer) {
            throw new Exception('Cant create peer');
        }

        return $peer;
    }

    public function updatePeer(
        string $publicKey,
        string $name,
        Ip $address,
    ): Peer {
        return $this->wrapper->updatePeer($publicKey, $name, $address);
    }
}
