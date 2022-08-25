<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Configuration;

use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use Tests\TestCase;

final class BombsAmountTest extends TestCase
{
    /**
     * @dataProvider invalidHeight
     */
    public function testValidation(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BombsAmount($value);
    }

    public function invalidHeight(): \Generator
    {
        yield 'zero' => [ 0 ];
    }
}