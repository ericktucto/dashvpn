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

    /**
     * @param list<string> $postUp
     * @param list<string> $postDown
     */
    public function createServer(
        Ip $ip,
        int $listenPort,
        Ip $address,
        Ip $dns,
        array $postUp,
        array $postDown,
        string $interface,
    ): Server {
        /** @var AppServer */
        $server = AppServer::query()->firstOrNew();
        $server->ip = $ip->getValue();
        $server->listenPort = $listenPort;
        $server->address = $address->getValue();
        $server->dns = $dns->getValue();
        $server->interface = $interface;
        $server->postUp = join('; ', $postUp);
        $server->postDown = join('; ', $postDown);
        $server->save();

        return $this->wrapper->createServer($server);
    }

    /**
     * @param list<string> $allowedIps
     */
    public function createPeer(
        string $name,
        array $allowedIps = [],
    ): Peer {
        $peer = $this->wrapper->createPeer(
            $name,
            $allowedIps,
        );

        if (!$peer) {
            throw new Exception('Cant create peer');
        }

        return $peer;
    }

    /**
     * @param list<string> $allowedIps
     */
    public function updatePeer(
        string $searchBySlug,
        string $name,
        Ip $address,
        array $allowedIps = [],
    ): Peer {
        return $this->wrapper->updatePeer($searchBySlug, $name, $address, $allowedIps);
    }
}
