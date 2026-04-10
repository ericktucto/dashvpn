<?php

use App\Definitions\AuthDefinition;
use App\Definitions\WireguardServiceDefinition;
use App\Domain\AuthInterface;
use App\Services\WireguardWrapperInterface;
use function DI\factory;

return [
    AuthInterface::class => factory([AuthDefinition::class, "create"]),
    WireguardWrapperInterface::class => factory([WireguardServiceDefinition::class, "create"]),
];
