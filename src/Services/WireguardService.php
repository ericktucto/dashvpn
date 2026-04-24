<?php

namespace App\Services;

use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;
use Exception;
use App\Models\Server as AppServer;
use Illuminate\Database\ConnectionResolverInterface;

final class WireguardService
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected WireguardWrapper $wrapper,
        ConnectionResolverInterface $resolver,
    ) {
        AppServer::setConnectionResolver($resolver);
    }

    public function server(): ?Server
    {
        return $this->wrapper->getServer();
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
        Ip $ip,
        int $listenPort,
        Ip $address,
        Ip $dns,
    ): Server {
        /** @var ?AppServer */
        $server = AppServer::query()->first();
        if ($server === null) {
            $server = new AppServer();
            $server->ip = $ip->getValue();
            $server->listenPort = $listenPort;
            $server->address = $address->getValue();
            $server->dns = $dns->getValue();
            $server->save();
        }

        return $this->wrapper->createServer($server);
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
        string $searchBySlug,
        string $name,
        Ip $address,
    ): Peer {
        return $this->wrapper->updatePeer($searchBySlug, $name, $address);
    }
}
