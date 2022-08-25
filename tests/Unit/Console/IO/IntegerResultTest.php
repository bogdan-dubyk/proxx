<?php

declare(strict_types=1);

namespace Tests\Unit\Console\IO;

use BohdanDubyk\Proxx\Game\Console\IO\IntegerResult;
use PHPUnit\Framework\TestCase;

final class IntegerResultTest extends TestCase
{
    /**
     * @dataProvider invalidValues
     */
    public function testInvalidInputProvided(mixed $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        IntegerResult::fromValue($value);
    }

    public function testNumericStringProvider(): void
    {
        $result = IntegerResult::fromValue('1');
        $this->assertEquals(1, $result->value);
    }

    public function invalidValues(): \Generator
    {
        yield 'string value' => [ 'invalid' ];
        yield 'boolean value' => [ true ];
        yield 'null value' => [ null ];
    }
}