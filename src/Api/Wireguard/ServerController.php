<?php

namespace App\Api\Wireguard;

use App\Domain\Wireguard\Ip;
use App\Services\WireguardService;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Request;
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

    public function store(
        Request $request
    ): ResponseInterface {
        $json = json_decode($request->getBody()->getContents());

        return Response::json([
            'data' => $this->service->createServer(
                new Ip($json->ip),
                new Ip($json->address),
                (int) $json->listen_port,
                new Ip($json->dns ?? '1.1.1.1'),
            )->toArray(),
        ]);
    }
}
