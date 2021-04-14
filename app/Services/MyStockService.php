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
    public string $message;
    public $stocks;
    public string $totalEarnings;
    public array $myStockProfile;

    public function __construct(MyStocksRepository $myStocksRepository,MyBalanceRepository $myBalanceRepository)
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
    public function insert($name,$symbol,$buyPrice,$amount,$logo)
    {
        return $this->myStocksRepository->insert($name,$symbol,$buyPrice,$amount,$logo);
    }
    public function updateCurrentPriceAndEarnings($priceAtBuy,$price,$earnings)
    {
        return $this->myStocksRepository->updateCurrentPriceAndEarnings($priceAtBuy,$price,$earnings);
    }
    public function deleteStock($symbol)
    {
        return $this->myStocksRepository->deleteStock($symbol);
    }
    public function myStocks()
    {
        $myStockProfile = $this->selectAll();
        $totalEarnings = 0;
        if (!empty($myStockProfile)) {
            foreach ($myStockProfile as $item) {
                $price = $this->apiRepository->getSymbolPrice($item['symbol']);
                $currentPrice = $price->getC();
                $earnings = $item['price_at_buy'] - $currentPrice;
                $totalEarnings += $earnings;
                $this->updateCurrentPriceAndEarnings($item['price_at_buy'], $currentPrice, $earnings);
            }
        }
        $this->totalEarnings = number_format($totalEarnings,2);
        $this->myStockProfile = $this->selectAll();
    }
    public function sellStocks()
    {
        if (isset($_POST['submit3'])){
            $_SESSION['stock']['sell'] = $_POST['symbol2'];
            $this->stocks = $this->selectBySymbol($_SESSION['stock']['sell']);
            if (!empty($this->stocks)){
                $price = $this->apiRepository->getSymbolPrice($_SESSION['stock']['sell']);
                $price = $price->getC();
                $this->deleteStock($_SESSION['stock']['sell']);
                $value = 0;
                foreach ($this->stocks as $stock){
                    $value += $price * $stock['amount'];
                }
                $budget = $this->myBudgetService->selectBalance();
                $budget = $budget[0]['budget'];
                $newBudget = $budget+$value;
                $this->myBudgetService->updateBudget($newBudget);
                $this->message = 'Stock sold, you earned: '.$value.PHP_EOL.
                    'New budget: '.$newBudget;
            }else{
                $this->message = 'Did not found stock';
            }
        }
    }
}