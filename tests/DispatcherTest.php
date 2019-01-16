<?php
namespace Tests;

use PHPUnit\Framework\TestCase as PhpunitCase;
/**
 * 单元测试用例
 *
 */

//根据自己开发环境填入绝对路径
require_once 'C:\www\svn\ms\core/flight/autoload.php';

require_once 'C:\www\svn\ms\tests/classes/Hello.php';

class DispatcherTest extends PhpunitCase
{
     /**
     * @var \flight\core\Dispatcher
     */
    private $dispatcher;
    function setUp(){
        $this->dispatcher = new \flight\core\Dispatcher();
    }
    // Map a closure
    function testClosureMapping(){
        $this->dispatcher->set('map1', function(){
            return 'hello';
        });
        $result = $this->dispatcher->run('map1');
        $this->assertEquals('hello', $result);
    }
    // Map a function
    function testFunctionMapping(){
        $this->dispatcher->set('map2', function(){
            return 'hello';
        });
        $result = $this->dispatcher->run('map2');
        $this->assertEquals('hello', $result);
    }
    // Map a class method
    function testClassMethodMapping(){
        $h = new Hello();
        $this->dispatcher->set('map3', array($h, 'sayHi'));
        $result = $this->dispatcher->run('map3');
        $this->assertEquals('hello', $result);
    }
    // Map a static class method
    function testStaticClassMethodMapping(){
        $this->dispatcher->set('map4', array('Hello', 'sayBye'));
        $result = $this->dispatcher->run('map4');
        $this->assertEquals('goodbye', $result);
    }
    // Run before and after filters
    function testBeforeAndAfter() {
        $this->dispatcher->set('hello', function($name){
            return "Hello, $name!";
        });
        $this->dispatcher->hook('hello', 'before', function(&$params, &$output){
            // Manipulate the parameter
            $params[0] = 'Fred';
        });
        $this->dispatcher->hook('hello', 'after', function(&$params, &$output){
            // Manipulate the output
            $output .= " Have a nice day!";
        });
        $result = $this->dispatcher->run('hello', array('Bob'));
        $this->assertEquals('Hello, Fred! Have a nice day!', $result);
    }
    // Test an invalid callback
    function testInvalidCallback() {
        $this->setExpectedException('Exception', 'Invalid callback specified.');
        $this->dispatcher->execute(array('NonExistentClass', 'nonExistentMethod'));
    }  
}

//** run **/  ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/DispatcherTest