<?php

namespace App\Api\Controllers;

use App\Models\SharedLink;
use Carbon\Carbon;
use Illuminate\Database\ConnectionResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Touch\Http\Request;
use Touch\Http\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

final class PeerController
{
    public function __construct(
        ConnectionResolverInterface $resolver,
    ) {
        SharedLink::setConnectionResolver($resolver);
    }

    protected function generateOTP(): string
    {
        return str_pad(
            (string) random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function link(
        ServerRequestInterface $request
    ): ResponseInterface {
        $json = json_decode($request->getBody()->getContents());

        $sharedLink = new SharedLink();
        $sharedLink->slug = (string) $json->slug;
        $sharedLink->contents = (string) $json->contents;
        $sharedLink->otp = $this->generateOTP();
        $sharedLink->exp = Carbon::now()->addMinutes(15);
        $sharedLink->save();

        return Response::json([
            'data' => [
                'otp' => $sharedLink->otp,
            ],
        ]);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param array{slug: string} $args
     */
    public function config(
        Request $request,
        array $args,
    ): ResponseInterface {
        $json = json_decode($request->getBody()->getContents());

        $result = SharedLink::query()->where([
            'slug' => $args['slug'],
            'otp' => $json->otp,
        ])->first();

        if ($result === null) {
            return Response::json([
                'message' => 'OTP inválido',
            ]);
        }

        $content = $result->contents;
        $headers = ["Content-Type" => "text/plain", "Content-Length" => strlen($content)];
        $response = new GuzzleResponse(200, $headers);
        $response->getBody()->write($content);
        $response->getBody()->rewind();

        return $response;
    }
}
