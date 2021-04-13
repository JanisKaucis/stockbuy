<?php

namespace App\Services;

use App\Repository\MyBalanceRepository;

class MyBalanceService
{
    private $myBalanceRepository;
    public function __construct(MyBalanceRepository $myBalanceRepository)
    {
        $this->myBalanceRepository = $myBalanceRepository;
    }
    public function selectBalance()
    {
        return $this->myBalanceRepository->selectBalance();
    }
    public function updateBudget($budget)
    {
        return $this->myBalanceRepository->updateBudget($budget);
    }
}