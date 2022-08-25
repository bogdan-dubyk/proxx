<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Services;

use BohdanDubyk\Proxx\Game\Core\Board\Board;
use BohdanDubyk\Proxx\Game\Console\IO\IntegerResult;
use BohdanDubyk\Proxx\Game\Console\IO\IOManagerInterface;
use BohdanDubyk\Proxx\Game\Console\IO\StringResult;
use BohdanDubyk\Proxx\Game\Core\Board\FieldValue;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class IOSymfonyManager implements IOManagerInterface
{
    private SymfonyStyle $io;

    public function __construct(private readonly InputInterface $input, private readonly OutputInterface $output)
    {
        $this->io = new SymfonyStyle($this->input, $this->output);
    }

    public function message(string $message): void
    {
        $this->io->title($message);
    }

    public function error(string $message): void
    {
        $this->io->error($message);
    }

    public function warning(string $message): void
    {
        $this->io->warning($message);
    }

    public function askForWidth(): IntegerResult
    {
        return IntegerResult::fromValue($this->io->ask('Width of the board?'));
    }

    public function askForHeight(): IntegerResult
    {
        return IntegerResult::fromValue($this->io->ask('Height of the board?'));
    }

    public function askForBombsAmount(): IntegerResult
    {
        return IntegerResult::fromValue($this->io->ask('Amount of bombs?'));
    }

    public function renderBoard(Board $board): void
    {
        $table = new Table($this->output);

        $matrix = $board->getBoardMatrix();

        $header[] = ' ';
        $header = array_merge($header, range(1, \count($matrix[0])));
        $table->setHeaders($header);
        $line = [];
        foreach ($matrix as $rowNumber => $row) {
            $fields = [];
            $fields[] = sprintf('<comment>%s</>', $rowNumber + 1);
            foreach ($row as $cellData) {
                $fields[] = $this->getFieldRepresentation($cellData->displayValue());

            }
            $line[] = $fields;
        }
        $table->setRows($line);
        $table->render();
    }

    public function askForClickPosition(): StringResult
    {
        return StringResult::fromValue(
            $this->io->ask('Provide position of field you want to select. (Format: X Y)')
        );
    }

    public function start(): void
    {
        $this->io->title('Let\'s get start');
    }

    public function victory(): void
    {
        $this->io->success('👏 YOU WIN');
    }

    public function defeated(): void
    {
        $this->io->error('💣 BOOOM YOU LOOSE');
    }

    public function confirmReplay(): bool
    {
        return $this->io->confirm('Play again?');
    }

    public function confirmReconfiguration(): bool
    {
        return $this->io->confirm('Configure new board?');
    }

    public function boardBroken(): void
    {
        $this->io->warning('😔 OOPS. Sorry but somethinkg wrong with the board, we need restart the game');
    }

    private function getFieldRepresentation(FieldValue $fieldValue): string
    {
        return match ($fieldValue) {
            FieldValue::FIELD_BOMB => '💣',
            FieldValue::FIELD_NOT_SET => 'X',
            FieldValue::FIELD_CLOSED => '🔒',
            FieldValue::FIELD_EMPTY => ' ',
            FieldValue::FIELD_ONE => '1',
            FieldValue::FIELD_TWO => '2',
            FieldValue::FIELD_THREE => '3',
            FieldValue::FIELD_FOUR => '4',
            FieldValue::FIELD_FIVE => '5',
            FieldValue::FIELD_SIX => '6',
            FieldValue::FIELD_SEVEN => '7',
            FieldValue::FIELD_EIGHT => '8',
            FieldValue::FIELD_NINE => '9'
        };
    }
}