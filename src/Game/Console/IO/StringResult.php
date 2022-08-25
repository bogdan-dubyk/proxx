<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Console\IO;

final class StringResult implements InputResult
{
    private function __construct(public readonly string $value)
    {
    }

    public static function fromValue(mixed $value): self
    {

        if (!\is_string($value)) {
            throw new \InvalidArgumentException(
                'Invalid value provided expected string'
            );
        }

        return new self($value);
    }
}