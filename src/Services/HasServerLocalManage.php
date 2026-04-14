<?php

namespace App\Services;

use App\Builders\ServerConfig;
use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Server;
use App\Helper;
use Exception;

trait HasServerLocalManage
{
    public function getServer(): ?Server
    {
        $result = (bool) exec("cat {$this->prefix}/wireguard/wg0.conf", $output);
        if (!$result) {
            return null;
        }
        $keys = $this->getServerKeys();
        if ($keys === false) {
            throw new Exception('No keys');
        }
        return $this->adapterServer->parse($keys, $output);
    }

    protected function getServerKeys(): array|false
    {
        $keys = [
            'publicKey' => '',
            'privateKey' => '',
            'presharedKey' => '',
        ];
        $keys['publicKey'] = Helper::outputFirstLine("cat {$this->prefix}/wireguard/wg0.pub");
        if ($keys['publicKey'] === false) {
            return false;
        }

        $keys['privateKey'] = Helper::outputFirstLine("cat {$this->prefix}/wireguard/wg0.key");
        if ($keys['privateKey'] === false) {
            return false;
        }

        $psk = Helper::outputFirstLine("cat {$this->prefix}/wireguard/wg0.psk");
        if ($psk === false) {
            return false;
        }
        $keys['presharedKey'] = $psk;

        return $keys;
    }

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
