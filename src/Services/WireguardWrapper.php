<?php

namespace App\Services;

use App\Adapters\Wireguard\FileToPeer;
use App\Adapters\Wireguard\FileToServer;
use App\Builders\PeerConfig;
use App\Builders\ServerConfig;
use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Peer;
use App\Domain\Wireguard\Server;
use App\Helper;
use DI\Attribute\Inject;
use Exception;
use Illuminate\Support\Str;
use Noodlehaus\Config;
use Psr\Container\ContainerInterface;

/**
 * @todo No es final por que se esta usando en los tests
 */
class WireguardWrapper implements PeerManageInterface
{
    use HasPeerManage;

    protected string $prefix;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected ContainerInterface $container,
        protected ServerManageInterface $manager,
    ) {
        $prefix = $container->get('config')->get('data.config_dir');
        if (!is_string($prefix)) {
            throw new Exception('Cant get config dir');
        }
        $this->prefix = $prefix;
    }

    public function getServer(): ?Server
    {
        return $this->manager->getServer();
    }

    public function createServer(Ip $address, int $listenPort): Server
    {
        return $this->manager->createServer($address, $listenPort);
    }

    public function getConfigPeer(string $slug): string|false
    {
        return file_get_contents("{$this->prefix}/peers/{$slug}.conf");
    }

    public function updatePeer(
        string $slug,
        string $name,
        Ip $address,
    ): Peer {
        $server = $this->getServer();
        if ($server === null) {
            throw new Exception('Server not found');
        }

        $peers = $this->getPeers();
        if ($peers === false) {
            throw new Exception('Cant get peers');
        }
        $target = null;
        $currentKey = 0;
        foreach ($peers as $key => $peer) {
            if ($peer->getAddress() === $address->getValue() && $peer->getSlug() !== $slug) {
                throw new Exception('Ip already used');
            }
            if ($peer->getSlug() === $slug) {
                $target = $peer;
                $currentKey = $key;
            }
        }
        if (!$target) {
            throw new Exception('Peer not found');
        }

        $newSlug = Str::slug($name);
        if ($target->getSlug() !== $newSlug) {
            file_put_contents("{$this->prefix}/peers/{$newSlug}.pub", $target->getPublicKey());
            file_put_contents("{$this->prefix}/peers/{$newSlug}.key", $target->getPrivateKey());
            $this->removeFileOfPeer($target);
        }

        $peer = new Peer(
            $name,
            $address->getValue(),
            $target->getPublicKey(),
            $target->getPrivateKey(),
            $target->getPresharedKey(),
        );

        $this->setPeerFile($peer, $server);

        $peers[$currentKey] = $peer;
        $this->manager->reloadFileConfig($server, $peers);

        return $peer;
    }

    public function deletePeer(string $slug): void
    {
        $server = $this->getServer();
        if ($server === null) {
            throw new Exception('Server not found');
        }

        $peers = $this->getPeers();
        if ($peers === false) {
            return;
        }

        $target = null;
        foreach ($peers as $peer) {
            if ($peer->getSlug() === $slug) {
                $target = $peer;
                break;
            }
        }
        if (!$target) {
            return;
        }
        $this->removeFileOfPeer($target);

        $peers = $this->getPeers();
        if ($peers === false) {
            $peers = [];
        }

        $this->manager->reloadFileConfig($server, $peers);
    }

    public function createPeer(string $name): ?Peer
    {
        $this->generatePeersDirectory();

        $server = $this->getServer();
        if (!$server) {
            throw new Exception('Server not found');
        }
        $slug = Str::slug($name);

        $keys = $this->generateKeysPeers($slug);
        if ($keys === false) {
            throw new Exception('Cant generate keys');
        }

        $peer = new Peer(
            $name,
            $this->nextAllowAddress()->getValue(),
            $keys['publicKey'],
            $keys['privateKey'],
            $keys['presharedKey'],
        );

        $this->setPeerFile($peer, $server);
        $peers = $this->getPeers();
        if ($peers === false) {
            throw new Exception('Cant create peer');
        }
        $this->manager->reloadFileConfig($server, $peers);

        return $peer;
    }

    public function nextAllowAddress(): Ip
    {
        $address = $this->getServer()?->getAddress() ?? null;
        if ($address === null) {
            throw new Exception('Server not found');
        }

        $peers = $this->getPeers();

        if ($peers === false) {
            $newIp = preg_replace("/([0-9]+)$/", "2", $address);
            return is_string($newIp) ? new Ip(
                $newIp
            ) : throw new Exception('Invalid ip');
        }

        $numbers = array_map(function (Peer $peer) {
            $ip = $peer->getAddress();
            $number = (int) preg_replace("/^[0-9]+\.[0-9]+.[0-9]+.([0-9]+)$/", "$1", $ip);
            return $number;
        }, $peers);

        sort($numbers);

        $ip = preg_replace("/\/32/", "", $peers[0]->getAddress());
        if (!is_string($ip)) {
            throw new Exception('Invalid ip');
        }

        $i = 0;
        foreach ($numbers as $number) {
            if ($number !== ($i + 2)) {
                $newIp = preg_replace("/([0-9]+)$/", (string) ($i + 2), $ip);
                return is_string($newIp)
                    ? new Ip($newIp)
                    : throw new Exception('Invalid ip');
            }
            $i++;
        }
        $number = ip2long($address);
        if ($number === false) {
            throw new Exception('Invalid ip');
        }
        return new Ip(
            long2ip($number + 1)
        );
    }
}
