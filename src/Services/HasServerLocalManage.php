<?php

namespace App\Services;

use App\Builders\ServerConfig;
use App\Domain\Wireguard\Server;

trait HasServerLocalManage
{
    /**
     * @param \App\Domain\Wireguard\Peer[] $peers
     */
    public function reloadFileConfig(Server $server, array $peers): void
    {
        $builder = new ServerConfig($server, $peers);
        $lines = $builder->generate();

        file_put_contents("{$this->prefix}/wireguard/wg0.conf", $lines);

        // @todo clear peers
    }
}
