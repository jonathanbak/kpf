<?php declare(strict_types=1);

namespace Kpf\Tests;

use Kpf\Application;
use Kpf\Constant;
use Kpf\Directory;
use Kpf\System;
use PHPUnit\Framework\TestCase;

class MySystem extends System
{
    public function start()
    {
        echo "test ok";
    }
}

final class SystemTest extends TestCase
{
    protected function setUp(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        Application::init($baseDir);
    }

    public function testClass(): void
    {
        $MySystem = new MySystem();
        $MySystem->start();
        // Assert
        $this->assertTrue(true);
    }

}