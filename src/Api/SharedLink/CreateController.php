<?php

namespace App\Api\SharedLink;

use App\Builders\PeerConfig;
use App\Builders\Signature;
use App\Services\WireguardWrapper;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Request;
use Touch\Http\Response;
use GuzzleHttp\Client;


final class CreateController
{
    protected string $shared_api = '';
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected ContainerInterface $container,
        protected WireguardWrapper $wrapper,
        protected Signature $signature
    ) {
        $this->shared_api = $this->container->get('config')->get('shared_api');
    }

    /*
     * @param array{slug: string} $args
     */
    public function index(
        Request $_request,
        array $args,
    ): ResponseInterface {
        $slug = $args['slug'];

        $server = $this->wrapper->getServer();
        if ($server === null) {
            return Response::json([
                'message' => 'Server not found',
            ], 500);
        }

        $peers = $this->wrapper->getPeers();
        if ($peers === false) {
            return Response::json([
                'message' => 'Error getting peers',
            ], 500);
        }

        $target = null;
        foreach ($peers as $peer) {
            if ($peer->getSlug() === $slug) {
                $target = $peer;
                break;
            }
        }

        if ($target === null) {
            return Response::json([
                'message' => 'Peer not found',
            ], 422);
        }

        $builder = new PeerConfig($server, $target);

        $client = new Client();

        $data = [
            'slug' => $slug,
            'contents' => join("", $builder->generate()),
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'X-Signature' => $this->signature->signature($data),
        ];

        $response = $client->request('POST', "{$this->shared_api}/api/peer/link", [
            'headers' => $headers,
            'json' => $data,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (!is_array($data)) {
            return Response::json([
                'message' => 'Error creating link',
            ], 500);
        }

        return Response::json([
            "data" => [
                "url" => "{$this->shared_api}/{$slug}",
                "otp" => $data['data']['otp'],
            ],
        ]);
    }
}
