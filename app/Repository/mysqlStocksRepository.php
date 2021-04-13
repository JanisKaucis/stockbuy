<?php

namespace App\Repository;

use Medoo\Medoo;

class mysqlStocksRepository implements StocksRepository
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
        return $this->database->select('stocks', '*');
    }

    public function selectBySymbol($symbol): array
    {
        return $this->database->select('stocks', '*', ['symbol' => $symbol]);
    }

    public function insert($name, $symbol, $currentPrice, $logo)
    {
        $this->database->insert('stocks', ['name' => $name, 'symbol' => $symbol,
            'current_price' => $currentPrice, 'logo' => $logo]);
    }
    public function updateCurrentPrice($currentPrice,$symbol)
    {
        $this->database->update('stocks',['current_price' => $currentPrice],['symbol' => $symbol]);
    }
}