<?php

namespace App\Services;

use App\Builders\PeerConfig;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;
use App\Helper;

trait HasPeerLocalManage
{
    protected string $prefix;

    public function removeFileOfPeer(Peer $peer): void
    {
        unlink("{$this->prefix}/wireguard/peers/{$peer->getSlug()}.conf");
        unlink("{$this->prefix}/wireguard/peers/{$peer->getSlug()}.pub");
        unlink("{$this->prefix}/wireguard/peers/{$peer->getSlug()}.key");
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
        Helper::outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/peers/{$target}.pub");
        $pub = Helper::outputFirstLine("cat {$this->prefix}/wireguard/peers/{$target}.pub");
        if ($pub === false) {
            return false;
        }

        Helper::outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/peers/{$target}.key");
        $key = Helper::outputFirstLine("cat {$this->prefix}/wireguard/peers/{$target}.key");
        if ($key === false) {
            return false;
        }

        $psk = Helper::outputFirstLine("cat {$this->prefix}/wireguard/wg0.psk");
        if ($psk === false) {
            return false;
        }

        return [
            "publicKey" => $pub,
            "privateKey" => $key,
            "presharedKey" => $psk,
        ];
    }

    public function setPeerFile(Peer $peer, Server $server): void
    {
        $builder = new PeerConfig($server, $peer);
        $lines = $builder->generate();
        file_put_contents("{$this->prefix}/wireguard/peers/{$peer->getSlug()}.conf", $lines);
    }
}
