<?php

namespace Riverbedlab\Industrialist\Tests\Models;


use Orchestra\Testbench\TestCase;
use Riverbedlab\Industrialist\Models\User as UserModel;
use Riverbedlab\Industrialist\Exceptions\AttributeNotFoundException;
use Riverbedlab\Industrialist\Exceptions\MethodNotFoundException;

class User extends TestCase
{
    public function testSetAndGetGoodKey()
    {
        $user = new UserModel();
        $user->setNameId('some name');
        $this->assertEquals($user->getNameId(), 'some name');
    }

    public function testSetBadKey()
    {
        $user = new UserModel();
        $this->expectException(AttributeNotFoundException::class);
        $user->setBadAttributeName('some name');
    }

    public function testCallBadMethod()
    {
        $user = new UserModel();
        $this->expectException(MethodNotFoundException::class);
        $user->fooBadBar('some name');
    }

    public function testToArray()
    {
        $user = new UserModel();
        $user->setNameId('some_name_id');
        $user->setNameIdFormat('some_name_id:format::foo::::Email');
        $array = $user->toArray();
        $this->assertEquals($array['name_id'], 'some_name_id');
        $this->assertEquals($array['name_id_format'], 'some_name_id:format::foo::::Email');
        $this->assertEquals($array['is_authenticated'], false);
        $this->assertEquals($array['last_response'], null);
        $this->assertEquals($array['attributes'], []);
    }
}
