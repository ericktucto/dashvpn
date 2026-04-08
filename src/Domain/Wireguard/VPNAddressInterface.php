<?php

namespace App\Domain\Wireguard;

interface VPNAddressInterface
{
    public function getAddress(): string;
}
