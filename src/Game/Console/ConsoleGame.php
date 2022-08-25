<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Console;

use BohdanDubyk\Proxx\Game\Core\Board\BoardFactory;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use BohdanDubyk\Proxx\Game\Core\Configuration\Height;
use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use BohdanDubyk\Proxx\Game\Core\Engine;
use BohdanDubyk\Proxx\Game\Core\GameException;
use BohdanDubyk\Proxx\Game\Core\GameState;
use BohdanDubyk\Proxx\Game\Console\IO\IOManagerInterface;

final class ConsoleGame
{
    private Engine $engine;
    private Configuration $configuration;

    public function __construct(private readonly IOManagerInterface $io, BoardFactory $boardFactory)
    {
        $this->configuration = $this->buildConfig();
        $this->engine = new Engine($this->configuration, $boardFactory);
    }

    public function runGame(?GameState $state = null): ?GameState
    {
        try {
            return match ($state) {
                null, GameState::NOT_STARTED => $this->runGame($this->start()),
                GameState::STARTED => $this->runGame($this->next()),
                GameState::DEFEATE => $this->runGame($this->defeat()),
                GameState::WIN => $this->runGame($this->win()),
                GameState::STOP => null
            };
        } catch (GameException) {
            $this->io->boardBroken();
            return $this->runGame($this->restart());
        }
    }

    public function start(): GameState
    {
        $this->io->start();
        $nextState = $this->engine->start($this->makeClick()->position);
        $this->io->renderBoard($this->engine->getBoard());

        return $nextState;
    }

    public function next(): GameState
    {
        $nextState = $this->engine->next($this->makeClick()->position);
        $this->io->renderBoard($this->engine->getBoard());

        return $nextState;
    }

    public function defeat(): GameState
    {
        $this->io->defeated();
        return $this->restart();
    }

    public function win(): GameState
    {
        $this->io->victory();
        return $this->restart();
    }

    public function restart(): GameState
    {
        if ($this->io->confirmReplay()) {
            if ($this->io->confirmReconfiguration()) {
                $this->configuration = $this->buildConfig();
            }

            return $this->engine->restart($this->configuration);
        }

        return $this->engine->stop();
    }

    private function makeClick(): Click
    {
        try {
            return Click::fromInput(
                $this->io->askForClickPosition(),
                $this->configuration
            );

        } catch (\InvalidArgumentException $exception) {
            $this->io->error($exception->getMessage());
            return $this->makeClick();
        }
    }

    private function buildConfig(
        ?Width $width = null,
        ?Height $height = null,
        ?BombsAmount $bombsAmount = null
    ): Configuration {
        try {
            $width = $width ?? new Width($this->io->askForWidth()->value);
            $height = $height ?? new Height($this->io->askForHeight()->value);
            $bombsAmount = $bombsAmount ?? new BombsAmount($this->io->askForBombsAmount()->value);

            return new Configuration($width, $height, $bombsAmount);
        } catch (\InvalidArgumentException $exception) {
            $this->io->error($exception->getMessage());
            return $this->buildConfig($width, $height, $bombsAmount);
        }
    }
}