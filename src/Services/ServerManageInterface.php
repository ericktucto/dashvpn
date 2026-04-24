<?php

namespace App\Services;

use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Server;

interface ServerManageInterface
{
    public function createServer(
        Ip $ip,
        int $listenPort,
        Ip $address,
        Ip $dns,
    ): Server;


    /**
     * @param \App\Domain\Wireguard\Peer[] $peers
     */
    public function reloadFileConfig(Server $server, array $peers): void;

    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function generateKeys(): array|false;

    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function getServerKeys(): array|false;
}
