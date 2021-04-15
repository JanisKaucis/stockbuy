<?php
require_once '../vendor/autoload.php';
use App\Controllers\HomeController;
use App\Controllers\MyStocksController;
use App\Repository\mysqlMyBalanceRepository;
use App\Repository\mysqlMyStocksRepository;
use App\Repository\mysqlStocksRepository;
use App\Services\MyBalanceService;
use App\Services\MyStockService;
use App\Services\StocksService;
use League\Container\Container;
use App\Repository\StocksRepository;
use App\Repository\MyStocksRepository;
use App\Repository\MyBalanceRepository;

session_start();

//container

$container = new Container();
$container->add(StocksRepository::class, mysqlStocksRepository::class);
$container->add(MyStocksRepository::class,mysqlMyStocksRepository::class);
$container->add(MyBalanceRepository::class, mysqlMyBalanceRepository::class);

$container->add(StocksService::class,StocksService::class)
    ->addArguments([StocksRepository::class,MyBalanceRepository::class,MyStocksRepository::class]);
$container->add(MyStockService::class,MyStockService::class)
    ->addArguments([MyStocksRepository::class,MyBalanceRepository::class]);
$container->add(MyBalanceService::class,MyBalanceService::class)
    ->addArgument(MyBalanceRepository::class);

$container->add(HomeController::class,HomeController::class)
    ->addArguments([StocksService::class,MyStockService::class,MyBalanceService::class]);
$container->add(MyStocksController::class,MyStocksController::class)
    ->addArguments([StocksService::class,MyStockService::class,MyBalanceService::class]);


$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute(['GET','POST'], '/', [HomeController::class, 'searchAndBuyStock']);
    $r->addRoute(['GET','POST'],'/myStocks',[MyStocksController::class,'myStocks']);
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
        [$controller,$method]= $handler;

        // ... call $handler with $vars
        echo $container->get($controller)->$method($vars);
        break;
}

if ($httpMethod == 'GET' && isset($_SESSION['stock'])) {
    unset($_SESSION['stock']);
}