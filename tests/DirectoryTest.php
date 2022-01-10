<?php declare(strict_types=1);

namespace Kpf\Tests;

use Kpf\Constant;
use Kpf\Directory;
use PHPUnit\Framework\TestCase;

final class DirectoryTest extends TestCase
{

    private $directory;

    protected function setUp(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        $this->directory = new Directory;
        $this->directory->setRoot($baseDir);
    }

    public function testRoot(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        $dir = $this->directory->root();
        // Assert
        $this->assertTrue($dir == $baseDir);
    }

    public function testGet(): void
    {
        $configDir = $this->directory->get(Constant::DIR_CONFIG);

        // Assert
        $this->assertEquals(Constant::DIR_CONFIG, basename($configDir));
    }

}