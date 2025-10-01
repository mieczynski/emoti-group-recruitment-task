<?php

declare(strict_types=1);

namespace App\Infrastructure\Transaction;

use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTransactionManager implements TransactionManagerInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function transactional(callable $callback)
    {
        return $this->em->wrapInTransaction($callback);
    }
}
