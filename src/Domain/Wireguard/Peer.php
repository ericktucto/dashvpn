<?php

namespace App\Domain\Wireguard;

use Illuminate\Support\Str;

final class Peer implements KeyAwareInterface, VPNAddressInterface
{
    use KeyAwareTrait;
    use VPNAddressTrait;

    public function __construct(
        protected string $name,
        protected string $address,
        protected string $publicKey,
        protected string $privateKey,
        protected string $preSharedKey = '',
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return Str::slug($this->name);
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
            'slug' => $this->getSlug(),
        ];
    }
}
