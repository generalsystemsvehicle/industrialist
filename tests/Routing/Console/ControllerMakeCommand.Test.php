<?php

namespace GeneralSystemsVehicle\Industrialist\Tests\Models;


use ReflectionClass;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use GeneralSystemsVehicle\Industrialist\Routing\Console\ControllerMakeCommand as Command;

class ControllerMakeCommand extends TestCase
{
    public function getCommandObject()
    {
        $fs = $this->createMock(Filesystem::class);
        return new Command($fs);
    }

    public function getMethod($name)
    {
      $obj = $this->getCommandObject();
      $class = new ReflectionClass($obj);
      $method = $class->getMethod($name);
      $method->setAccessible(true);
      return $method;
    }

    public function callProtected(string $name, array $params = [], $commandObject = null)
    {
        $method = $this->getMethod($name);
        $commandObject = $commandObject ?? $this->getCommandObject();
        return $method->invokeArgs($commandObject, $params);
    }

    public function testGetStub()
    {
        $stub = $this->callProtected('getStub');
        $this->assertFileExists($stub);
    }

    public function testGetDefaultNamespace()
    {
        $ns = $this->callProtected('getDefaultNamespace', ['FooBert']);
        $this->assertEquals($ns, 'FooBert\Http\Controllers');
    }

    public function testBuildClass()
    {
       $app = $this->createMock(Application::class);
       $obj = $this->getCommandObject();
       $obj->setLaravel($app);
       $ns = $this->callProtected('buildClass', ['DummyRootNamespaceHttp\Controllers\Controller'], $obj);
       $this->assertEquals($ns, '');
    }
}
