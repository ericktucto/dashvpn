<?php

namespace App\Domain\Wireguard;

class Server implements KeyAwareInterface, VPNAddressInterface
{
    use KeyAwareTrait;
    use VPNAddressTrait;

    public function __construct(
        protected string $address,
        protected string $ip,
        protected int $listenPort = 51820,
        protected string $dns = '',
    ) {
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getListenPort(): int
    {
        return $this->listenPort;
    }

    public function getDns(): string
    {
        return $this->dns;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'address' => $this->address . '/24',
            'ip' => $this->ip,
            'port' => $this->listenPort,
            'dns' => $this->dns,
            'public_key' => $this->publicKey,
        ];
    }
}
