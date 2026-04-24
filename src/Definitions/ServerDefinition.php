<?php

namespace App\Definitions;

use App\Models\Server;
use Illuminate\Database\ConnectionResolverInterface;

final class ServerDefinition
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function create(
        ConnectionResolverInterface $resolver,
    ): ?Server {
        Server::setConnectionResolver($resolver);
        /** @var ?Server $server */
        $server = Server::query()->first();
        return $server;
    }
}
