<?php

namespace App\Api;

use App\Api\Wireguard\PeerController;
use App\Api\Wireguard\ServerController;
use League\Route\RouteGroup;
use Override;
use Touch\Http\Route;

final class WireguardRoutes extends Route
{
    #[Override]
    protected function routes(RouteGroup $group): void
    {
        $group->get('/server', [ServerController::class, 'index']);
        $group->post('/server', [ServerController::class, 'store']);

        $group->get('/peers', [PeerController::class, 'index']);
        $group->post('/peers', [PeerController::class, 'store']);
        $group->put('/peers/{slug}', [PeerController::class, 'update']);
        $group->delete('/peers/{slug}', [PeerController::class, 'destroy']);
    }
}
