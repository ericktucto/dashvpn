<?php

namespace App\Services;

use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;

interface WireguardWrapperInterface
{
    public function getServer(): ?Server;

    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function getServerKeys(): array|false;

    /**
     * @return list<Peer>|false
     */
    public function getPeers(): array|false;

    public function getPsk(): string|false;

    public function createServer(
        Ip $ip,
        Ip $address,
        int $listenPort,
        Ip|null $dns,
    ): Server;

    public function createPeer(string $name): ?Peer;

    public function updatePeer(string $slug, string $name, Ip $address): Peer;

    public function deletePeer(string $slug): void;
}
