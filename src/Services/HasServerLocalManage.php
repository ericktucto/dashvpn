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


    public function nextAllowAddress(): Ip
    {
        $server = $this->getServer();
        if (!$server) {
            throw new Exception('Server not found');
        }

        $output = [];
        exec("cat {$this->prefix}/wireguard/wg0.conf | grep AllowedIPs | awk '{print $3}'", $output);

        sort($output);
        for ($i = 0; $i < count($output); $i++) {
            $ip = preg_replace("/\/32/", "", $output[$i]);
            if (!is_string($ip)) {
                throw new Exception('Invalid ip');
            }

            $number = (int) preg_replace("/^[0-9]+\.[0-9]+.[0-9]+.([0-9]+)$/", "$1", $ip);
            if ($number !== ($i + 2)) {
                $newIp = preg_replace("/([0-9]+)$/", (string) ($i + 2), $ip);
                return is_string($newIp)
                    ? new Ip($newIp)
                    : throw new Exception('Invalid ip');
            }
        }
        $number = ip2long($server->getAddress());
        if ($number === false) {
            throw new Exception('Invalid ip');
        }
        return new Ip(
            long2ip($number + 1)
        );
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
