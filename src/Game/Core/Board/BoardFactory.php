<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Board;

use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;

final class BoardFactory
{
    public function create(Configuration $configuration, Position $initialPosition): Board
    {
        return new Board($configuration, $initialPosition);
    }

}