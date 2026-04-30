<?php

namespace App\Domain\Wireguard;

use Illuminate\Support\Str;

final class Server implements KeyAwareInterface, VPNAddressInterface
{
    use KeyAwareTrait;
    use VPNAddressTrait;

    public function __construct(
        protected string $address,
        protected string $ip,
        protected int $listenPort = 51820,
        protected string $dns = '',
        /** @var list<string> */
        protected array $postUp = [],
        /** @var list<string> */
        protected array $postDown = [],
        protected string $interface = '',
    ) {
        $this->setKeys('', '');
    }

    public function getPostDownParsed(): string
    {
        $result = [];
        foreach ($this->postDown as $line) {
            if (Str::startsWith($line, '#')) {
                continue;
            }
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }
            $result[] = strtr($trimmed, [
                '%interface%' => $this->getInterface(),
                '%address%' => $this->getAddress(),
                '%net%' => $this->getNet(),
            ]);
        }
        return join('; ', $result);
    }

    public function getNet(): string
    {
        $parts = explode('.', $this->getAddress());
        $parts[3] = '0';
        return implode('.', $parts);
    }

    public function getPostUpParsed(): string
    {
        $result = [];
        foreach ($this->postUp as $line) {
            if (Str::startsWith($line, '#')) {
                continue;
            }
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }
            $result[] = strtr($trimmed, [
                '%interface%' => $this->getInterface(),
                '%address%' => $this->getAddress(),
                '%net%' => $this->getNet(),
            ]);
        }
        return join('; ', $result);
    }

    public function getInterface(): string
    {
        return $this->interface;
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
            'address' => $this->address,
            'ip' => $this->ip,
            'port' => $this->listenPort,
            'dns' => $this->dns,
            'public_key' => $this->publicKey,
            'post_up' => $this->postUp,
            'post_down' => $this->postDown,
            'interface' => $this->interface,
        ];
    }
}
