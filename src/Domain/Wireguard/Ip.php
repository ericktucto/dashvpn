<?php

namespace App\Domain\Wireguard;

final class Ip
{
    public function __construct(
        private string $ip,
    ) {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            throw new \Exception('Invalid IP');
        }
    }

    public function getValue(): string
    {
        return $this->ip;
    }
}
