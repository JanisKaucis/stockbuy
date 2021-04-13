<?php
namespace App\Repository;

interface MyStocksRepository
{
    public function selectAll(): array;
    public function selectBySymbol($symbol): array;
    public function insert($name,$symbol,$buyPrice,$amount,$logo);
    public function updateAmount($symbol,$amount);
    public function updateCurrentPriceAndEarnings($priceAtBuy,$price,$earnings);
}