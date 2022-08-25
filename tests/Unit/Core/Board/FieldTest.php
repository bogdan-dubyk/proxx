<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Board;

use BohdanDubyk\Proxx\Game\Core\Board\Field;
use BohdanDubyk\Proxx\Game\Core\Board\FieldValue;
use BohdanDubyk\Proxx\Game\Core\Board\Position;
use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\Configuration\Height;
use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use Tests\TestCase;

final class FieldTest extends TestCase
{
    public function testDispalyValueIfFieldNotRevealed(): void
    {
        $field = new Field(
            new Position(
                1,
                1,
                new Configuration(
                    new Width(10),
                    new Height(10),
                    new BombsAmount(5)
                )
            ),
            FieldValue::FIELD_BOMB
        );

        static::assertEquals(FieldValue::FIELD_CLOSED, $field->displayValue());
        static::assertTrue($field->isEqualTo(FieldValue::FIELD_BOMB));
        static::assertFalse($field->revealed());
    }

    public function testDisplayValueAfterFieldRevealed(): void
    {
        $field = new Field(
            new Position(
                1,
                1,
                new Configuration(
                    new Width(10),
                    new Height(10),
                    new BombsAmount(5)
                )
            ),
            FieldValue::FIELD_BOMB
        );

        $field->reveal();

        static::assertEquals(FieldValue::FIELD_BOMB, $field->displayValue());
        static::assertTrue($field->isEqualTo(FieldValue::FIELD_BOMB));
        static::assertTrue($field->revealed());
    }
}