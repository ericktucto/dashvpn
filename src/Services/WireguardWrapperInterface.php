<?php

namespace App\Services;

interface WireguardWrapperInterface
{
    /**
     * @return string[]|false
     */
    public function getServer(): array|false;

    /**
     * @return string[]|false
     */
    public function getServerKeys(): array|false;
}
