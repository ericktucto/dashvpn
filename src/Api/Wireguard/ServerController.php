<?php

namespace App\Api\Wireguard;

use App\Domain\Wireguard\Ip;
use App\Services\WireguardService;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Request;
use Touch\Http\Response;

final class ServerController
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected WireguardService $service,
    ) {
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): ResponseInterface
    {
        $server = $this->service->server();
        if ($server === null) {
            return Response::json([
                'message' => 'Server not found',
            ], 422);
        }
        return Response::json([
            'data' => $server->toArray(),
        ]);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function store(
        Request $request
    ): ResponseInterface {
        $json = json_decode($request->getBody()->getContents());

        return Response::json([
            'data' => $this->service->createServer(
                new Ip($json->ip),
                (int) $json->listen_port,
                new Ip($json->address),
                new Ip($json->dns),
            )->toArray(),
        ]);
    }
}
