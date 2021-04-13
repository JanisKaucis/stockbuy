<?php
namespace App\Repository;

interface StocksRepository
{
    public function selectAll(): array;
    public function selectBySymbol($symbol): array;
    public function insert($name, $symbol, $currentPrice, $logo);
    public function updateCurrentPrice($currentPrice,$symbol);
}