<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Configuration;

final class Width
{
    public function __construct(public readonly int $value)
    {
        if ($this->value < 5 || $this->value > 40) {
            throw new \InvalidArgumentException('Invalid width provided. Value should be between 5 and 40');
        }
    }
}