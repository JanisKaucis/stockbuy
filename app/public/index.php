<?php
require_once '../../vendor/autoload.php';
use App\Controllers\HomeController;
use App\Controllers\MyStocksController;

session_start();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute(['GET','POST'], '/', [HomeController::class, 'searchStock','buyStock']);
    $r->addRoute(['GET','POST'],'/myStocks',[MyStocksController::class,'myStocks','sellStock']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $controller = $handler[0];
        $class = new $controller;
        $firstFunction = $handler[1];
        $class->$firstFunction();
if (!empty($handler[2])){
    $secondFunction = $handler[2];
    $class->$secondFunction();
}
        // ... call $handler with $vars
        break;
}

if ($httpMethod == 'GET' && isset($_SESSION['stock'])) {
    unset($_SESSION['stock']);
}