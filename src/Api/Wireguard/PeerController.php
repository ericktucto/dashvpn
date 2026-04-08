<?php

namespace App\Api\Wireguard;

use App\Services\WireguardService;
use App\Services\WireguardWrapperInterface;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Request;
use Touch\Http\Response;

class PeerController
{
    public function __construct(
        protected WireguardService $service,
        protected WireguardWrapperInterface $wrapper,
    ) {
    }

    public function index(): ResponseInterface
    {
        $peers = $this->service->peers();
        return Response::json([
            'data' => array_map(fn($peer) => $peer->toArray(), $peers),
        ]);
    }

    public function create(
        Request $request
    ): ResponseInterface {
        $json = json_decode($request->getBody()->getContents());

        $slug = Str::slug($json->name);
        if ($this->wrapper->isPeerNameExists($slug)) {
            return Response::json([
                'message' => 'Peer name already exists',
            ], 422);
        }


        return Response::json([
            'data' => $this->service->addPeer(
                $json->name,
            )->toArray(),
        ]);
    }
}
