<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Console;

use BohdanDubyk\Proxx\Game\Core\Board\Position;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Console\IO\StringResult;

final class Click
{
    private function __construct(public readonly Position $position)
    {
    }

    public static function fromInput(StringResult $intpuValue, Configuration $configuration): self
    {
        $coordinates = explode(' ', $intpuValue->value);
        if (\count($coordinates) !== 2) {
            throw new \InvalidArgumentException(
                'Invalid position provided. Expected values are space separated integers.'
            );
        }

        [$x, $y] = $coordinates;
        if (!\ctype_digit($x) || !\ctype_digit($y)) {
            throw new \InvalidArgumentException(
                'Invalid position provided. Expected values are space separated integers'
            );
        }

        return new self(new Position((int) $x - 1, (int) $y - 1, $configuration));
    }
}