<?php

namespace App\Services;

use App\Adapters\Wireguard\FileToServer;
use App\Builders\ServerConfig;
use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Server;
use App\Helper;
use DI\Attribute\Inject;
use Exception;
use Noodlehaus\Config;
use Override;
use Psr\Container\ContainerInterface;

/**
 * @todo crear la version para wg-quick
 */
final class ServerLocalManage implements ServerManageInterface
{
    protected string $prefix;

    public function __construct(
        protected FileToServer $adapterServer,
        protected ContainerInterface $container,
    ) {
        $this->prefix = $this->getConfigData('data.config_dir');
    }

    protected function getConfigData(string $key): string
    {
        $value = $this->container->get('config')->get($key);

        if (!is_string($value)) {
            throw new Exception("Config key '{$key}' must be a string");
        }

        return $value;
    }

    #[Override]
    public function createServer(
        Ip $address,
        int $listenPort,
    ): Server {
        $ip = new Ip(
            $this->getConfigData('data.ip'),
        );
        $dns = new Ip(
            $this->getConfigData('data.dns'),
        );
        $server = new Server(
            $address->getValue(),
            $ip->getValue(),
            $listenPort,
            $dns->getValue(),
        );

        $keys = $this->generateKeys();
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
    public function getServer(): ?Server
    {
        $result = (bool) exec("cat {$this->prefix}/wg0.conf", $output);
        if (!$result) {
            return null;
        }
        $keys = $this->getServerKeys();
        if ($keys === false) {
            throw new Exception('No keys');
        }
        return $this->adapterServer->parse($keys, $output);
    }

    #[Override]
    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function generateKeys(): array|false
    {
        Helper::outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wg0.pub");

        Helper::outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wg0.key");

        Helper::outputFirstLine("openssl rand -base64 32 > {$this->prefix}/wg0.psk");

        return [
            "publicKey" => (string) file_get_contents("{$this->prefix}/wireguard/wg0.pub"),
            "privateKey" => (string) file_get_contents("{$this->prefix}/wireguard/wg0.key"),
            "presharedKey" => (string) file_get_contents("{$this->prefix}/wireguard/wg0.psk"),
        ];
    }

    #[Override]
    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function getServerKeys(): array|false
    {
        $keys = [
            'publicKey' => '',
            'privateKey' => '',
            'presharedKey' => '',
        ];
        $keys['publicKey'] = Helper::outputFirstLine("cat {$this->prefix}/wg0.pub");
        if ($keys['publicKey'] === false) {
            return false;
        }

        $keys['privateKey'] = Helper::outputFirstLine("cat {$this->prefix}/wg0.key");
        if ($keys['privateKey'] === false) {
            return false;
        }

        $psk = Helper::outputFirstLine("cat {$this->prefix}/wg0.psk");
        if ($psk === false) {
            return false;
        }
        $keys['presharedKey'] = $psk;

        return $keys;
    }

    #[Override]
    /**
     * @param \App\Domain\Wireguard\Peer[] $peers
     */
    public function reloadFileConfig(Server $server, array $peers): void
    {
        $builder = new ServerConfig($server, $peers);
        $lines = $builder->generate();

        file_put_contents("{$this->prefix}/wg0.conf", $lines);

        // @todo clear peers
    }
}
