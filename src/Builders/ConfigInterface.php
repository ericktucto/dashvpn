<?php

namespace App\Builders;

interface ConfigInterface
{
    /**
     * @return string[]
     */
    public function generate(): array;
}
