<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Game\Core\Board;

use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\GameState;

final class Board
{
    /**
     * @var \SplFixedArray<int, \SplFixedArray<int, Field>>
     */
    protected \SplFixedArray $matrix;

    /**
     * @var array<int, Field>
     */
    private array $bombs = [];
    private int $revealedAmount = 0;

    private GameState $state;

    public function __construct(private readonly Configuration $configuration, Position $initialPosition)
    {
        $this->matrix = new \SplFixedArray($this->configuration->height->value);
        for ($y =0; $y < $this->configuration->height->value; $y++) {
            $this->matrix[$y] = new \SplFixedArray($this->configuration->width->value);
            for ($x=0; $x < $this->configuration->width->value; $x++) {
                $this->setField(new Position($x, $y, $this->configuration), FieldValue::FIELD_NOT_SET);
            }
        }
        $this->state = GameState::NOT_STARTED;

        $this->revealInitialPosition($initialPosition);
    }

    /**
     * Populate board with all fields. Sometimes it happening that during the population we can WIN the game :)
     * To avoid this, in case of winning during population let's repopulate
     *
     * @param Position $position
     * @return void
     */
    private function revealInitialPosition(Position $position): void
    {
        $this->setField($position, FieldValue::FIELD_EMPTY);
        $this->populateBombs($position);
        $this->populateNonBombFields();
        $this->state = GameState::STARTED;
        $this->revealField($this->getFieldByPosition($position));

        dump($this->state);
        if ($this->state !== GameState::STARTED) {
            $this->revealedAmount = 0;
            $this->revealInitialPosition($position);
        }
    }

    /**
     * Reveal the field by provided user clicked position.
     *
     * @param Position $position
     */
    public function revealFieldInPosition(Position $position): void
    {
         $this->revealField($this->getFieldByPosition($position));
    }

    /**
     * Reveal the field clicked by user.
     *  - reveal if not revealed yet
     *  - if field is bomb, chamge state to "DEFEAT" and reveal all bombs
     *  - if all non-bomb bields revealed, change state to "WIN" and reveal all bombs
     *  - if revealed field empty, get all surrounding fields and reveal them (recursion)
     *
     * @param Field $field
     */
    private function revealField(Field $field): void
    {
        if ($field->revealed()) {
            dump('revealed');
            return;
        }

        $field->reveal();
        $this->revealedAmount++;

        if ($field->isEqualTo(FieldValue::FIELD_BOMB)) {
            $this->revealAllBombs();
            $this->state = GameState::DEFEATE;

            return;
        }

        if ((
            ($this->configuration->width->value * $this->configuration->height->value)
            - $this->revealedAmount) <= \count($this->bombs)
        ) {
            $this->revealAllBombs();
            $this->state = GameState::WIN;

            return;
        }

        if ($field->isEqualTo(FieldValue::FIELD_EMPTY)) {
            $surroundingFields = $this->getSurroundingFields($field->getPosition());
            foreach ($surroundingFields as $surroundingField) {
                 if (\in_array($this->state, [GameState::WIN, GameState::DEFEATE], true)) {
                    break;
                 }

                 $this->revealField($surroundingField);
            }
        }
    }

    /**
     * Randomly put black holes on the board
     *
     * @param Position $prepopulatedPosition
     * @return void
     */
    private function populateBombs(Position $prepopulatedPosition): void
    {
        while (\count($this->bombs) < $this->configuration->bombsAmount->value) {
            $position = $this->generateRandomPositionForBomb($prepopulatedPosition);
            $this->setField($position, FieldValue::FIELD_BOMB);
            $this->bombs[] = $this->getFieldByPosition($position);
        }
    }

    /**
     * Populate board with all non bomb fields.
     * Loop through all of the positions on the board, and for each position:
     *  1. check if it's not occupied yet
     *  2. get surroundinf positions
     *  3. calculate amount of surrounding bombs
     *  4. depends on amoun of surrounding bobms set correct value of the field
     * @return void
     */
    private function populateNonBombFields(): void
    {
        foreach ($this->matrix as $y => $row) {
            foreach ($row as $x => $val) {
                $position = new Position($x, $y, $this->configuration);
                if ($this->isFieldSetInPosition($position)) {
                    continue;
                }
                $surroundingFields = $this->getSurroundingFields($position);
                $surroundingBombsAmount = 0;
                foreach ($surroundingFields as $surroundingField) {
                    if ($surroundingField->isEqualTo(FieldValue::FIELD_BOMB)) {
                        $surroundingBombsAmount++;
                    }
                }
                if ($surroundingBombsAmount === 0) {
                    $this->setField($position, FieldValue::FIELD_EMPTY);
                } else {
                    $this->setField($position, FieldValue::from((string) $surroundingBombsAmount));
                }
            }
        }
    }

    /**
     * Get all surrounding position, filtering out positions which are not in the board
     *
     * @param Position $currentPosition
     * @return array<int, Field>
     */
    private function getSurroundingFields(Position $currentPosition): array
    {
        $fields = [];
        $coordinates = [
            [$currentPosition->x - 1, $currentPosition->y - 1], // top left
            [$currentPosition->x, $currentPosition->y -1], // top
            [$currentPosition->x + 1, $currentPosition->y - 1], // top rght
            [$currentPosition->x - 1, $currentPosition->y + 1], // bottom left
            [$currentPosition->x, $currentPosition->y + 1], // bottom
            [$currentPosition->x + 1, $currentPosition->y + 1], // bottom right
            [$currentPosition->x -1, $currentPosition->y], // left
            [$currentPosition->x + 1, $currentPosition->y], // right
        ];
        foreach ($coordinates as $coordinate) {
            [$x, $y] = $coordinate;
            try {
                $fields[] = $this->getFieldByPosition(new Position($x, $y, $this->configuration));
            } catch (\InvalidArgumentException ) {
                continue;
            }
        }
        return $fields;
    }

    /**
     *  Get random positin where bomb will be placed.
     *  if $positionToAvoid provided
     *    - get random position
     *    - check if distance between that position and $positionToAvoid is more than 1 field in either X or Y
     *
     * @param Position|null $positionToAvoid - poistion of initial user click, that positions and surrounding positions
     *    should be ignored
     * @return Position
     */
    private function generateRandomPositionForBomb(?Position $positionToAvoid = null): Position
    {
        if ($positionToAvoid === null){
            try {
                $position = new Position(
                    random_int(0, $this->configuration->width->value - 1),
                    random_int(0, $this->configuration->height->value - 1),
                    $this->configuration
                );
            } catch (\Exception ) {
                $position = $this->generateRandomPositionForBomb();
            }

            if ($this->isFieldSetInPosition($position)) {
                $position = $this->generateRandomPositionForBomb();
            }

            return $position;
        }

        while (true){
            $position = $this->generateRandomPositionForBomb();

            // check distance between generated random position and position to ignore
            $distanceFromX = abs($position->x - $positionToAvoid->x);
            $distanceFromY = abs($position->y - $positionToAvoid->y);

            // we need the bomb to places not on the position to avoid and not on the it surrounding positions,
            // so distance shold be more than 1 field in either direction
            if (($distanceFromX > 1) || ($distanceFromY > 1)){
                return $position;
            }
        }
    }

    /**
     * Show all bomb fields. This method should be at the end of the game (win or defead)
     * @return void
     */
    private function revealAllBombs(): void
    {
        foreach ($this->bombs as $blackHole) {
            $blackHole->reveal();
        }
    }

    private function isFieldSetInPosition(Position $position): bool
    {
        return !$this->getFieldByPosition($position)->isEqualTo(FieldValue::FIELD_NOT_SET);
    }

    private function getFieldByPosition(Position $position): Field
    {
        return $this->matrix[$position->y][$position->x];
    }

    private function setField(Position $position, FieldValue $fieldValue): void
    {
        $this->matrix[$position->y][$position->x] = new Field($position, $fieldValue);
    }

    /**
     * @var \SplFixedArray<int, \SplFixedArray<int, Field>>
     */
    public function getBoardMatrix(): \SplFixedArray
    {
        return $this->matrix;
    }

    public function getState(): GameState
    {
        return $this->state;
    }
}