<?php
declare(strict_types=1);

namespace App\Http\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class FromQuery
{
    public function __construct(
        public array $defaults = []
    ) {}
}
