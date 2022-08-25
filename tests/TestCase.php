<?php

declare(strict_types=1);

namespace Tests;

use DG\BypassFinals;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        BypassFinals::enable();
    }
}