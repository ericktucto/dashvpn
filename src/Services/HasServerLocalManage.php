<?php

namespace App\Services;

use App\Builders\ServerConfig;
use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Server;
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
        if (!$keys) {
            throw new Exception('No keys');
        }
        return $this->adapterServer->parse($keys, $output);
    }

    public function nextAllowAddress(): Ip
    {
        $server = $this->getServer();
        if (!$server) {
            throw new Exception('Server not found');
        }

        /** @var list<string> $output */
        $output = [];
        exec("cat {$this->prefix}/wireguard/wg0.conf | grep AllowedIPs | awk '{print $3}'", $output);

        if (!is_array($output) || count($output) === 0) {
            $number = ip2long($server->getAddress());
            if ($number === false) {
                throw new Exception('Invalid ip');
            }
            return new Ip(
                long2ip($number + 1)
            );
        }

        sort($output);
        for ($i = 0; $i < count($output); $i++) {
            $ip = preg_replace("/\/32/", "", $output[$i]);
            $number = (int) preg_replace("/^[0-9]+\.[0-9]+.[0-9]+.([0-9]+)$/", "$1", $ip);
            if ($number !== ($i + 2)) {
                return new Ip(
                    preg_replace("/([0-9]+)$/", $i + 2, $ip),
                );
            }
        }
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
