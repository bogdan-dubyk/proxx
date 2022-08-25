<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Configuration;

use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use Tests\TestCase;

final class WidthTest extends TestCase
{
    /**
     * @dataProvider invalidWidth
     */
    public function testValidation(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Width($value);
    }

    public function invalidWidth(): \Generator
    {
        yield 'zero' => [ 0 ];
        yield 'smaller value than allowed' => [ 4 ];
        yield 'higher value than allowed' => [ 41 ];
    }
}