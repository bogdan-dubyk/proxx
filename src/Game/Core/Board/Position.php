<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Board;

use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;

final class Position
{
    public function __construct(public readonly int $x, public readonly int $y, Configuration $configuration)
    {
        if ($x < 0 || $x > $configuration->width->value -1) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The X axis value must be inside the board. Between 1 and %d',
                    $configuration->width->value - 1
                )
            );
        }

        if ($y < 0 || $y> $configuration->height->value - 1) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The Y axis value must be inside the board. Between 1 and %d',
                    $configuration->height->value -1
                )
            );
        }
    }

    public function isEqual(Position $position): bool
    {
        return $this->x === $position->x && $this->y === $position->y;
    }
}