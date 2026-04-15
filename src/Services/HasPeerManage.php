<?php

namespace App\Services;

use App\Builders\PeerConfig;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;
use App\Helper;
use App\Adapters\Wireguard\FileToPeer;
use Exception;

trait HasPeerManage
{
    protected string $prefix;

    public function removeFileOfPeer(Peer $peer): void
    {
        unlink("{$this->prefix}/peers/{$peer->getSlug()}.conf");
        unlink("{$this->prefix}/peers/{$peer->getSlug()}.pub");
        unlink("{$this->prefix}/peers/{$peer->getSlug()}.key");
    }

    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function generateKeysPeers(string $target): array|false
    {
        // public and private keys
        $target = "{$this->prefix}/peers/{$target}";
        Helper::outputFirstLine("wg genkey | tee {$target}.key | wg pubkey > {$target}.pub");

        $pub = file_get_contents("{$target}.pub");
        if ($pub === false) {
            return false;
        }

        $key = file_get_contents("{$target}.key");
        if ($key === false) {
            return false;
        }

        $psk = file_get_contents("{$this->prefix}/wg0.key");
        if ($psk === false) {
            return false;
        }

        return [
            "publicKey" => $pub,
            "privateKey" => $key,
            "presharedKey" => $psk,
        ];
    }

    /**
     * @return list<Peer>|false
     */
    public function getPeers(): array|false
    {
        $result = (bool) exec("ls {$this->prefix}/peers/*.conf", $output);
        if (!$result || !$output) {
            return false;
        }

        $psk = file_get_contents("{$this->prefix}/wg0.key");
        if ($psk === false) {
            return false;
        }

        $peers = [];

        $adapter = new FileToPeer();
        /** @var string[] $output */
        foreach ($output as $peer) {
            $data = explode("\n", (string) file_get_contents($peer));

            // keys
            $keys = [
                'presharedKey' => $psk,
            ];

            $pub = preg_replace('/.conf$/', '.pub', $peer);
            if ($pub === null) {
                throw new Exception('Cant get public key');
            }
            $keys['publicKey'] = (string) file_get_contents($pub);

            $prv = preg_replace('/.conf$/', '.key', $peer);
            if ($prv === null) {
                throw new Exception('Cant get private key');
            }
            $keys['privateKey'] = (string) file_get_contents($prv);

            $peers[] = $adapter->parse($keys, $data);
        }

        return $peers;
    }

    public function setPeerFile(Peer $peer, Server $server): void
    {
        $builder = new PeerConfig($server, $peer);
        $lines = $builder->generate();
        file_put_contents("{$this->prefix}/peers/{$peer->getSlug()}.conf", $lines);
    }

    protected function generatePeersDirectory(): void
    {
        @mkdir("{$this->prefix}/peers");
    }
}
