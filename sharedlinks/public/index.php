<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Api\Controllers\PeerController;
use App\Api\Middlewares\PrivateMiddleware;
use App\Web\Controllers\HomeController;
use Touch\Application;
use Touch\Core\Kernel;

$app = Application::create(new Kernel());

$app->route()->get("/{slug}", [HomeController::class, 'index']);
$app->route()->post("/{slug}", [HomeController::class, 'peer']);

$app->route()
    ->post("/api/peer/link", [PeerController::class, 'link'])
    ->middleware(new PrivateMiddleware($app->getContainer()));

$app->run();
