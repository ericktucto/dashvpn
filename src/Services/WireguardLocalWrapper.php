<?php

namespace App\Services;

use App\Adapters\Wireguard\FileToPeer;
use App\Builders\PeerConfig;
use App\Builders\ServerConfig;
use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;
use Illuminate\Support\Str;

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

    public function generateKeys(
        string $target,
    ): array|false {
        $this->outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/{$target}.pub");
        $pub = $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.pub");

        $this->outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/{$target}.key");
        $key = $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.key");

        $this->outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/{$target}.psk");
        $psk = $this->outputFirstLine("cat {$this->prefix}/wireguard/wg0.psk");

        return [
            "publicKey" => $pub,
            "privateKey" => $key,
            "presharedKey" => $psk,
        ];
    }

    public function createServer(Server $server): bool
    {
        $builder = new ServerConfig($server, []);
        $lines = $builder->generate();

        file_put_contents("{$this->prefix}/wireguard/wg0.conf", $lines);

        return true;
    }

    public function addPeer(Server $server, Peer $peer): bool
    {
        $builder = new PeerConfig($server, $peer);
        $lines = $builder->generate();

        $slug = Str::slug($peer->getName());

        file_put_contents("{$this->prefix}/wireguard/peers/{$slug}.conf", $lines);

        exec("ls {$this->prefix}/wireguard/peers/*.conf", $files);
        $adapter = new FileToPeer();
        $peers = array_map(
            fn ($peer) => $adapter->parse($peer['keys'], $peer['data']),
            $this->getPeers(),
        );

        $builder = new ServerConfig($server, $peers);
        $lines = $builder->generate();

        file_put_contents("{$this->prefix}/wireguard/wg0.conf", $lines);

        return true;
    }

    public function generatePeersDirectory(): void
    {
        @mkdir("{$this->prefix}/wireguard/peers");
    }

    public function nextAddress(): Ip
    {
        exec("grep Address -Rna {$this->prefix}/wireguard/peers | awk '{print $3}'", $output);
        if (count($output) === 0) {
            exec("grep Address {$this->prefix}/wireguard/wg0.conf | awk '{print $3}'", $ip);
            if (count($ip) === 0) {
                throw new Exception('No address found');
            }
            return new Ip(
                preg_replace('/\d+\/\d+$/', '2', $ip[0])
            );
        }
        sort($output);
        $last = $output[count($output) - 1];
        $ip = preg_replace('/\/\d+$/', '', $last);

        return new Ip(
            long2ip(ip2long($ip) + 1),
        );
    }

    public function isPeerNameExists(string $slug): bool
    {
        return file_exists("{$this->prefix}/wireguard/peers/{$slug}.conf");
    }
}
