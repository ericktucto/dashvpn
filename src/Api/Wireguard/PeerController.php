<?php

namespace App\Api\Wireguard;

use App\Domain\Wireguard\Ip;
use App\Services\WireguardService;
use App\Services\WireguardWrapperInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Request;
use Touch\Http\Response;

final class PeerController
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected WireguardService $service,
        protected WireguardWrapperInterface $wrapper,
    ) {
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): ResponseInterface
    {
        $peers = $this->service->peers();
        return Response::json([
            'data' => array_map(fn($peer) => $peer->toArray(), $peers),
        ]);
    }

    protected function isPeerNameExists(string $slug): bool
    {
        $peers = $this->service->peers();
        foreach ($peers as $peer) {
            if ($peer->getSlug() === $slug) {
                return true;
            }
        }
        return false;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function store(
        Request $request
    ): ResponseInterface {
        $json = json_decode($request->getBody()->getContents());

        $slug = Str::slug($json->name);
        if ($this->isPeerNameExists($slug)) {
            return Response::json([
                'message' => 'Peer name already exists',
            ], 422);
        }

        return Response::json([
            'data' => $this->service->createPeer(
                $json->name,
            )->toArray(),
        ]);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param array{slug: string} $args
     */
    public function update(
        Request $request,
        array $args,
    ): ResponseInterface {
        $json = json_decode($request->getBody()->getContents());

        $peer = $this->service->updatePeer(
            $args['slug'],
            $json->name,
            new Ip($json->address),
        );

        return Response::json([
            'data' => $peer->toArray(),
            'message' => 'Peer updated',
        ]);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param array{slug: string} $args
     */
    public function destroy(
        Request $_request,
        array $args,
    ): ResponseInterface {
        $this->wrapper->deletePeer($args['slug']);
        return Response::json([
            'message' => 'Peer deleted',
        ]);
    }

    public function config(
        Request $_request,
        array $args,
    ): ResponseInterface {
        $content = (string) $this->wrapper->getConfigPeer($args['slug']);
        $headers = ["Content-Type" => "text/plain", "Content-Length" => strlen($content)];
        $response = new GuzzleResponse(200, $headers);
        $response->getBody()->write($content);
        $response->getBody()->rewind();
        return $response;
    }
}
