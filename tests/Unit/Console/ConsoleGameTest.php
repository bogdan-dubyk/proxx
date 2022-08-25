<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use BohdanDubyk\Proxx\Game\Console\ConsoleGame;
use BohdanDubyk\Proxx\Game\Console\IO\IntegerResult;
use BohdanDubyk\Proxx\Game\Console\IO\IOManagerInterface;
use BohdanDubyk\Proxx\Game\Console\IO\StringResult;
use BohdanDubyk\Proxx\Game\Core\Board\Board;
use BohdanDubyk\Proxx\Game\Core\Board\BoardFactory;
use BohdanDubyk\Proxx\Game\Core\Board\FieldValue;
use BohdanDubyk\Proxx\Game\Core\Board\Position;
use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\Configuration\Height;
use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use Tests\TestCase;

final class ConsoleGameTest extends TestCase
{
    public function testVictory(): void
    {
        $width = 5;
        $height = 5;
        $bombsAmount = 15;
        $initalPositionX = 3;
        $initialPositionY = 3;

        $io = $this->createMock(IOManagerInterface::class);
        $this->mockConfig($io, $width, $height, $bombsAmount);
        $board = $this->createBoardWithInitialClickToMock(
            $initalPositionX,
            $initialPositionY,
            $width,
            $height,
            $bombsAmount
        );

        $boardFactory = $this->createMock(BoardFactory::class);
        $boardFactory->expects($this->once())
            ->method('create')
            ->willReturn($board);

        //  collect click to non-bomb fields
        $clicks = [];
        $clicks[] = StringResult::fromValue(sprintf('%s %s', $initalPositionX -1, $initialPositionY -1));
        foreach ($board->getBoardMatrix() as $row) {
            foreach ($row as $field) {
                if ($field->isEqualTo(FieldValue::FIELD_BOMB) === false) {
                    $clicks[] = StringResult::fromValue(
                        sprintf(
                            '%s %s',
                            $field->getPosition()->x +1,
                            $field->getPosition()->y +1
                        )
                    );
                }
            }
        }
        // mock clicks on non-bomb fields
        $io->expects($this->any())
            ->method('askForClickPosition')
            ->willReturnOnConsecutiveCalls(...$clicks);
        // expecting to print victory message
        $io->expects($this->once())
            ->method('victory');


        $game = new ConsoleGame($io, $boardFactory);

        $game->runGame();
    }

    public function testDefeat(): void
    {
        $width = 5;
        $height = 5;
        $bombsAmount = 15;
        $initalPositionX = 3;
        $initialPositionY = 3;

        $io = $this->createMock(IOManagerInterface::class);
        $this->mockConfig($io, $width, $height, $bombsAmount);
        $board = $this->createBoardWithInitialClickToMock(
            $initalPositionX,
            $initialPositionY,
            $width,
            $height,
            $bombsAmount
        );

        $boardFactory = $this->createMock(BoardFactory::class);
        $boardFactory->expects($this->once())
            ->method('create')
            ->willReturn($board);

        //  collect click to non-bomb fields
        $clicks = [];
        $clicks[] = StringResult::fromValue(sprintf('%s %s', $initalPositionX -1, $initialPositionY -1));
        foreach ($board->getBoardMatrix() as $row) {
            foreach ($row as $field) {
                if ($field->isEqualTo(FieldValue::FIELD_BOMB)) {
                    $clicks[] = StringResult::fromValue(
                        sprintf(
                            '%s %s',
                            $field->getPosition()->x +1,
                            $field->getPosition()->y +1
                        )
                    );
                    break;
                }
            }
        }
        // mock clicks on non-bomb fields
        $io->expects($this->any())
            ->method('askForClickPosition')
            ->willReturnOnConsecutiveCalls(...$clicks);

        // expecting to print defeat message
        $io->expects($this->once())
            ->method('defeated');


        $game = new ConsoleGame($io, $boardFactory);

        $game->runGame();
    }

    private function mockConfig(IOManagerInterface $io, int $width, int $height, int $bombsAmount): void
    {
        // Mock with
        $io->expects($this->once())
            ->method('askForWidth')
            ->willReturn(IntegerResult::fromValue($width));
        // Mock height
        $io->expects($this->once())
            ->method('askForHeight')
            ->willReturn(IntegerResult::fromValue($height));
        // Mock bombs amount
        $io->expects($this->once())
            ->method('askForBombsAmount')
            ->willReturn(IntegerResult::fromValue($bombsAmount));
    }

    private function createBoardWithInitialClickToMock(int $x, int $y, int $width, int $height, int $bombsAmount): Board
    {
        $config = new Configuration(
            new Width($width),
            new Height($height),
            new BombsAmount($bombsAmount),
        );
        return new Board(
            $config,
            new Position(
                $x,
                $y,
                $config
            )
        );
    }
}