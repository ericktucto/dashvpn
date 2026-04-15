<?php

use App\Definitions\AuthDefinition;
use App\Definitions\WireguardServiceDefinition;
use App\Domain\AuthInterface;
use App\Services\ServerManageInterface;
use function DI\factory;

return [
    AuthInterface::class => factory([AuthDefinition::class, "create"]),
    ServerManageInterface::class => factory([WireguardServiceDefinition::class, "create"]),
];
