<?php

namespace App\Api;

use App\Services\SystemService;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Response;

final class SystemController
{
    public function __construct(
        protected SystemService $service
    ) {
    }

    public function interfaces(): ResponseInterface
    {
        return Response::json([
            'data' => array_keys($this->service->interfaces()),
        ]);
    }
}
