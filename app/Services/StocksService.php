<?php
namespace App\Services;


use App\Repository\StocksRepository;

class StocksService
{
    private StocksRepository $stocksRepository;
    public function __construct(StocksRepository $stocksRepository)
    {
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
}