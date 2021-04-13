<?php
namespace App\Services;


use App\Repository\MyStocksRepository;
use App\Repository\StocksRepository;

class MyStockService
{
    private MyStocksRepository $myStocksRepository;
    public function __construct(MyStocksRepository $myStocksRepository)
    {
        $this->myStocksRepository = $myStocksRepository;
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
}