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

    public function __construct(StocksService $stockService, MyStockService $myStockService, MyBalanceService $myBudgetService)
    {
        $this->apiRepository = new apiRepository();
        $this->twigService = new twigService();
        $this->stockService = $stockService;
        $this->myStockService = $myStockService;
        $this->myBudgetService = $myBudgetService;
    }

    public function searchAndBuyStock()
    {

        $this->stockService->searchStock();
        $budget = $this->stockService->budget;
        $companyProfile = $this->stockService->companyProfile;
//        var_dump($companyProfile);
        $currentPrice = $this->stockService->currentPrice;
        $companyName = $companyProfile[0]['name'];
        $companySymbol = $companyProfile[0]['symbol'];
        $companyLogo = $companyProfile[0]['logo'];

        echo $this->twigService->twig->render('headerView.twig');
        $home = [
            'budget' => $budget
        ];
        echo $this->twigService->twig->render('homeView.twig',$home);

        if (isset($_POST['submit1']) && empty($companyProfile)) {
            $errorMessage = 'Wrong symbol';
            $context = [
                'error' => $errorMessage
            ];
            echo $this->twigService->twig->render('homeErrorView.twig', $context);
        }
        if(!empty($companyProfile)){
            $context = [
                'name' => $companyName,
                'stock' => $currentPrice,
                'logo' => $companyLogo,
                'ticker' => $companySymbol,
            ];
            echo $this->twigService->twig->render('showStockView.twig', $context);
        }
        $this->stockService->buyStock();
        if (isset($_POST['submit1']) && $companyProfile[0]['current_price'] > $budget) {
            $_SESSION['stock']['message'] = 'You cannot afford this stock';
            $context = [
                'error' => $_SESSION['stock']['message'],
            ];
            echo $this->twigService->twig->render('homeErrorView.twig', $context);
        } else {
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
}