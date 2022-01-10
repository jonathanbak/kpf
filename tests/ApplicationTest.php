<?php declare(strict_types=1);

namespace Kpf\Tests;

use Kpf\Application;
use Kpf\Constant;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    public function testInstall(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        $namespace = "example";
        Application::install($baseDir, $namespace);

        $defaultRouteConfigure = Application::getConfig()->common(Constant::KEY_ROUTE);
        $routeConfigure = Application::getConfig()->load($defaultRouteConfigure);
        // Assert
        $this->assertNotEmpty($routeConfigure['default']);
    }

    public function testInit(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        Application::init($baseDir);

        $contDir = Application::getDirectory()->getController();
        // Assert
        $this->assertEquals(Constant::DIR_CONTROLLER, $contDir);
    }

    public function testStart(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        Application::init($baseDir);

        try{
            Application::start();
            $this->assertTrue(true);
        }catch(\Exception $e){
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }

    }
}