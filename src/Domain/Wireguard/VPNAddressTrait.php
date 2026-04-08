<?php

namespace App\Domain\Wireguard;

trait VPNAddressTrait
{
    protected string $address;

    public function getAddress(): string
    {
        return $this->address;
    }
}
