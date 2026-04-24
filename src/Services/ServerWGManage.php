<?php

namespace App\Services;

use App\Builders\ServerConfig;
use App\Domain\Wireguard\Ip;
use App\Domain\Wireguard\Server;
use App\Helper;
use DI\Attribute\Inject;
use Exception;
use Noodlehaus\Config;
use Override;
use Psr\Container\ContainerInterface;

final class ServerWGManage implements ServerManageInterface
{
    protected string $prefix;

    public function __construct(
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
        Ip $ip,
        int $listenPort,
        Ip $address,
        Ip $dns,
    ): Server {
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
    /**
     * @return array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * }|false
     */
    public function generateKeys(): array|false
    {
        exec("sudo PREFIX_DIR='{$this->prefix}' /usr/local/bin/wg-manager generate-keys");

        return $this->getServerKeys();
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

        $output = [];
        exec("sudo PREFIX_DIR='{$this->prefix}' /usr/local/bin/wg-manager get-keys", $output);
        foreach ($output as $line) {
            $split = explode('=', $line);
            $keys[$split[0]] = $split[1];
        }

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

        exec("sudo PREFIX_DIR='{$this->prefix}' /usr/local/bin/wg-manager reload");
    }
}
