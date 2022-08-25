<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use BohdanDubyk\Proxx\Game\Core\Board\BoardFactory;
use BohdanDubyk\Proxx\Game\Core\Board\Position;
use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\Configuration\Height;
use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use BohdanDubyk\Proxx\Game\Core\Engine;
use BohdanDubyk\Proxx\Game\Core\GameException;
use BohdanDubyk\Proxx\Game\Core\GameState;
use Tests\TestCase;

final class EngineTest extends TestCase
{
    public function testStartReturnCorrectState(): void
    {
        $config = new Configuration(
            new Width(5),
            new Height(5),
            new BombsAmount(3)
        );
        $engine = new Engine($config, new BoardFactory());

        self::assertSame(GameState::STARTED, $engine->start(new Position(3, 3, $config)));
    }

    public function testStartFaildIfStarted(): void
    {
        $config = new Configuration(
            new Width(5),
            new Height(5),
            new BombsAmount(3)
        );
        $engine = new Engine($config, new BoardFactory());

        $this->expectException(GameException::class);
        $engine->start(new Position(3, 3, $config));
        $engine->start(new Position(3, 3, $config));
    }

    public function testNextIfNotStarted(): void
    {
        $config = new Configuration(
            new Width(5),
            new Height(5),
            new BombsAmount(3)
        );
        $engine = new Engine($config, new BoardFactory());

        $this->expectException(GameException::class);
        $engine->next(new Position(3, 3, $config));
    }

    public function testStopReturnStopState(): void
    {
        $config = new Configuration(
            new Width(5),
            new Height(5),
            new BombsAmount(3)
        );
        $engine = new Engine($config, new BoardFactory());

        self::assertSame(GameState::STOP, $engine->stop());
    }

    public function testRestartStartedGame(): void
    {
        $config = new Configuration(
            new Width(5),
            new Height(5),
            new BombsAmount(3)
        );
        $engine = new Engine($config, new BoardFactory());

        $started = $engine->start(new Position(3, 3, $config));
        self::assertNotSame(GameState::NOT_STARTED, $started);
        self::assertSame(GameState::NOT_STARTED,  $engine->restart($config));
    }

    public function testNextIfRestarted(): void
    {
        $config = new Configuration(
            new Width(5),
            new Height(5),
            new BombsAmount(3)
        );
        $engine = new Engine($config, new BoardFactory());

        $engine->start(new Position(3, 3, $config));
        $engine->next(new Position(3, 3, $config));

        $this->expectException(GameException::class);
        $engine->restart($config);
        $engine->next(new Position(3, 3, $config));
    }
}