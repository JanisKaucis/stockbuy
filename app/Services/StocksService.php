<?php

namespace App\Services;


use App\Repository\apiRepository;
use App\Repository\MyBalanceRepository;
use App\Repository\MyStocksRepository;
use App\Repository\StocksRepository;

class StocksService
{
    private StocksRepository $stocksRepository;
    private apiRepository $apiRepository;
    private MyBalanceService $myBudgetService;
    public twigService $twigService;
    public MyStockService $myStocksService;
    public $companyProfile = '';
    public $currentPrice;
    public $budget;
    public $maxAmount;

    public function __construct(StocksRepository $stocksRepository, MyBalanceRepository $myBalanceRepository, MyStocksRepository $myStocksRepository)
    {
        $this->twigService = new twigService();
        $this->myStocksService = new MyStockService($myStocksRepository, $myBalanceRepository);
        $this->myBudgetService = new MyBalanceService($myBalanceRepository);
        $this->apiRepository = new apiRepository();
        $this->stocksRepository = $stocksRepository;
    }

    public function selectAll(): array
    {
        return $this->stocksRepository->selectAll();
    }

    public function selectBySymbol($symbol): array
    {
        return $this->stocksRepository->selectBySymbol($symbol);
    }

    public function insert($name, $symbol, $currentPrice, $logo)
    {
        return $this->stocksRepository->insert($name, $symbol, $currentPrice, $logo);
    }

    public function updateCurrentPrice($currentPrice, $symbol)
    {
        return $this->stocksRepository->updateCurrentPrice($currentPrice, $symbol);
    }

    public function searchStock()
    {
        $this->budget = $this->myBudgetService->selectBalance();
        $this->budget = $this->budget[0]['budget'];
        if (isset($_POST['submit1'])) {
            $_SESSION['stock']['symbol'] = $_POST['symbol'];
            $stock = $this->apiRepository->getSymbolPrice($_SESSION['stock']['symbol']);
            if ($stock->getC() != 0) {
                $this->currentPrice = $stock->getC();
                $_SESSION['stock']['price'] = $this->currentPrice;
                if (empty($this->selectBySymbol($_SESSION['stock']['symbol']))) {
                    $companyProfile = $this->apiRepository->getCompanyProfile($_SESSION['stock']['symbol']);
                    $companyName = $companyProfile->getName();
                    $companySymbol = $companyProfile->getTicker();
                    $companyLogo = $companyProfile->getLogo();
                    $this->insert($companyName, $companySymbol, $this->currentPrice, $companyLogo);
                } else {
                    $this->updateCurrentPrice($this->currentPrice, $_SESSION['stock']['symbol']);
                }
                $this->companyProfile = $this->selectBySymbol($_SESSION['stock']['symbol']);
                $_SESSION['stock']['profile'] = $this->companyProfile;
            }
        }
    }

    public function buyStock()
    {
        $_SESSION['stock']['amountMessage'] = '';
        $_SESSION['stock']['message'] = '';
        if (!empty($_SESSION['stock']['profile'])) {
            $this->companyProfile = $_SESSION['stock']['profile'];
            $this->maxAmount = floor($this->budget / $this->companyProfile[0]['current_price']);
            $leftBudget = $this->budget - $this->maxAmount * $this->companyProfile[0]['current_price'];
            $_SESSION['stock']['message'] = 'You can buy up to ' . $this->maxAmount . ' stocks' . PHP_EOL . 'with ' .
                $leftBudget . ' budget left.';
            if (isset($_POST['submit2'])) {
                if (empty($_POST['amount'])) {
                    $_SESSION['stock']['amountMessage'] = 'Please enter amount which is not zero';
                } else {
                    $_SESSION['stock']['amount'] = $_POST['amount'];
                    if (isset($_SESSION['stock']['amount']) && $_SESSION['stock']['amount'] > $this->maxAmount ||
                        $_SESSION['stock']['amount'] <= 0) {
                        $_SESSION['stock']['amountMessage'] = 'You cannot buy ' . $_SESSION['stock']['amount'] .
                            ' stocks';
                    } else {
                        $buyPrice = $this->companyProfile[0]['current_price'];
                        $amount = $_SESSION['stock']['amount'];
                        $this->budget = $this->budget - $amount * $buyPrice;
                        $this->myBudgetService->updateBudget($this->budget);

                        $companyName = $this->companyProfile[0]['name'];
                        $companySymbol = $this->companyProfile[0]['symbol'];
                        $companyLogo = $this->companyProfile[0]['logo'];
                        $companyTotalPrice = $buyPrice * $amount;

                        $myStocks = $this->myStocksService->selectAll();
                        $insertHint = 0;
                        if (!empty($myStocks)) {
                            foreach ($myStocks as $stock) {
                                if ((in_array($companySymbol, $stock))) {
                                    $insertHint++;
                                }
                                if ($stock['symbol'] == $companySymbol) {
                                    $amount = $amount + $stock['amount'];
                                    $totalPrice = $companyTotalPrice + $stock['total_price'];
                                    $priceAtBuy = $totalPrice / $amount;
                                    $this->myStocksService->updateStockPriceAndAmount($priceAtBuy, $amount, $totalPrice, $companySymbol);
                                }
                            }
                            if ($insertHint == 0) {
                                $this->myStocksService->insert($companyName, $companySymbol, $buyPrice, $amount,
                                    $companyTotalPrice, $companyLogo);
                            }
                        } else {
                            $this->myStocksService->insert($companyName, $companySymbol, $buyPrice, $amount,
                                $companyTotalPrice, $companyLogo);
                        }
                        header('Location: myStocks');
                    }
                }
            }
        }
    }
}