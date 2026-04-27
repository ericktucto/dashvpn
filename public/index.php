<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Api\AuthController;
use App\Api\Middlewares\AuthMiddleware;
use App\Api\Middlewares\PrivateMiddleware;
use App\Api\SystemController;
use App\Api\WireguardRoutes;
use Touch\Application;
use Touch\Core\Kernel;
use Touch\Http\Response;
use Tuupola\Middleware\CorsMiddleware;

$app = Application::create(new Kernel());

$app->route()->middleware(
    new CorsMiddleware(
        $app->getContainer()->get('config')->get('cors'),
    )
);

$app->route()->get("/", fn() => Response::html("Hello world!!!"));

$app->route()->post("/api/login", [AuthController::class, 'login']);
$app->route()
    ->post("/api/register", [AuthController::class, 'register'])
    ->middleware(new PrivateMiddleware($app->getContainer()));

$authMiddleware = $app->getContainer()->make(AuthMiddleware::class);

$app->route()
    ->post("/api/change-password", [AuthController::class, 'changePassword'])
    ->middleware($authMiddleware);

$app->route()
    ->get(
        '/api/system/interfaces',
        [SystemController::class, 'interfaces']
    )
    ->middleware($authMiddleware);

$app->route()
    ->group('/api/wireguard', new WireguardRoutes())
    ->middleware($authMiddleware);

$app->run();
