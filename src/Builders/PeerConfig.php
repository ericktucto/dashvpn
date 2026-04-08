<?php

namespace App\Builders;

use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;

class PeerConfig implements ConfigInterface
{
    public function __construct(
        protected Server $server,
        protected Peer $peer,
    ) {
    }

    public function generate(): array
    {
        $lines = [];
        $lines[] = "# {$this->peer->getName()}\n";
        $lines[] = '[Interface]';
        $lines[] = "PrivateKey = {$this->server->getPrivateKey()}\n";
        $lines[] = "Address = {$this->peer->getAddress()}/32\n";
        if ($this->server->getDns()) {
            $lines[] = "DNS = {$this->server->getDns()}\n";
        }
        $lines[] = '[Peer]';
        $lines[] = "PublicKey = {$this->peer->getPublicKey()}\n";
        $lines[] = "PresharedKey = {$this->peer->getPresharedKey()}\n";
        $lines[] = "Endpoint = {$this->server->getIp()}\n";
        $lines[] = "AllowedIPs = 0.0.0.0/0, ::/0\n";
        $lines[] = "PersistentKeepalive = 25\n";
        return $lines;
    }
}
