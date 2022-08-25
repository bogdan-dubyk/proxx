<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Configuration;

final class Height
{
    public function __construct(public readonly int $value)
    {
        if ($this->value < 5 || $this->value > 40) {
            throw new \InvalidArgumentException('Invalid height provided. Value should be between 5 and 40');
        }
    }
}