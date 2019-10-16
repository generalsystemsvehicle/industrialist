<?php

namespace Riverbedlab\Samltron\Tests;

use Riverbedlab\Samltron\Facades\Samltron;
use Riverbedlab\Samltron\Providers\ServiceProvider;
use Orchestra\Testbench\TestCase;

class SamltronTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'samltron' => Samltron::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
