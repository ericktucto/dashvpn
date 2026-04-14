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
use Exception;
use Illuminate\Support\Str;
use Override;
use Psr\Container\ContainerInterface;

final class WireguardLocalWrapper implements WireguardWrapperInterface, PeerManageInterface, ServerManageInterface
{
    use HasPeerLocalManage;
    use HasServerLocalManage;

    public function __construct(
        protected FileToServer $adapterServer,
        protected string $prefix,
        protected ContainerInterface $container,
    ) {
    }

    #[Override]
    /**
     * @return list<Peer>|false
     */
    public function getPeers(): array|false
    {
        $result = (bool) exec("ls {$this->prefix}/wireguard/peers/*.conf", $output);
        if (!$result || !$output) {
            return false;
        }

        $psk = $this->getServer()?->getPresharedKey() ?? false;
        if ($psk === false) {
            return false;
        }

        $peers = [];

        $adapter = new FileToPeer();
        /** @var string[] $output */
        foreach ($output as $peer) {
            $data = null;
            exec("cat $peer", $data);

            // keys
            $keys = [
                'presharedKey' => $psk,
            ];

            $pub = preg_replace('/.conf$/', '.pub', $peer);
            $keys['publicKey'] = (string) Helper::outputFirstLine("cat $pub");

            $prv = preg_replace('/.conf$/', '.key', $peer);
            $keys['privateKey'] = (string) Helper::outputFirstLine("cat $prv");

            $peers[] = $adapter->parse($keys, $data);
        }

        return $peers;
    }

    #[Override]
    public function getConfigPeer(string $slug): string|false
    {
        return file_get_contents("{$this->prefix}/wireguard/peers/{$slug}.conf");
    }

    public function generateKeys(
        string $target,
    ): array|false {
        Helper::outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/{$target}.pub");
        $pub = Helper::outputFirstLine("cat {$this->prefix}/wireguard/wg0.pub");

        Helper::outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/{$target}.key");
        $key = Helper::outputFirstLine("cat {$this->prefix}/wireguard/wg0.key");

        Helper::outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wireguard/{$target}.psk");
        $psk = Helper::outputFirstLine("cat {$this->prefix}/wireguard/wg0.psk");

        return [
            "publicKey" => $pub,
            "privateKey" => $key,
            "presharedKey" => $psk,
        ];
    }

    #[Override]
    public function createServer(
        Ip $address,
        int $listenPort,
    ): Server {
        $ip = new Ip($this->container->get('config')->get('data.ip'));
        $dns = new Ip($this->container->get('config')->get('data.dns'));
        $server = new Server(
            $address->getValue(),
            $ip->getValue(),
            $listenPort,
            $dns->getValue(),
        );

        $keys = $this->generateKeys('wg0');
        if ($keys === false) {
            throw new Exception('Cant generate keys');
        }

        $server->setKeys(
            $keys['publicKey'],
            $keys['privateKey'],
            $keys['presharedKey'],
        );

        $this->reloadFileConfig($server, []);

        return $server;
    }

    protected function setKeys(string $slug, string $pub, string $prv): void
    {
        file_put_contents("{$this->prefix}/wireguard/peers/{$slug}.pub", $pub);
        file_put_contents("{$this->prefix}/wireguard/peers/{$slug}.key", $prv);
    }

    #[Override]
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
            if ($peer->getSlug() === $slug) {
                $target = $peer;
                $currentKey = $key;
                break;
            }
        }
        if (!$target) {
            throw new Exception('Peer not found');
        }

        if ($target->getSlug() !== Str::slug($name)) {
            $this->setKeys(Str::slug($name), $target->getPublicKey(), $target->getPrivateKey());
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
        $this->reloadFileConfig($server, $peers);

        return $peer;
    }

    #[Override]
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

        $this->reloadFileConfig($server, $peers);
    }

    protected function generatePeersDirectory(): void
    {
        @mkdir("{$this->prefix}/wireguard/peers");
    }

    #[Override]
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
        $this->reloadFileConfig($server, $peers);

        return $peer;
    }
}
