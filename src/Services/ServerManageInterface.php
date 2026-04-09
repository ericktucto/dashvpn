<?php

namespace App\Services;

use App\Domain\Wireguard\Server;

interface ServerManageInterface
{
    /**
     * @param \App\Domain\Wireguard\Peer[] $peers
     */
    public function reloadFileConfig(Server $server, array $peers): void;
}
