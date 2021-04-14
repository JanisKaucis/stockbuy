<?php

namespace App\Controllers;

use App\Repository\apiRepository;
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

    public function __construct(StocksService $stockService,MyStockService $myStockService,MyBalanceService $myBudgetService)
    {
        $this->apiRepository = new apiRepository();
        $this->twigService = new twigService();
        $this->stockService = $stockService;
        $this->myStockService = $myStockService;
        $this->myBudgetService = $myBudgetService;
    }

    public function myStocks()
    {
        $this->myStockService->myStocks();
        $this->myStockService->sellStocks();
        $myStockProfile = $this->myStockService->myStockProfile;
        $totalEarnings = $this->myStockService->totalEarnings;
        if (isset($_POST['submit3'])) {
            $message = $this->myStockService->message;
        }

        echo $this->twigService->twig->render('headerView.twig');
        $context = [
            'myStocks' => $myStockProfile,
            'totalEarnings' => $totalEarnings
        ];
        echo $this->twigService->twig->render('myStocksView.twig', $context);
            $context = [
                'message' => $message,
            ];
            echo $this->twigService->twig->render('sellStockView.twig', $context);
    }
}