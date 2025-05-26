<?php

namespace KpfTest\Framework;

use Kpf\Application;
use Kpf\Constant;
use Kpf\Installer;
use PHPUnit\Framework\TestCase;

final class InstallerTest extends TestCase
{
    private $installer;
    private $rootDir;
    private $namespace = 'Example';

    protected function setUp(): void
    {
        $this->rootDir = dirname(__DIR__,2) . '/example';
        $this->installer = new Installer();
        $this->clearRootDir(); // 테스트 시작 전 초기화
    }

    public function testFullInstallationProcess(): void
    {
        // 1. 설정 파일 생성
        $this->installer->setup($this->rootDir, $this->namespace);
        $this->assertFileExists($this->rootDir . '/configure.json');

        // 2. 설정 기반 Application 초기화
        Application::boot($this->rootDir);

        // 3. 실제 설치
        $this->installer->install();

        // 4. 결과 확인
        $directory = Application::getDirectory();
        $config = Application::getConfig();

        $this->assertFileExists($directory->get(Constant::DIR_CONFIG) . $config->getDbConfigFilename());
        $this->assertFileExists($directory->get(Constant::DIR_CONFIG) . $config->getRouteConfigFilename());
        $this->assertFileExists($directory->get(Constant::DIR_CONTROLLER) . '_sys/Test.php');
        $this->assertFileExists($directory->get(Constant::DIR_CONTROLLER) . 'Main/Main.php');
        $this->assertFileExists($directory->get(Constant::DIR_MODEL) . 'Account/Member.php');
        $this->assertFileExists($directory->get(Constant::DIR_VIEW) . 'base.twig');
        $this->assertFileExists($directory->get(Constant::DIR_VIEW) . 'main/main.twig');
        $this->assertFileExists($directory->get(Constant::DIR_HOME) . 'index.php');
    }

    protected function tearDown(): void
    {
//        $this->clearRootDir(); // 테스트 종료 후 정리
    }

    private function clearRootDir(): void
    {
        if (!is_dir($this->rootDir)) {
            mkdir($this->rootDir, 0755, true);
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->rootDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @rmdir($file->getRealPath());
            } else {
                @unlink($file->getRealPath());
            }
        }
    }
}