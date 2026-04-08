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
        $lines[] = "[Interface]";
        $lines[] = "Address = {$this->server->getAddress()}/24";
        $lines[] = "ListenPort = {$this->server->getListenPort()}";
        $lines[] = "PrivateKey = {$this->server->getPrivateKey()}";
        foreach ($this->peers as $peer) {
            $lines[] = "# {$peer->getName()}";
            $lines[] = "[Peer]";
            $lines[] = "PublicKey = {$peer->getPublicKey()}";
            $lines[] = "PresharedKey = {$peer->getPresharedKey()}";
            $lines[] = "AllowedIPs = {$peer->getAddress()}/32";
            $lines[] = "PersistentKeepalive = 25";
        }
    }
}
