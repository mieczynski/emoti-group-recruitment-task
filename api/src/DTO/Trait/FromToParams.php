<?php

namespace App\DTO\Trait;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

trait FromToParams
{
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeImmutable $from = null;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeImmutable $to = null;

    #[Assert\Callback]
    public function validateFromToParams(): void
    {
        if ($this->from && $this->to && $this->from >= $this->to) {
            throw new \InvalidArgumentException('from must be earlier than to.');
        }
    }
}
