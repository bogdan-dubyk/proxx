<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Board;

use BohdanDubyk\Proxx\Game\Core\Board\Board;
use BohdanDubyk\Proxx\Game\Core\Board\Field;
use BohdanDubyk\Proxx\Game\Core\Board\FieldValue;
use BohdanDubyk\Proxx\Game\Core\Board\Position;
use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\Configuration\Height;
use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use BohdanDubyk\Proxx\Game\Core\GameState;
use Tests\TestCase;

final class BoardTest extends TestCase
{

    public function testGameStateChangeAfterInitialClick(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));

        self::assertEquals($board->getState(), GameState::STARTED);
    }

    public function testThatWeDoNotHaveNotSetFieldsAfterInitialClick(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));

        foreach ($board->getBoardMatrix() as $row) {
            foreach ($row as $field) {
                self::assertFalse($field->isEqualTo(FieldValue::FIELD_NOT_SET));
            }
        }
    }

    public function tstCorrectBombsAmountPopulatedAfterInitialClick(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));

        $bombsAmount = 0;
        foreach ($board->getBoardMatrix() as $row) {
            foreach ($row as $field) {
                self::assertFalse($field->revealed());
                if ($field->isEqualTo(FieldValue::FIELD_BOMB)) {
                    $bombsAmount++;
                }
            }
        }

        self::assertEquals($expectedBombs, $bombsAmount);
    }

    public function testBombsNotSetOnInitiallyRevealedFieldAndSurrounding(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $revealedPosition = new Position(3, 3, $config);
        $arrayPositionsWithoutBombs = $this->getSurroindingPoints($revealedPosition, $config);
        $arrayPositionsWithoutBombs[] = $revealedPosition;

        $board = new Board($config, $revealedPosition);

        foreach ($board->getBoardMatrix() as $row) {
            foreach ($row as $field) {
                foreach ($arrayPositionsWithoutBombs as $positionsWithoutBomb) {
                    // if field is one of the fields where bomb should not exist, check it
                    if ($field->isAtTheSamePosition($positionsWithoutBomb)) {
                        self::assertFalse($field->isEqualTo(FieldValue::FIELD_BOMB));
                    }
                }
            }
        }
    }

    public function testMakeSureAllBombsSurroundedWithNumericFIeldsAfterInitialClick(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));

        $matrix = $board->getBoardMatrix();

        // collct bomb fields
        $bombs = [];
        foreach ($matrix as $row) {
            foreach ($row as $field) {
                if ($field->isEqualTo(FieldValue::FIELD_BOMB)) {
                    $bombs[] = $field;
                }
            }
        }

        foreach ($bombs as $bomb) {
            $bombSurroundingPositions = $this->getSurroindingPoints($bomb->getPosition(), $config);
            foreach ($bombSurroundingPositions as $surroundingPosition) {
                self::assertNotContains(
                    $matrix[$surroundingPosition->y][$surroundingPosition->x]->hiddenValue(),
                    [FieldValue::FIELD_NOT_SET, FieldValue::FIELD_EMPTY]
                );
            }
        }
    }

    public function testCorrectStateOnDefeat(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));
        $matrix = $board->getBoardMatrix();

        // find the first bomb
        $bomb = null;
        foreach ($matrix as $row) {
            foreach ($row as $field) {
                if ($field->isEqualTo(FieldValue::FIELD_BOMB)) {
                    $bomb = $field;
                    break;
                }
            }
        }

        self::assertInstanceOf(Field::class, $bomb);

        // click on bomb
        $board->revealFieldInPosition($bomb->getPosition());
        self::assertSame($board->getState(), GameState::DEFEATE);
    }

    public function testIfAllBombsRevealedOnDefeat(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));
        $matrix = $board->getBoardMatrix();

        // collect all bombs
        $bombs = [];
        foreach ($matrix as $row) {
            foreach ($row as $field) {
                if ($field->isEqualTo(FieldValue::FIELD_BOMB)) {
                    $bombs[] = $field;
                }
            }
        }

        // click on the first bomb
        $board->revealFieldInPosition($bombs[0]->getPosition());

        // make sure all bombs revealed
        foreach ($bombs as $bomb) {
            self::assertTrue($bomb->revealed());
        }
    }

    public function testCorrectStateOnWin(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));
        $matrix = $board->getBoardMatrix();

        // reveal all non-bombs
        $state = null;
        foreach ($matrix as $row) {
            foreach ($row as $field) {
                if ($field->isEqualTo(FieldValue::FIELD_BOMB) === false) {
                    $board->revealFieldInPosition($field->getPosition());
                    $state = $board->getState();
                    if ($state === GameState::WIN) {
                        break 2;
                    }
                }
            }
        }

        self::assertEquals(GameState::WIN, $state);
    }

    public function testAllBombsRevealdOnWin(): void
    {
        $width = 5;
        $height = 8;
        $expectedBombs = 5;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));
        $matrix = $board->getBoardMatrix();

        // reveal all non-bombs
        $bombs = [];
        foreach ($matrix as $row) {
            foreach ($row as $field) {
                if ($field->isEqualTo(FieldValue::FIELD_BOMB) === false) {
                    $board->revealFieldInPosition($field->getPosition());
                    continue;
                }

                $bombs[] = $field;
            }
        }

        foreach ($bombs as $bomb) {
            self::assertTrue($bomb->revealed());
        }
    }

    public function testStateNotChangingOnRegularClick(): void
    {
        $width = 20;
        $height = 20;
        $expectedBombs = 20;
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($expectedBombs)
        );
        $board = new Board($config, new Position(3, 3, $config));

        self::assertEquals($board->getState(), GameState::STARTED);

        // click on random non bomb field
        $state = null;
        foreach ($board->getBoardMatrix() as $row) {
            foreach ($row as $field) {
                if ($field->isEqualTo(FieldValue::FIELD_BOMB) === false) {
                    $board->revealFieldInPosition($field->getPosition());
                    break 2;
                }
            }
        }

        self::assertEquals($board->getState(), GameState::STARTED);
    }

    private function getSurroindingPoints(Position $position, Configuration $config): array
    {
        $surrroundingPoints[] = $this->getPosition($position->x - 1, $position->y - 1, $config); // top left
        $surrroundingPoints[] = $this->getPosition($position->x, $position->y - 1, $config); // top
        $surrroundingPoints[] = $this->getPosition($position->x + 1, $position->y - 1, $config); // top rght
        $surrroundingPoints[] = $this->getPosition($position->x - 1, $position->y + 1, $config); // bottom left
        $surrroundingPoints[] = $this->getPosition($position->x, $position->y + 1, $config); // bottom
        $surrroundingPoints[] = $this->getPosition($position->x + 1, $position->y + 1, $config); // bottom right
        $surrroundingPoints[] = $this->getPosition($position->x - 1, $position->y, $config); // left
        $surrroundingPoints[] = $this->getPosition($position->x + 1, $position->y, $config); //  right

        return array_filter($surrroundingPoints, fn (?Position $element) => $element !== null);
    }

    private function getPosition(int $x, int $y, Configuration $config): ?Position
    {
        try {
            return new Position($x, $y, $config);
        } catch (\InvalidArgumentException){
            return null;
        }

    }
}