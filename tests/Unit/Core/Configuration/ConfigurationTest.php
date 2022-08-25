<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Configuration;

use BohdanDubyk\Proxx\Game\Core\Configuration\BombsAmount;
use BohdanDubyk\Proxx\Game\Core\Configuration\Configuration;
use BohdanDubyk\Proxx\Game\Core\Configuration\Height;
use BohdanDubyk\Proxx\Game\Core\Configuration\Width;
use Tests\TestCase;

final class ConfigurationTest extends TestCase
{
    public function testInvalidConfig(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Configuration(
            new Width(5),
            new Height(5),
            new BombsAmount(17) // to many bombs
        );
    }
}