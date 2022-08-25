<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Configuration;

final class Configuration
{
    public function __construct(
        public readonly Width $width,
        public readonly Height $height,
        public readonly BombsAmount $bombsAmount
    ) {
        $maximumAllowedBombsAmount = ($width->value * $height->value) -9;
        if ($bombsAmount->value < 1 || $bombsAmount->value > $maximumAllowedBombsAmount) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Numebr of bombs in the board can\'t be less than 1 or bigger than %d. Please re-configure the board',
                    $maximumAllowedBombsAmount
                )
            );
        }
    }
}