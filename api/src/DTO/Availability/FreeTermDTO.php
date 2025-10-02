<?php
declare(strict_types=1);

namespace App\DTO\Availability;

use Symfony\Component\Serializer\Annotation\Groups;

class FreeTermDTO
{
    #[Groups(['availability:read'])]
    public string $startDate;

    #[Groups(['availability:read'])]
    public string $endDate;

    #[Groups(['availability:read'])]
    public int $nights;

    #[Groups(['availability:read'])]
    public string $totalPrice;

    public function __construct(\DateTimeImmutable $start, \DateTimeImmutable $end, int $nights, string $totalPrice)
    {
        $this->startDate = $start->format('Y-m-d');
        $this->endDate   = $end->format('Y-m-d');
        $this->nights    = $nights;
        $this->totalPrice = $totalPrice;
    }
}
