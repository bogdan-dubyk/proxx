<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use BohdanDubyk\Proxx\Game\Console\Click;
use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\Configuration\Height;
use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use BohdanDubyk\Proxx\Game\Console\IO\StringResult;
use PHPUnit\Framework\TestCase;

final class ClickTest extends TestCase
{
    /**
     * @dataProvider invalidClick
     */
    public function testInvalidClick(StringResult $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Click::fromInput(
            $value,
            new Configuration(
                new Width(5),
                new Height(5),
                new BombsAmount(17)
            )
        );
    }

    public function invalidClick(): \Generator
    {
        yield 'empty string' => [ StringResult::fromValue('') ];
        yield 'too many space separated values' => [ StringResult::fromValue('4 5 7') ];
        yield 'non integer values' => [ StringResult::fromValue('dsfds sdfdsf') ];
        yield 'float values' => [ StringResult::fromValue('1.1 2.2') ];
        yield 'single values' => [ StringResult::fromValue('4') ];
    }
}