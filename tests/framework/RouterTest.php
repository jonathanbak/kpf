<?php declare(strict_types=1);

namespace KpfTest\Framework;

use Kpf\Exception\RouterException;
use Kpf\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__) . '/stub/SampleController.php';

        Router::resetInstance();
        Router::setNamespace('Stub');
        Router::setDefaultUri('sample');
        Router::addRoute(['GET'], '/', function () {return 'home'; });
        Router::addRoute(['GET'], '/hello/(\\w+)', function ($name) {
            return "Hello, $name!";
        });
        Router::addRoute(['GET'], '/sample', 'sample');
        Router::addRoute(['GET'], '/sample/greet', 'sample/greet');
    }

    public function testCallableRoute(): void
    {
        $result = Router::dispatch(['hello', 'World'], 'GET');
        $this->assertEquals('Hello, World!', $result);
    }

    public function testDefaultRouteDispatch(): void
    {
        $result = Router::dispatch([], 'GET');
        $this->assertEquals('home', $result);
    }

    public function testClassMethodRouting(): void
    {
        $result = Router::dispatch(['sample', 'greet'], 'GET');
        $this->assertEquals('called greet', $result);
    }

    public function testThrowsIfClassNotFound(): void
    {
        $this->expectException(RouterException::class);
        Router::dispatch(['not', 'exists'], 'GET');
    }

    public function testThrowsIfMethodNotFound(): void
    {
        $this->expectException(RouterException::class);
        Router::dispatch(['sample', 'notfound'], 'GET');
    }

    public function testAddPostRoute(): void
    {
        Router::post('/submit', function () {
            return 'Submitted';
        });
        $result = Router::dispatch(['submit'], 'POST');
        $this->assertEquals('Submitted', $result);
    }

    public function testAddAnyRoute(): void
    {
        Router::any( '/ping', function () {
            return 'pong';
        });
        $getResult = Router::dispatch(['ping'], 'GET');
        $postResult = Router::dispatch(['ping'], 'POST');
        $this->assertEquals('pong', $getResult);
        $this->assertEquals('pong', $postResult);
    }

    public function testExecuteWithDefaultFallback(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '';
        $result = Router::execute(null);
        $this->assertEquals('home', $result);
    }
}
