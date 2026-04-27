<?php

namespace App\Services;

class SystemService
{
    /**
     * @return array<mixed>
     */
    public function interfaces(): array
    {
        $interfaces = net_get_interfaces();
        return $interfaces === false ? [] : $interfaces;
    }
}
