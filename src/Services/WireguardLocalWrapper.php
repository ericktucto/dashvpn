<?php

namespace App\Services;

use App\Domain\Wireguard\Server;

class WireguardLocalWrapper implements WireguardWrapperInterface
{
    public function __construct(
        protected string $prefix,
    ) {
    }

    public function getServer(): array|false
    {
        $result = exec("cat {$this->prefix}/wireguard/wg0.conf", $output);
        if (!$result) {
            return false;
        }
        return $output;
    }

    public function getServerKeys(): array|false
    {
        $keys = [
            'publicKey' => '',
            'privateKey' => '',
            'presharedKey' => '',
        ];
        $keys['publicKey'] = $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.pub");
        if (!$keys['publicKey']) {
            return false;
        }

        $keys['privateKey'] = $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.key");
        if (!$keys['privateKey']) {
            return false;
        }

        $psk = $this->getPsk();
        if (!$psk) {
            return false;
        }
        $keys['presharedKey'] = $psk;

        return $keys;
    }

    public function getPeers(): array|false
    {
        $result = exec("ls {$this->prefix}/wireguard/peers/*.conf", $output);
        if (!$result) {
            return false;
        }

        $psk = $this->getPsk();
        if (!$psk) {
            return false;
        }

        $peers = [];

        foreach ($output as $peer) {
            $data = null;
            exec("cat $peer", $data);

            // keys
            $keys = [
                'presharedKey' => $psk,
            ];

            $pub = preg_replace('/.conf$/', '.pub', $peer);
            $keys['publicKey'] = $this->outputFirstLine("cat $pub");

            $prv = preg_replace('/.conf$/', '.key', $peer);
            $keys['privateKey'] = $this->outputFirstLine("cat $prv");

            $peers[] = compact('data', 'keys');
        }

        return $peers;
    }

    public function getPsk(): string|false
    {
        return $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.psk");
    }

    protected function outputFirstLine(string $command): string|false
    {
        $result = exec($command, $output);
        if (!$result) {
            return false;
        }
        return $output[0];
    }

    public function generateKeys(): array|false
    {
        $this->outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/wg0.pub");
        $pub = $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.pub");

        $this->outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/wg0.key");
        $key = $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.key");

        $this->outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/wg0.psk");
        $psk = $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.psk");

        return [
            "publicKey" => $pub,
            "privateKey" => $key,
            "presharedKey" => $psk,
        ];
    }

    public function createServer(Server $server): bool
    {
        $lines = [];
        $lines[] = "[Interface]\n";
        $lines[] = "Address = {$server->getAddress()}/24\n";
        $lines[] = "ListenPort = {$server->getListenPort()}\n";
        $lines[] = "PrivateKey = {$server->getPrivateKey()}\n";

        file_put_contents("{$this->prefix}/wireguard/wg0.conf", $lines);

        return true;
    }
}
