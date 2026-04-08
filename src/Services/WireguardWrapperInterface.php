<?php

namespace App\Services;

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
    public function generateKeys(): array|false;

    public function createServer(Server $server): bool;
}
