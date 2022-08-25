<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Board;

final class Field
{
    private bool $revealed = false;

    public function __construct(private readonly Position $position, private readonly FieldValue $value)
    {
    }

    public function revealed(): bool
    {
        return $this->revealed;
    }

    public function reveal(): FieldValue
    {
        $this->revealed = true;

        return $this->displayValue();
    }

    public function hiddenValue(): FieldValue
    {
        return $this->value;
    }

    public function displayValue(): FieldValue
    {
        return $this->revealed ? $this->value : FieldValue::FIELD_CLOSED;
    }

    public function isEqualTo(FieldValue $valueToComapare): bool
    {
        return $this->value === $valueToComapare;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function isAtTheSamePosition(Position $position): bool
    {
        return $this->position->isEqual($position);
    }
}