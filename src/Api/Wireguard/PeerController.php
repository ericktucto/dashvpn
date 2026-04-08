<?php

namespace App\Api\Wireguard;

use App\Services\WireguardService;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Response;

class PeerController
{
    public function __construct(
        protected WireguardService $service,
    ) {
    }

    public function index(): ResponseInterface
    {
        $peers = $this->service->peers();
        return Response::json([
            'data' => array_map(fn($peer) => $peer->toArray(), $peers),
        ]);
    }
}
