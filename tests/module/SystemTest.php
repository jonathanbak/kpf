<?php declare(strict_types=1);

namespace KpfTest\Module;

use Kpf\System;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';

class MySystem extends System
{
    public function start()
    {
        echo "test ok";
    }
}

final class SystemTest extends TestCase
{
    public function testClass(): void
    {
        $MySystem = new \KpfTest\Module\MySystem();
        $MySystem->start();
        // Assert
        $this->assertTrue(true);
    }

    public function testNonCliAccessThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Access Denied.");

        new class extends System {
            protected function isCli(): bool
            {
                return false; // 강제로 비 CLI 상태 시뮬레이션
            }
        };
    }

}