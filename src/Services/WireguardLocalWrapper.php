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

class WireguardLocalWrapper implements WireguardWrapperInterface, PeerManageInterface, ServerManageInterface
{
    use HasPeerLocalManage;
    use HasServerLocalManage;

    public function __construct(
        protected FileToServer $adapterServer,
        protected FileToPeer $adapterPeer,
        protected string $prefix,
    ) {
    }

    #[Override]
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

    #[Override]
    public function getServerKeys(): array|false
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

        $psk = $this->getPsk();
        if ($psk === false) {
            return false;
        }
        $keys['presharedKey'] = $psk;

        return $keys;
    }

    #[Override]
    /**
     * @return Peer[]|false
     */
    public function getPeers(): array|false
    {
        $result = (bool) exec("ls {$this->prefix}/wireguard/peers/*.conf", $output);
        if (!$result || !$output) {
            return false;
        }

        $psk = $this->getPsk();
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
    public function getPsk(): string|false
    {
        return Helper::outputFirstLine("cat {$this->prefix}/wireguard/wg0.psk");
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
        Ip $ip,
        Ip $address,
        int $listenPort,
        Ip|null $dns,
    ): Server {
        $server = new Server(
            $address->getValue(),
            $ip->getValue(),
            $listenPort,
            $dns?->getValue() ?? '',
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
    public function nextAddress(): Ip
    {
        $output = null;
        exec("grep Address -Rna {$this->prefix}/wireguard/peers | awk '{print $3}'", $output);
        if (count($output) === 0) {

            $ip = null;
            exec("grep Address {$this->prefix}/wireguard/wg0.conf | awk '{print $3}'", $ip);
            if (!$ip) {
                throw new Exception('No address found');
            }

            $newIp = preg_replace('/\d+\/\d+$/', '2', $ip[0]);
            if ($newIp === null) {
                throw new Exception('No address found');
            }
            return new Ip(
                $newIp
            );
        }

        sort($output);
        $last = $output[count($output) - 1];
        $ip = preg_replace('/\/\d+$/', '', $last);
        if ($ip === null) {
            throw new Exception('No address found');
        }

        $ipv4 = ip2long($ip);

        if ($ipv4 === false) {
            throw new Exception('No address found');
        }

        return new Ip(
            long2ip($ipv4 + 1),
        );
    }

    public function isPeerNameExists(string $slug): bool
    {
        return file_exists("{$this->prefix}/wireguard/peers/{$slug}.conf");
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
            $this->nextAddress()->getValue(),
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
