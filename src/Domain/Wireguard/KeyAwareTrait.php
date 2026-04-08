<?php

namespace App\Domain\Wireguard;

trait KeyAwareTrait
{
    protected string $publicKey;
    protected string $privateKey;
    protected string $preSharedKey;

    public function setKeys(string $publicKey, string $privateKey, string $preSharedKey = ''): void
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->preSharedKey = $preSharedKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getPresharedKey(): string
    {
        return $this->preSharedKey;
    }
}
