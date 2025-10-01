<?php

namespace App\Infrastructure\Transaction;

interface TransactionManagerInterface
{
    public function transactional(callable $callback);
}
