<?php

namespace App\Api\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Override;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Touch\Http\Response;

class PrivateMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected ContainerInterface $container,
    ) {
    }

    #[Override]
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $headers = $request->getHeader('X-Key');
        if (count($headers) === 0) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }
        $key = $headers[0];

        if ($this->container->get('config')->get('jwt.key') !== $key) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }

        return $handler->handle($request);
    }
}
