<?php

namespace App\Api;

use App\Api\Wireguard\PeerController;
use App\Api\Wireguard\ServerController;
use League\Route\RouteGroup;
use Touch\Http\Route;

class WireguardRoutes extends Route
{
    protected function routes(RouteGroup $group): void
    {
        $group->get('/peers', [PeerController::class, 'index']);
        $group->post('/peers', [PeerController::class, 'create']);
        $group->get('/server', [ServerController::class, 'index']);
        $group->post('/server', [ServerController::class, 'create']);
    }
}
