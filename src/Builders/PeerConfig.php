<?php

namespace App\Builders;

use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;
use Override;

final class PeerConfig implements ConfigInterface
{
    public function __construct(
        protected Server $server,
        protected Peer $peer,
    ) {
    }

    #[Override]
    /**
     * @return list<string>
     */
    public function generate(): array
    {
        $lines = [];
        $lines[] = "# {$this->peer->getName()}\n";
        $lines[] = "[Interface]\n";
        $lines[] = "PrivateKey = {$this->peer->getPrivateKey()}\n";
        $lines[] = "Address = {$this->peer->getAddress()}/32\n";
        if ($this->server->getDns()) {
            $lines[] = "DNS = {$this->server->getDns()}\n";
        }
        $lines[] = "[Peer]\n";
        $lines[] = "PublicKey = {$this->server->getPublicKey()}\n";
        $lines[] = "PresharedKey = {$this->peer->getPresharedKey()}\n";
        $lines[] = "Endpoint = {$this->server->getIp()}:{$this->server->getListenPort()}\n";
        $lines[] = "AllowedIPs = {$this->peer->getAllowedIpsParsed()}\n";
        $lines[] = "PersistentKeepalive = 25\n";
        return $lines;
    }
}
