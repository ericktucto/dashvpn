<?php

use App\Definitions\WireguardServiceDefinition;
use App\Services\WireguardWrapperInterface;
use function DI\factory;

return [
    WireguardWrapperInterface::class => factory([WireguardServiceDefinition::class, "create"]),
];
