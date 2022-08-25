# PROXX

PHP implementation of Minesweeper game. This application coming with CLI implementation
of the game, but can be easily extended.

## Requirements

- PHP 8.1
- Composer (https://getcomposer.org/)

## Installation

Download the files or clone this project.

``git clone https://github.com/mauserrifle/php-minesweeper.git``

Get all dependencies through composer:

``composer install``

## Running unit tests

``./vendor/bin/phpunit tests``

This should output like this :

```
..........................................                        42 / 42 (100%)

Time: 0 seconds, Memory: 4.00Mb

OK (42 tests, 142 assertions)
```

## Playing

Run in your console ``bin/proxx``

## TODO

- adding presets with different levels of complexity
- ability to mark field with flag
- interface/abstraction for game state storage
- provide HTTP api for game
- HTML/JS GUI

## Customization

### New implementations
The core of the application exist under ``BohdanDubyk\Proxx\Game\Core`` namespace. So basically to
provide new game implementation, for example SPA implementation you need only properly use ``BohdanDubyk\Proxx\Game\Core\Engine`` 
(API of enigne provided in seciont API).

Currently application has only console implementation, and here is the example of how it's implemented (simplified)

```
public function runGame(?GameState $state = null): ?GameState
{
    try {
    return match ($state) {
        null, GameState::NOT_STARTED => $this->runGame($this->start()),
        GameState::STARTED => $this->runGame($this->next()),
        GameState::DEFEATE => $this->runGame($this->defeat()),
        GameState::WIN => $this->runGame($this->win()),
        GameState::STOP => null
    };
    } catch (GameException) {
        return $this->runGame($this->restart());
    }
}
```
Basically it's recursion which making different steps depends on the current game state. 

For example if you want to make HTTP implementation you simply need to run correct HTTP calls depends on the 
state provided in response. But also you need to think about how to store the user session (engine state, board etc.),
it's up to how to do it.

### Cusomizing current CLI implementation

If you want to change a messages, board style or localazie the current CLI implementation, you can do it by simply
implementing ``BohdanDubyk\Proxx\Game\Console\IO\``. Game using ``BohdanDubyk\Proxx\Services\IOSymfonyManager`` implementation based on (Symfony console component)[https://symfony.com/doc/current/console.html#testing-commands]

## Engine API

### start(Position $position)

*__Desctiption__*:
This method trigger game start and generating the board.
Should be called if game in one of states: `GameState::NOT_STARTED`, `GameState::WIN` and `GameState::DEFEAT`

*__Parameters__*:
 - `Position $position` - position of initial/first click to the board, depend on that click board will be generated. 
So board will be generated in a way that first click never will be on a bomb

*__Return value__*:

`GameState` - state of the game after method execution. It should be `GameState::STARTED`

*__Exception__*:

``BohdanDubyk\Proxx\Game\Core\GameException`` - will be thrown if method called from incorrect state of the game

### next(Position $position)

*__Desctiption__*:
This method making the click and revealing filed on the board in the place of provided position
Should be called only if game is in  `GameState::STARTED` state

*__Parameters__*:
- `Position $position` - position of next click to the board

*__Return value__*:

`GameState` - state of the game after method execution. Can be eithre `GameState::STARTED`, `GameState::WIN`, `GameState::DEFEAT`

*__Exception__*:


``BohdanDubyk\Proxx\Game\Core\GameException`` - will be thrown if method called from incorrect state of the game

### restart(?Configuration $newConfiguration)

*__Desctiption__*:
This method can be called when you want to restart the board. This method will invalidate the current board. 
If configuration provided it'll replace existing config with it and will be used for new board generation
Can be called from any state except GameState::STOP

*__Parameters__*:
- `P?Configuration $newConfiguration` - optional congiguration, if you want to generate new board with new configurations

*__Return value__*:

`GameState` - state of the game after method execution. Can be eithre `GameState::NOT_STARTED`

*__Exception__*:

``BohdanDubyk\Proxx\Game\Core\GameException`` - will be thrown if method called from incorrect state of the game

