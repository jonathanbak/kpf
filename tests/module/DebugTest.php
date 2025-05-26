<?php declare(strict_types=1);

namespace KpfTest\Module;

use Kpf\Application;
use Kpf\Debug;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';

final class DebugTest extends TestCase
{
    public function testWrite(): void
    {
        Debug::write("TEST Debug log");
        $this->assertTrue(true);
    }


}