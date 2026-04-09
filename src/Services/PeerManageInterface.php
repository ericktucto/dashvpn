<?php

namespace App\Services;

use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;

interface PeerManageInterface
{
    public function removeFileOfPeer(Peer $peer): void;

    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function generateKeysPeers(string $target): array|false;

    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function getKeysPeers(string $target): array|false;

    public function setPeerFile(Peer $peer, Server $server): void;
}
