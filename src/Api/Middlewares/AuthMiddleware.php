<?php

namespace App\Api\Middlewares;

use App\Domain\AuthInterface;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\ConnectionResolverInterface;
use Override;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Touch\Http\Response;

final class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected ContainerInterface $container,
        protected AuthInterface $auth,
        protected ConnectionResolverInterface $resolver,
    ) {
        User::setConnectionResolver($resolver);
    }

    #[Override]
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $headers = $request->getHeader('Authorization');
        if (count($headers) === 0) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }
        $splitted = explode(' ', $headers[0]);
        if (count($splitted) !== 2) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }

        $token = $splitted[1];
        $key = $this->container->get('config')->get('jwt.key');

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            /** @var ?User $user */
            $user = User::query()->where("username", $decoded->user->username)->first();
            if (!$user) {
                return Response::json(['message' => 'Unauthorized'], 401);
            }

            $this->auth->setUser($user);
        } catch (\Exception $e) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }

        return $handler->handle($request);
    }
}
