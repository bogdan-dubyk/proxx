<?php

declare(strict_types=1);

namespace Tests\Unit\Console\IO;

use BohdanDubyk\Proxx\Game\Console\IO\StringResult;
use PHPUnit\Framework\TestCase;

final class StringResultTest extends TestCase
{
    /**
     * @dataProvider invalidValues
     */
    public function testInvalidInputProvided(mixed $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        StringResult::fromValue($value);
    }

    public function invalidValues(): \Generator
    {
        yield 'number value' => [ 1 ];
        yield 'boolean value' => [ true ];
        yield 'null value' => [ null ];
    }
}