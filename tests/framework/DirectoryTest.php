<?php declare(strict_types=1);

namespace KpfTest\Framework;

use Kpf\Constant;
use Kpf\Directory;
use Kpf\Exception\DirectoryException;
use PHPUnit\Framework\TestCase;

final class DirectoryTest extends TestCase
{
    private $directory;
    private $rootDir;

    protected function setUp(): void
    {
        $this->rootDir = dirname(__DIR__,2) . '/example';
        $this->directory = new Directory();
    }

    public function testSetAndGetRoot(): void
    {
        $this->directory->setRoot($this->rootDir);
        $this->assertEquals($this->rootDir, $this->directory->root());
    }

    public function testExceptionWhenRootNotSet(): void
    {
        $this->expectException(DirectoryException::class);
        $this->directory->get(Constant::DIR_CONFIG);
    }

    public function testGetDefaultDirectory(): void
    {
        $this->directory->setRoot($this->rootDir);
        $path = $this->directory->get(Constant::DIR_CONFIG);
        $this->assertEquals($this->rootDir . '/config/', $path);
    }

    public function testSetCustomDirectory(): void
    {
        $this->directory->setRoot($this->rootDir);
        $this->directory->set(Constant::DIR_MODEL, 'custom-models');
        $path = $this->directory->get(Constant::DIR_MODEL);
        $this->assertEquals($this->rootDir . '/custom-models/', $path);
    }

    public function testGetAllDirectories(): void
    {
        $this->directory->setRoot($this->rootDir);
        $all = $this->directory->getAll();

        $this->assertArrayHasKey(Constant::DIR_CONFIG, $all);
        $this->assertEquals($this->rootDir . '/config/', $all[Constant::DIR_CONFIG]);
        $this->assertEquals($this->rootDir . '/_tmp/compile/', $all[Constant::DIR_COMPILE]);
    }

    public function testSetTrimsSlashes(): void
    {
        $this->directory->setRoot($this->rootDir);
        $this->directory->set(Constant::DIR_LOG, '/custom/logs/');
        $this->assertEquals($this->rootDir . '/custom/logs/', $this->directory->get(Constant::DIR_LOG));
    }
}