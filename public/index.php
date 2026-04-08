<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Api\WireguardRoutes;
use Touch\Application;
use Touch\Core\Kernel;
use Touch\Http\Response;

$app = Application::create(new Kernel());

$app->route()->get("/", fn() => Response::html("Hello world!!!"));
$app->route()->group('/api/wireguard', new WireguardRoutes());
$app->run();
