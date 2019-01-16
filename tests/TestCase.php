<?php
namespace Tests;

use PHPUnit\Framework\TestCase as PhpunitCase;
/**
 * 单元测试用例
 *
 */

//根据自己开发环境填入绝对路径
require_once 'C:\www\svn\ms\core/flight/autoload.php';

class TestCase extends PhpunitCase
{
     /**
     * @var \flight\Engine
     */
    private $app;
    function setUp() {
        $this->app = new \flight\Engine();
        $this->app->path(__DIR__.'/classes');
    }
    // Autoload a class
    function testAutoload(){
        $this->app->register('user', 'User');
        $loaders = spl_autoload_functions();
        $user = $this->app->user();
        $this->assertTrue(sizeof($loaders) > 0);
        $this->assertTrue(is_object($user));
        $this->assertEquals('User', get_class($user));
    }
    // Check autoload failure
    function testMissingClass(){
        $test = null;
        $this->app->register('test', 'NonExistentClass');
        if (class_exists('NonExistentClass')) {
            $test = $this->app->test();
        }
        $this->assertEquals(null, $test);
    }    
}

//** run **/  ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/TestCase