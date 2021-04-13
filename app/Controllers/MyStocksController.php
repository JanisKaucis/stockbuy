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
use http\Encoding\Stream\Enbrotli;

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
    public function sellStock()
    {
        if (isset($_POST['submit3'])){
            $_SESSION['stock']['sell'] = $_POST['symbol2'];
            $stocks = $this->myStockService->selectBySymbol($_SESSION['stock']['sell']);
            if (!empty($stocks)){
                $price = $this->apiRepository->getSymbolPrice($_SESSION['stock']['sell']);
                $price = $price->getC();
                $this->myStockService->deleteStock($_SESSION['stock']['sell']);
                $value = 0;
                foreach ($stocks as $stock){
                    $value += $price * $stock['amount'];
                }
                $budget = $this->myBudgetService->selectBalance();
                $budget = $budget[0]['budget'];
                $newBudget = $budget+$value;
                $this->myBudgetService->updateBudget($newBudget);
            }else{
                $message = 'Did not found stock';
            }
        }
        $context = [
            'message' => $message,
        ];
    echo $this->twigService->twig->render('sellStockView.twig',$context);
    if (!empty($stocks)){
        $context = [
            'value' => $value,
            'newBudget' => $newBudget
        ];
        echo $this->twigService->twig->render('soldView.twig',$context);
        header('Location: myStocks');
    }
    }
}