<?php

namespace App\Services;

use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;

interface WireguardWrapperInterface
{
    /**
     * @return string[]|false
     */
    public function getServer(): array|false;

    /**
     * @return string[]|false
     */
    public function getServerKeys(): array|false;

    /**
     * @return array<string, array<string, string>>|false
     */
    public function getPeers(): array|false;

    public function getPsk(): string|false;

    /**
     * @return array<string, array<string, string>>|false
     */
    public function generateKeys(string $target): array|false;

    public function createServer(Server $server): bool;

    public function generatePeersDirectory(): void;

    public function addPeer(Server $server, Peer $peer): bool;

    public function nextAddress(): Ip;

    public function isPeerNameExists(string $slug): bool;
}
