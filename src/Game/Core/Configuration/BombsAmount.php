<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Configuration;

final class BombsAmount
{
    public function __construct(public readonly int $value)
    {
        if ($this->value < 2) {
            throw new \InvalidArgumentException(
                'We need to have at two bombs'
            );
        }
    }
}