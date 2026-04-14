<?php

namespace App\Services;

use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;

interface WireguardWrapperInterface
{
    public function getServer(): ?Server;

    public function createServer(
        Ip $address,
        int $listenPort,
    ): Server;

    /**
     * @return list<Peer>|false
     */
    public function getPeers(): array|false;

    public function createPeer(string $name): ?Peer;

    public function updatePeer(string $slug, string $name, Ip $address): Peer;

    public function deletePeer(string $slug): void;

    public function nextAllowAddress(): Ip;

    public function getConfigPeer(string $slug): string|false;
}
