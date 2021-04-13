<?php

namespace App\Repository;

use Medoo\Medoo;

class mysqlMyBalanceRepository implements MyBalanceRepository
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
    public function selectBalance(): array
    {
        return $this->database->select('MyBalance','*');
    }
    public function updateBudget($budget)
    {
        $this->database->update('MyBalance',['budget' => $budget]);
    }
}