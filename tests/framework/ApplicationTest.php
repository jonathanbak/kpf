<?php

namespace KpfTest\Framework;

use Kpf\Application;
use Kpf\Installer;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    private $rootDir;

    protected function setUp(): void
    {
        $this->rootDir = dirname(__DIR__,2) . '/example';

        if (!file_exists($this->rootDir)) {
            mkdir($this->rootDir, 0755, true);
        }

        //최초 셋팅 후 인스톨
        $Installer = new Installer();
        $Installer->setup($this->rootDir, "Example");
        // 2. 설정 기반 Application 초기화
        Application::boot($this->rootDir);
        // 3. 실제 설치
        $Installer->install();
    }

    public function testBootInitializesDirectoryAndConfig(): void
    {
        Application::boot($this->rootDir);

        $this->assertNotNull(Application::getDirectory());
        $this->assertNotNull(Application::getConfig());
        $this->assertEquals('controllers', Application::getConfig()->common('dirs.controller'));
    }

    public function testSetAndGetTemplate(): void
    {
        Application::boot($this->rootDir);

        $template = Application::getTemplate();
        $this->assertNotNull($template);
    }

    public function testDisplayErrorsTogglesIniSetting(): void
    {
        Application::boot($this->rootDir);
        Application::displayErrors();

        $this->assertEquals('On', ini_get('display_errors'));
    }

    protected function tearDown(): void
    {
//        @unlink($this->rootDir . '/configure.json');
    }
}
