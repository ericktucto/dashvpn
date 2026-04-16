<?php

namespace App\Api\Middlewares;

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

    protected function getPublicPem(): string
    {
        $path = (string) $this->container->get('config')->get('key_public');
        return (string) file_get_contents($path);
    }

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $headers = $request->getHeader('X-Signature');
        if (count($headers) === 0) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }
        $signature = $headers[0];

        $isValid = openssl_verify(
            $request->getBody()->getContents(),
            base64_decode($signature),
            $this->getPublicPem(),
            OPENSSL_ALGO_SHA256
        );

        if ($isValid === 1) {
            $request->getBody()->rewind();
            return $handler->handle($request);
        }
        return Response::json(["message" => "Unauthorized"], 401);
    }
}
