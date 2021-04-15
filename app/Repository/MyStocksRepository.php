<?php
namespace App\Repository;

interface MyStocksRepository
{
    public function selectAll(): array;
    public function selectBySymbol($symbol): array;
    public function insert($name,$symbol,$buyPrice,$amount,$totalPrice,$logo);
    public function updateStockPriceAndAmount($priceAtBuy,$amount,$totalPrice,$symbol);
    public function updateAmountAndTotalPrice($symbol,$amount,$totalPrice);
    public function updateCurrentPriceAndEarnings($priceAtBuy,$price,$earnings);
    public function deleteStock($symbol);
}