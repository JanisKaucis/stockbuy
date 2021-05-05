<?php

namespace App\Services;


use App\Repository\apiRepository;
use App\Repository\MyBalanceRepository;
use App\Repository\MyStocksRepository;

class MyStockService
{
    private apiRepository $apiRepository;
    private MyStocksRepository $myStocksRepository;
    private MyBalanceService $myBudgetService;
    public string $message = '';
    public $stocks;
    public $budget;
    public string $totalEarnings;
    public array $myStockProfile;

    public function __construct(MyStocksRepository $myStocksRepository, MyBalanceRepository $myBalanceRepository)
    {
        $this->apiRepository = new apiRepository();
        $this->myStocksRepository = $myStocksRepository;
        $this->myBudgetService = new MyBalanceService($myBalanceRepository);
    }

    public function selectAll(): array
    {
        return $this->myStocksRepository->selectAll();
    }

    public function selectBySymbol($symbol): array
    {
        return $this->myStocksRepository->selectBySymbol($symbol);
    }

    public function insert($name, $symbol, $buyPrice, $amount, $totalPrice, $logo)
    {
        return $this->myStocksRepository->insert($name, $symbol, $buyPrice, $amount, $totalPrice, $logo);
    }

    public function updateStockPriceAndAmount($priceAtBuy, $amount, $totalPrice, $symbol)
    {
        return $this->myStocksRepository->updateStockPriceAndAmount($priceAtBuy, $amount, $totalPrice, $symbol);
    }

    public function updateAmountAndTotalPrice($symbol, $amount,$totalPrice)
    {
        return $this->myStocksRepository->updateAmountAndTotalPrice($symbol,$amount,$totalPrice);
    }

    public function updateCurrentPriceAndEarnings($priceAtBuy, $price, $earnings)
    {
        return $this->myStocksRepository->updateCurrentPriceAndEarnings($priceAtBuy, $price, $earnings);
    }

    public function deleteStock($symbol)
    {
        return $this->myStocksRepository->deleteStock($symbol);
    }

    public function myStocks()
    {
        $this->budget = $this->myBudgetService->selectBalance();
        $this->budget = $this->budget[0]['budget'];
        $myStockProfile = $this->selectAll();
        $totalEarnings = 0;
        if (!empty($myStockProfile)) {
            foreach ($myStockProfile as $item) {
                $amount = $item['amount'];
                $price = $this->apiRepository->getSymbolPrice($item['symbol']);
                $currentPrice = $price->getC();
                $earnings = ($currentPrice * $amount)- ($item['price_at_buy'] * $amount);
                $earnings = number_format($earnings, 2);
                $totalEarnings += floatval($earnings);
                $this->updateCurrentPriceAndEarnings($item['price_at_buy'], $currentPrice, $earnings);
            }
        }
        $this->totalEarnings = number_format($totalEarnings, 2);
        $this->myStockProfile = $this->selectAll();
    }

    public function sellStocks()
    {
        if (isset($_POST['submit3'])) {
            $_SESSION['stock']['sell'] = $_POST['symbol2'];
            $_SESSION['stock']['sellAmount'] = $_POST['amount'];
            $this->stocks = $this->selectBySymbol($_SESSION['stock']['sell']);
            $value = 0;
            if (!empty($this->stocks)) {
                $price = $this->apiRepository->getSymbolPrice($_SESSION['stock']['sell']);
                $price = $price->getC();

                if ($_SESSION['stock']['sellAmount'] > $this->stocks[0]['amount'] ||
                    $_SESSION['stock']['sellAmount'] <= 0) {
                    $value = 0;
                } elseif ($_SESSION['stock']['sellAmount'] == $this->stocks[0]['amount']) {
                    $this->deleteStock($_SESSION['stock']['sell']);
                    foreach ($this->stocks as $stock) {
                        $value = $price * $stock['amount'];
                    }
                } else {
                    $amount = $this->stocks[0]['amount'] - $_SESSION['stock']['sellAmount'];
                    $totalPrice = $this->stocks[0]['total_price'] -
                        $this->stocks[0]['price_at_buy']*$_SESSION['stock']['sellAmount'];
                    $this->updateAmountAndTotalPrice($_SESSION['stock']['sell'], $amount,$totalPrice);
                        $value = $price * $_SESSION['stock']['sellAmount'];
                }
                if ($value == 0) {
                    $this->message = 'Wrong Amount';
                } else {
                    $budget = $this->myBudgetService->selectBalance();
                    $budget = $budget[0]['budget'];
                    $newBudget = $budget + $value;
                    $this->myBudgetService->updateBudget($newBudget);
                    header('Location: myStocks');
                }
            } else {
                $this->message = 'Did not found stock';
            }
        }
    }
}