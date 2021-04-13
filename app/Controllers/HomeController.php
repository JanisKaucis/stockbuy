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

class HomeController
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

    public function searchStock()
    {
        echo $this->twigService->twig->render('headerView.twig');
        echo $this->twigService->twig->render('homeView.twig');
        if (isset($_POST['submit1'])) {
            $_SESSION['stock']['symbol'] = $_POST['symbol'];
            $stock = $this->apiRepository->getSymbolPrice($_SESSION['stock']['symbol']);
            if (!empty($stock)) {
                $currentPrice = $stock->getC();
                if (empty($this->stockService->selectBySymbol($_SESSION['stock']['symbol']))) {
                    $companyProfile = $this->apiRepository->getCompanyProfile($_SESSION['stock']['symbol']);
                    $companyName = $companyProfile->getName();
                    $companySymbol = $companyProfile->getTicker();
                    $companyLogo = $companyProfile->getLogo();
                    $this->stockService->insert($companyName, $companySymbol, $currentPrice, $companyLogo);
                }else{
                    $this->stockService->updateCurrentPrice($currentPrice,$_SESSION['stock']['symbol']);
                }
            } else {
                $errorMessage = 'Wrong symbol entered';
            }
            $companyProfile = $this->stockService->selectBySymbol($_SESSION['stock']['symbol']);
            $companyName = $companyProfile[0]['name'];
            $companySymbol = $companyProfile[0]['symbol'];
            $companyLogo = $companyProfile[0]['logo'];
        }
        if (!empty($companyProfile)) {
            $context = [
                'name' => $companyName,
                'stock' => $currentPrice,
                'logo' => $companyLogo,
                'ticker' => $companySymbol,
                'error' => $errorMessage
            ];
            echo $this->twigService->twig->render('showStockView.twig', $context);
        } else {
            echo 'No stock found';
        }
    }

    public function buyStock()
    {

        $budget = $this->myBudgetService->selectBalance();
        $budget = $budget[0]['budget'];
        $companyProfile = $this->stockService->selectBySymbol($_SESSION['stock']['symbol']);
        if (isset($_POST['submit1']) && $companyProfile[0]['current_price'] > $budget) {
            $_SESSION['stock']['message'] = 'You cannot afford this stock';
            $context = [
                'message' => $_SESSION['stock']['message'],
                'budget' => $budget
            ];
            echo $this->twigService->twig->render('validateBudgetView.twig', $context);
        } else {
            $maxAmount = floor($budget / $companyProfile[0]['current_price']);
            $leftBudget = $budget - $maxAmount * $companyProfile[0]['current_price'];
            $_SESSION['stock']['message'] = 'You can buy up to ' . $maxAmount . ' stocks' . PHP_EOL . 'with ' .
                $leftBudget . ' budget left.';
            if (isset($_POST['submit2'])) {
                $_SESSION['stock']['amount'] = $_POST['amount'];
                if ($_SESSION['stock']['amount'] > $maxAmount) {
                    $_SESSION['stock']['amountMessage'] = 'You cannot buy ' . $_SESSION['stock']['amount'] . ' stocks';
                } else {
                    $buyPrice = $companyProfile[0]['current_price'];
                    $amount = $_SESSION['stock']['amount'];
                    $budget = $budget - $amount * $buyPrice;
                    $this->myBudgetService->updateBudget($budget);

                        $companyName = $companyProfile[0]['name'];
                        $companySymbol = $companyProfile[0]['symbol'];
                        $companyLogo = $companyProfile[0]['logo'];
                        $this->myStockService->insert($companyName, $companySymbol, $buyPrice, $amount, $companyLogo);
                    header('Location: myStocks');
                }
            }
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