<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core;

enum GameState
{
    case NOT_STARTED;
    case STARTED;
    case DEFEATE;
    case WIN;
    case STOP;
}