<?php
declare(strict_types=1);

namespace App\DTO\Availability;

use App\DTO\Trait\FromToParams;
use App\DTO\Trait\RoomType;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

class AvailabilityListParamsDTO
{
    use FromToParams;
    use RoomType;

    #[Assert\NotNull]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeImmutable $from = null;

    #[Assert\NotNull]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeImmutable $to = null;

    #[Assert\Positive]
    public ?int $nights = null;

    #[Assert\Positive]
    public int $minCapacity = 1;

    #[Assert\Callback]
    public function validate(): void
    {
        if ($this->nights !== null && $this->nights > 180) {
            throw new \InvalidArgumentException('nights too large.');
        }
    }
}
