<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Console\IO;

final class IntegerResult implements InputResult
{
    private function __construct(public readonly int $value)
    {
    }

    public static function fromValue(mixed $value): self
    {
        if (\is_int($value)) {
            return new self($value);
        }

        if (\is_string($value) && \ctype_digit($value)) {
            return new self((int) $value);
        }
        throw new \InvalidArgumentException(
            'Invalid value provided expected integer'
        );
    }
}