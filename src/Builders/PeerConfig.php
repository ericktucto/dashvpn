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
        $lines[] = "# {$this->peer->getName()}";
        $lines[] = '[Interface]';
        $lines[] = "PrivateKey = {$this->server->getPrivateKey()}";
        $lines[] = "Address = {$this->peer->getAddress()}/32";
        if ($this->server->getDns()) {
            $lines[] = "DNS = {$this->server->getDns()}";
        }
        $lines[] = '[Peer]';
        $lines[] = "PublicKey = {$this->peer->getPublicKey()}";
        $lines[] = "PresharedKey = {$this->peer->getPresharedKey()}";
        $lines[] = "Endpoint = {$this->server->getIp()}";
        $lines[] = "AllowedIPs = 0.0.0.0/0, ::/0";
        $lines[] = "PersistentKeepalive = 25";
        return $lines;
    }
}
