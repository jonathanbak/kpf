<?php declare(strict_types=1);

namespace Kpf\Tests;

use Kpf\Application;
use Kpf\Debug;
use PHPUnit\Framework\TestCase;

final class DebugTest extends TestCase
{
    protected function setUp(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        Application::init($baseDir);
    }

    public function testWrite(): void
    {
        Debug::write("TEST Debug log");
        $this->assertTrue(true);
    }


}