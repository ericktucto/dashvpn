<?php

namespace App\Services;

use App\Domain\Wireguard\Server;

interface WireguardServiceInterface
{
    public function server(): Server;
    /**
     * @return \App\Domain\Wireguard\Peer[]
     */
    public function peers(): array;
}
