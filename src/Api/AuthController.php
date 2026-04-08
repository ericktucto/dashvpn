<?php

namespace App\Api;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\ConnectionResolverInterface;
use Psr\Container\ContainerInterface;
use Touch\Http\Request;
use Touch\Http\Response;

class AuthController
{
    public function __construct(
        protected ContainerInterface $container,
        protected ConnectionResolverInterface $resolver,
    ) {
        User::setConnectionResolver($this->resolver);
    }

    public function login(
        Request $request
    ) {
        try {
            $data = $request->getBody()->getContents();
            $json = json_decode($data);

            // validation
            /** @var User|null $user */
            $user = User::where('username', $json->username)->first();
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
            dd($e);
            return Response::json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function register(Request $request)
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

    public function changePassword(Request $request)
    {
        $data = $request->getBody()->getContents();
        $json = json_decode($data);
        if ($json->password !== $json->password_confirmation) {
            return Response::json([
                'message' => 'Password not match',
            ], 422);
        }
        $headers = $request->getHeader('Authorization');
        $splitted = explode(' ', $headers[0]);
        $token = $splitted[1];
        $key = $this->container->get('config')->get('jwt.key');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));

        /** @var User|null $user */
        $user = User::where('username', $decoded->user->username)->first();
        if (!$user) {
            return Response::json([
                'message' => 'Password not match',
            ], 422);
        }
        $user->password = $json->password;
        $user->save();

        return Response::json([
            'message' => 'Password changed',
        ]);
    }
}
