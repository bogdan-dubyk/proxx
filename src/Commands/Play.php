<?php

declare(strict_types=1);

namespace BohdanDubyk\Proxx\Commands;

use BohdanDubyk\Proxx\Game\Console\ConsoleGame;
use BohdanDubyk\Proxx\Game\Core\Board\BoardFactory;
use BohdanDubyk\Proxx\Services\IOSymfonyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Play extends Command
{
    protected static $defaultName = 'play';

    protected static $defaultDescription = 'Play the game!';

    /**
     * Execute the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $game = new ConsoleGame(new IOSymfonyManager($input, $output), new BoardFactory());

        $game->runGame();

        return Command::SUCCESS;
    }

}