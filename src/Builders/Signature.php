<?php

namespace App\Builders;

use Exception;
use Psr\Container\ContainerInterface;

final class Signature
{
    protected string $privateKey;

    public function __construct(
        protected ContainerInterface $container,
    ) {
        $path = $this->container->get('config')->get('private_key');
        $content = file_get_contents($path);
        if ($content === false) {
            throw new Exception('Private key not found');
        }

        $this->privateKey = $content;
    }

    /**
     * @param mixed[] $data
     */
    public function signature(array $data): string
    {
        $stringData = json_encode($data);
        if ($stringData === false) {
            throw new Exception('Invalid data');
        }
        openssl_sign(
            $stringData,
            $signature,
            $this->privateKey,
            OPENSSL_ALGO_SHA256
        );

        return base64_encode($signature);
    }
}
