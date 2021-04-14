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
    public $errorMessage;
    public $companyProfile;
    public $currentPrice;
    public $budget;

    public function __construct(StocksRepository $stocksRepository,MyBalanceRepository $myBalanceRepository,MyStocksRepository $myStocksRepository)
    {
        $this->twigService = new twigService();
        $this->myStocksService = new MyStockService($myStocksRepository,$myBalanceRepository);
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
    public function updateCurrentPrice($currentPrice,$symbol)
    {
        return $this->stocksRepository->updateCurrentPrice($currentPrice,$symbol);
    }
    public function searchStock()
    {
        if (isset($_POST['submit1'])) {
            $_SESSION['stock']['symbol'] = $_POST['symbol'];
            $stock = $this->apiRepository->getSymbolPrice($_SESSION['stock']['symbol']);
            if (!empty($stock)) {
                $this->currentPrice = $stock->getC();
                if (empty($this->selectBySymbol($_SESSION['stock']['symbol']))) {
                    $companyProfile = $this->apiRepository->getCompanyProfile($_SESSION['stock']['symbol']);
                    $companyName = $companyProfile->getName();
                    $companySymbol = $companyProfile->getTicker();
                    $companyLogo = $companyProfile->getLogo();
                    $this->insert($companyName, $companySymbol, $this->currentPrice, $companyLogo);
                }else{
                    $this->updateCurrentPrice($this->currentPrice,$_SESSION['stock']['symbol']);
                }
            }
            $this->companyProfile = $this->selectBySymbol($_SESSION['stock']['symbol']);
        }
    }
    public function buyStock()
    {
        $this->budget = $this->myBudgetService->selectBalance();
        $this->budget = $this->budget[0]['budget'];
        $companyProfile = $this->selectBySymbol($_SESSION['stock']['symbol']);
        if (isset($_POST['submit1']) && $companyProfile[0]['current_price'] > $this->budget) {
            $_SESSION['stock']['message'] = 'You cannot afford this stock';

        } else {
            $maxAmount = floor($this->budget / $companyProfile[0]['current_price']);
            $leftBudget = $this->budget - $maxAmount * $companyProfile[0]['current_price'];
            $_SESSION['stock']['message'] = 'You can buy up to ' . $maxAmount . ' stocks' . PHP_EOL . 'with ' .
                $leftBudget . ' budget left.';
            if (isset($_POST['submit2'])) {
                $_SESSION['stock']['amount'] = $_POST['amount'];
                if ($_SESSION['stock']['amount'] > $maxAmount) {
                    $_SESSION['stock']['amountMessage'] = 'You cannot buy ' . $_SESSION['stock']['amount'] . ' stocks';
                } else {
                    $buyPrice = $companyProfile[0]['current_price'];
                    $amount = $_SESSION['stock']['amount'];
                    $this->budget = $this->budget - $amount * $buyPrice;
                    $this->myBudgetService->updateBudget($this->budget);

                    $companyName = $companyProfile[0]['name'];
                    $companySymbol = $companyProfile[0]['symbol'];
                    $companyLogo = $companyProfile[0]['logo'];
                    $this->myStocksService->insert($companyName, $companySymbol, $buyPrice, $amount, $companyLogo);
                    header('Location: myStocks');
                }
            }
        }
    }
}