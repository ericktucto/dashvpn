<?php

namespace App\Api\Wireguard;

use App\Services\WireguardService;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Response;

class ServerController
{
    public function __construct(
        protected WireguardService $service,
    ) {
    }

    public function index(): ResponseInterface
    {
        return Response::json([
            'data' => $this->service->server()->toArray(),
        ]);
    }
}
