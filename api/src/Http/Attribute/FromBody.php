<?php
declare(strict_types=1);

namespace App\Http\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class FromBody
{
    public function __construct(
        public bool $allowExtraFields = false
    ) {}
}
