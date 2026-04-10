<?php

namespace App\Api;

use App\Domain\AuthInterface;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\ConnectionResolverInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\Request;
use Touch\Http\Response;

final class AuthController
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected ContainerInterface $container,
        protected ConnectionResolverInterface $resolver,
        protected AuthInterface $auth,
    ) {
        User::setConnectionResolver($this->resolver);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function login(
        Request $request
    ): ResponseInterface {
        try {
            $data = $request->getBody()->getContents();
            $json = json_decode($data);

            // validation
            /** @var User|null $user */
            $user = User::query()->where('username', $json->username)->first();
            if (
                !$user || !$user->isPassword($json->password)
            ) {
                return Response::json(['message' => 'Invalid credentials'], 401);
            }

            $payload = [
                'iss' => 'dashvpn.back',
                'aud' => 'dashvpn-ui',
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + $this->container->get('config')->get('jwt.expires'),
                'user' => [
                    'username' => $json->username,
                ],
            ];

            $token = JWT::encode(
                $payload,
                $this->container->get('config')->get('jwt.key'),
                'HS256',
            );
            return Response::json([
                'data' => $token,
            ]);
        } catch (\Throwable $e) {
            return Response::json(['message' => 'Invalid credentials'], 401);
        }
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function register(Request $request): ResponseInterface
    {
        $data = $request->getBody()->getContents();
        $json = json_decode($data);

        try {
            $user = new User();
            $user->username = $json->username;
            $user->password = $json->password;
            $user->save();

            return Response::json([
                'message' => 'User created successfully',
            ]);
        } catch (\Throwable $e) {
            return Response::json([
                'message' => 'User not created',
            ], 422);
        }
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function changePassword(Request $request): ResponseInterface
    {
        $data = $request->getBody()->getContents();
        $json = json_decode($data);
        if ($json->password !== $json->password_confirmation) {
            return Response::json([
                'message' => 'Password not match',
            ], 422);
        }

        $user = $this->auth->user();
        if (!$user) {
            return Response::json([
                'message' => 'Invalid credentials',
            ], 422);
        }
        $user->password = $json->password;
        $user->save();

        return Response::json([
            'message' => 'Password changed',
        ]);
    }
}
