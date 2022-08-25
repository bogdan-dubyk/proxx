<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core;

use BohdanDubyk\Proxx\Game\Core\Board\Board;
use BohdanDubyk\Proxx\Game\Core\Board\BoardFactory;
use BohdanDubyk\Proxx\Game\Core\Board\Position;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;

final class Engine
{
    private GameState $state;
    private ?Board $board;

    public function __construct(private Configuration $configuration, private readonly BoardFactory $boardFactory)
    {
        $this->state = GameState::NOT_STARTED;
    }

    /**
     * This use this method to start the game
     *
     * @param Position $position
     * @return GameState
     */
    public function start(Position $position): GameState
    {
        if ($this->state === GameState::STOP) {
            throw new GameException('Game exited, please run new instance of the game');
        }

        if ($this->state === GameState::STARTED) {
            throw new GameException(
                sprintf(
                    'Invalid game state [%s] for running method [start]. Allowed states: [%s, %s, %s]',
                    $this->state->name,
                    GameState::WIN->name,
                    GameState::DEFEATE->name,
                    GameState::NOT_STARTED->name,
                )
            );
        }

        $this->board = $this->boardFactory->create($this->configuration, $position);
        $this->state = $this->board->getState();

        return $this->state;
    }

    /**
     * Use this method to make a next click
     *
     * @param Position $position
     * @return GameState
     */
    public function next(Position $position): GameState
    {
        if ($this->state === GameState::STOP) {
            throw new GameException('Game exited, please run new instance of the game');
        }

        if ($this->state !== GameState::STARTED) {
            throw new GameException(
                sprintf(
                    'Invalid game state [%s]. Allowed states [%s]',
                    $this->state->name,
                    GameState::STARTED->name
                )
            );
        }

        $this->board->revealFieldInPosition($position);
        $this->state = $this->board->getState();

        return $this->state;
    }

    /**
     * Restart the game, if configuration provided it's mean we need restart it with new board configurations
     *
     * @param Configuration|null $newConfiguration
     * @return GameState
     */
    public function restart(?Configuration $newConfiguration): GameState
    {
        if ($this->state === GameState::STOP) {
            throw new GameException('Game exited, please run new instance of the game');
        }

        $this->state = GameState::NOT_STARTED;
        if ($newConfiguration !== null) {
            $this->configuration = $newConfiguration;
        }

        $this->board = null;

        return $this->state;
    }

    /**
     * Call it when you want to exit the game
     * @return GameState
     */
    public function stop(): GameState
    {
        $this->state = GameState::STOP;

        return $this->state;
    }

    public function getBoard(): ?Board
    {
        if ($this->state === GameState::STOP) {
            throw new GameException('Game exited, please run new instance of the game');
        }

        return $this->board;
    }
}