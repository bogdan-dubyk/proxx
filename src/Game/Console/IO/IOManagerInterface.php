<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Console\IO;

use BohdanDubyk\Proxx\Game\Core\Board\Board;

interface IOManagerInterface
{
    public function start(): void;

    public function victory(): void;

    public function defeated(): void;

    public function error(string $message): void;

    public function warning(string $message): void;

    public function askForWidth(): IntegerResult;

    public function askForHeight(): IntegerResult;

    public function askForBombsAmount(): IntegerResult;

    public function renderBoard(Board $board): void;

    public function askForClickPosition(): StringResult;

    public function confirmReplay(): bool;

    public function confirmReconfiguration(): bool;

    public function boardBroken(): void;
}