<?php

namespace Riverbedlab\Industrialist\Tests;

use Riverbedlab\Industrialist\Facades\Industrialist;
use Riverbedlab\Industrialist\Providers\ServiceProvider;
use Orchestra\Testbench\TestCase;

class IndustrialistTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'industrialist' => Industrialist::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
