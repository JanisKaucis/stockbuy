<?php

namespace App\Controllers;

use App\Repository\apiRepository;
use App\Services\MyBalanceService;
use App\Services\MyStockService;
use App\Services\StocksService;
use App\Services\twigService;

class HomeController
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

    public function searchAndBuyStock()
    {
        echo $this->twigService->twig->render('headerView.twig');
        echo $this->twigService->twig->render('homeView.twig');
        $this->stockService->searchStock();
        $companyProfile = $this->stockService->companyProfile;
        $currentPrice = $this->stockService->currentPrice;
        $companyName = $companyProfile[0]['name'];
        $companySymbol = $companyProfile[0]['symbol'];
        $companyLogo = $companyProfile[0]['logo'];
        if (!empty($companyProfile)) {
            $context = [
                'name' => $companyName,
                'stock' => $currentPrice,
                'logo' => $companyLogo,
                'ticker' => $companySymbol,
            ];
            echo $this->twigService->twig->render('showStockView.twig', $context);
        }else{
            $errorMessage = 'Wrong symbol entered';
            $context = [
                'error' => $errorMessage
                ];
            echo $this->twigService->twig->render('homeErrorView.twig', $context);
        }
        $this->stockService->buyStock();
        $budget = $this->stockService->budget;
        $context = [
            'message' => $_SESSION['stock']['message'],
            'amountMessage' => $_SESSION['stock']['amountMessage'],
            'budget' => $budget
        ];
        if (isset($_POST['submit1']) && !empty($companyProfile)) {
            echo $this->twigService->twig->render('buyStockView.twig', $context);
        }
    }
}