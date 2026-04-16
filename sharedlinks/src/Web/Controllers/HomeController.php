<?php

namespace App\Web\Controllers;

use App\Models\SharedLink;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\ConnectionResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Touch\Http\EngineTemplate;
use Touch\Http\Request;

final class HomeController
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        protected EngineTemplate $twig,
        ConnectionResolverInterface $resolver,
    ) {
        SharedLink::setConnectionResolver($resolver);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param array{slug: string} $args
     */
    public function index(
        Request $_request,
        array $args
    ): ResponseInterface {
        $sharedLink = SharedLink::query()
            ->where([
                'slug' => $args['slug'],
            ])
            ->where('exp', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->first();
        return $sharedLink === null
            ? $this->twig->render('expirated')
            : $this->twig->render('index');
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @param array{slug: string} $args
     */
    public function peer(
        Request $request,
        array $args,
    ): ResponseInterface {
        /** @var array{otp: string} $json */
        $body = [];
        parse_str($request->getBody()->getContents(), $body);

        if (array_key_exists('otp', $body) === false) {
            return $this->twig->render('index', [
                'error' => 'Debes enviar un código',
            ])->withStatus(422);
        }
        $sharedLink = SharedLink::query()
            ->where([
                'slug' => $args['slug'],
                'otp' => $body['otp'],
            ])
            ->where('exp', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->first();
        return $sharedLink === null
            ? $this->twig->render('index', [
                    "error" => "Código inválido",
                ])->withStatus(422)
            : $this->twig->render('sharedlink', compact('sharedLink'));

    }
}
