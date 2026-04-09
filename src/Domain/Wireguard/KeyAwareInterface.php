<?php

namespace App\Domain\Wireguard;

interface KeyAwareInterface
{
    public function setKeys(string $publicKey, string $privateKey, string $preSharedKey = ''): void;
    public function getPublicKey(): string;
    public function getPrivateKey(): string;
    public function getPresharedKey(): string;
}
