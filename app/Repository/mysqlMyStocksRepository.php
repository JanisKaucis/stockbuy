<?php

namespace App\Repository;

use Medoo\Medoo;
class mysqlMyStocksRepository implements MyStocksRepository
{
    private Medoo $database;
    public function __construct()
    {
        $this->database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'stockBuy',
            'server' => 'localhost',
            'username' => 'root',
            'password' => ''
        ]);
    }
    public function selectAll(): array
    {
        return $this->database->select('MyStocks','*');
    }
    public function selectBySymbol($symbol): array
    {
        return $this->database->select('MyStocks','*',['symbol' => $symbol]);
    }
    public function insert($name,$symbol,$buyPrice,$amount,$totalPrice,$logo)
    {
        $this->database->insert('MyStocks',['name' => $name, 'symbol' => $symbol,
            'price_at_buy' => $buyPrice,'amount' => $amount, 'total_price' => $totalPrice,
            'logo' => $logo]);
    }
    public function updateStockPriceAndAmount($priceAtBuy,$amount,$totalPrice,$symbol)
    {
        $this->database->update('MyStocks',['price_at_buy' => $priceAtBuy,'amount' => $amount,
            'total_price' => $totalPrice], ['symbol' => $symbol]);
    }
    public function updateAmountAndTotalPrice($symbol,$amount,$totalPrice)
    {
        $this->database->update('MyStocks',['amount' => $amount,'total_price' => $totalPrice],
            ['symbol' => $symbol]);
    }
    public function updateCurrentPriceAndEarnings($priceAtBuy,$price,$earnings)
    {
        $this->database->update('MyStocks',['current_price' => $price,'earnings' => $earnings],['price_at_buy' => $priceAtBuy]);
    }
    public function deleteStock($symbol)
    {
        $this->database->delete('MyStocks',['symbol' => $symbol]);
    }
}