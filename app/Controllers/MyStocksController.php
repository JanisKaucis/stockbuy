<?php

namespace App\Controllers;

use App\Repository\apiRepository;
use App\Repository\mysqlMyBalanceRepository;
use App\Repository\mysqlMyStocksRepository;
use App\Repository\mysqlStocksRepository;
use App\Services\MyBalanceService;
use App\Services\MyStockService;
use App\Services\StocksService;
use App\Services\twigService;

class MyStocksController
{
    public apiRepository $apiRepository;
    public twigService $twigService;
    public StocksService $stockService;
    public MyStockService $myStockService;
    public MyBalanceService $myBudgetService;

    public function __construct()
    {
        $this->apiRepository = new apiRepository();
        $this->twigService = new twigService();
        $this->stockService = new StocksService(new mysqlStocksRepository());
        $this->myStockService = new MyStockService(new mysqlMyStocksRepository());
        $this->myBudgetService = new MyBalanceService(new mysqlMyBalanceRepository());
    }

    public function myStocks()
    {
        echo $this->twigService->twig->render('headerView.twig');
        $myStockProfile = $this->myStockService->selectAll();
        $myBudget = $this->myBudgetService->selectBalance();
        $myBudget = $myBudget[0]['budget'];
        $totalEarnings = 0;
        if (!empty($myStockProfile)) {
            foreach ($myStockProfile as $item) {
                $price = $this->apiRepository->getSymbolPrice($item['symbol']);
                $currentPrice = $price->getC();
                $earnings = $item['price_at_buy'] - $currentPrice;
                $totalEarnings += $earnings;
                $this->myStockService->updateCurrentPriceAndEarnings($item['price_at_buy'], $currentPrice, $earnings);
            }
        }
        $totalEarnings = number_format($totalEarnings,2);
        $myStockProfile = $this->myStockService->selectAll();
        $context = [
            'myStocks' => $myStockProfile,
            'myBudget' => $myBudget,
            'totalEarnings' => $totalEarnings
        ];
        echo $this->twigService->twig->render('myStocksView.twig', $context);
    }
}