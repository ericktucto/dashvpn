<?php

namespace App\Builders;

use App\Domain\Wireguard\Server;

class ServerConfig implements ConfigInterface
{
    public function __construct(
        protected Server $server,
        /**
         * @var \App\Domain\Wireguard\Peer[]
         */
        protected array $peers,
    ) {
    }

    public function generate(): array
    {
        $lines = [];
        $lines[] = "[Interface]\n";
        $lines[] = "Address = {$this->server->getAddress()}/24\n";
        $lines[] = "ListenPort = {$this->server->getListenPort()}\n";
        $lines[] = "PrivateKey = {$this->server->getPrivateKey()}\n";
        foreach ($this->peers as $peer) {
            $lines[] = "# {$peer->getName()}\n";
            $lines[] = "[Peer]\n";
            $lines[] = "PublicKey = {$peer->getPublicKey()}\n";
            $lines[] = "PresharedKey = {$peer->getPresharedKey()}\n";
            $lines[] = "AllowedIPs = {$peer->getAddress()}/32\n";
            $lines[] = "PersistentKeepalive = 25\n";
        }
        return $lines;
    }
}
