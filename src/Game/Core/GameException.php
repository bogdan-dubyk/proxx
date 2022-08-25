<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core;

final class GameException extends \RuntimeException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct(
            $message,
            0,
            $previous
        );
    }
}