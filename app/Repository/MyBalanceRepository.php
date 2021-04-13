<?php

namespace App\Repository;

interface MyBalanceRepository
{
    public function selectBalance(): array;
    public function updateBudget($budget);
}