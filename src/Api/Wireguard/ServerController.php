<?php

namespace App\Api\Wireguard;

use App\Domain\Wireguard\Ip;
use App\Services\SystemService;
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
        protected SystemService $system,
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
                $json->post_up,
                $json->post_down,
                $json->interface,
            )->toArray(),
        ]);
    }

    public function recommended(): ResponseInterface
    {
        return Response::json([
            'data' => [
                'listen_port' => 51820,
                'address' => '10.20.0.1',
                'dns' => '1.1.1.1',
                'post_up' => [
                    "iptables -t nat -A POSTROUTING -s %net%/24 -o %interface% -j MASQUERADE",
                    "iptables -A FORWARD -i wg0 -o %interface% -j ACCEPT",
                    "iptables -A FORWARD -i %interface% -o wg0 -m state --state RELATED,ESTABLISHED -j ACCEPT",
                ],
                'post_down' => [
                    "iptables -t nat -A POSTROUTING -s %net%/24 -o %interface% -j MASQUERADE",
                    "iptables -D FORWARD -i wg0 -o %interface% -j ACCEPT",
                    "iptables -D FORWARD -i %interface% -o wg0 -m state --state RELATED,ESTABLISHED -j ACCEPT",
                ],
                'interface' => $this->getInterfaceDefault(),
            ],
        ]);
    }

    protected function getInterfaceDefault(): string
    {
        $interfaces = $this->system->interfaces();
        if ($interfaces === []) {
            return '';
        }

        $first = '';
        foreach ($interfaces as $name => $interface) {
            if (preg_match('/^(eth|en|wlan|wl)/', $name)) {
                if ($first === '') {
                    $first = $name;
                }
                foreach ($interface['unicast'] as $addr) {
                    if (
                        $addr['family'] === 2 &&
                        isset($addr['address']) &&
                        $addr['address'] !== '127.0.0.1'
                    ) {
                        return $name;
                    }
                }
            }
        }
        return $first;
    }
}
