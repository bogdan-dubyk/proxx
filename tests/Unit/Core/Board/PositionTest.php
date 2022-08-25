<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Board;

use BohdanDubyk\Proxx\Game\Core\Board\Position;
use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\Configuration\Height;
use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use Tests\TestCase;

final class PositionTest extends TestCase
{
    /**
     * @dataProvider invalidPositions
     */
    public function testInvalidPosition(int $x, int $y, Configuration $configuration): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Position($x, $y, $configuration);
    }

    public function invalidPositions(): \Generator
    {
        yield 'less than zero' => [
            -1,
            -1,
            new Configuration(
                new Width(5),
                new Height(5),
                new BombsAmount(5)
            )
        ];
        yield 'bigger than height' => [
            5,
            7,
            new Configuration(
                new Width(5),
                new Height(5),
                new BombsAmount(5)
            )
        ];
        yield 'bigger than x' => [
            7,
            5,
            new Configuration(
                new Width(5),
                new Height(5),
                new BombsAmount(5)
            )
        ];
    }
}