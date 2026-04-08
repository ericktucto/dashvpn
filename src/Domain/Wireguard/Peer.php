<?php

namespace App\Domain\Wireguard;

class Peer implements KeyAwareInterface, VPNAddressInterface
{
    use KeyAwareTrait;
    use VPNAddressTrait;

    public function __construct(
        private string $name,
        private string $address,
        private string $publicKey,
        private string $privateKey,
        private string $preSharedKey = '',
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'publicKey' => $this->publicKey,
        ];
    }
}
