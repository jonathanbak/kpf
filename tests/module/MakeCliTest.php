<?php

namespace KpfTest\Module;

use Kpf\Application;
use Kpf\Bin\MakerRunner;
use Kpf\Constant;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';

final class MakeCliTest extends TestCase
{
    protected static $projectRoot;

    protected function setUp(): void
    {
        self::$projectRoot = realpath($GLOBALS['EXAMPLE_ROOT'] ?? dirname(__DIR__, 2) . '/example');

        if (!defined('ROOT_DIR')) {
            define('ROOT_DIR', self::$projectRoot);
        }

        Application::init(ROOT_DIR);
    }

    public function testRunCreatesController()
    {
        $output = MakerRunner::run(['make.php', 'controller', 'CliTestController', '--force']);
        $expectedFile = Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'CliTestController.php';

        $this->assertFileExists($expectedFile);
        $this->assertStringContainsString('Created controller', $output);
    }

    public function testRunCreatesModel()
    {
        $output = MakerRunner::run(['make.php', 'model', 'CliTestModel', '--force']);
        $expectedFile = Application::getDirectory()->get(Constant::DIR_MODEL) . 'CliTestModel.php';

        $this->assertFileExists($expectedFile);
        $this->assertStringContainsString('Created model', $output);
    }

    public function testRunCreatesView()
    {
        $output = MakerRunner::run(['make.php', 'view', 'clitestview', '--force']);
        $expectedFile = Application::getDirectory()->get(Constant::DIR_VIEW) . 'clitestview.twig';

        $this->assertFileExists($expectedFile);
        $this->assertStringContainsString('Created view', $output);
    }

    public function testRunCreatesPage()
    {
        $output = MakerRunner::run(['make.php', 'page', 'Demo/Welcome', '--force']);

        $expectedController = Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'Demo/Welcome.php';
        $expectedView = Application::getDirectory()->get(Constant::DIR_VIEW) . 'demo/welcome.twig';

        $this->assertFileExists($expectedController);
        $this->assertFileExists($expectedView);
        $this->assertStringContainsString('Created controller', $output);
        $this->assertStringContainsString('Created view', $output);
    }

    public function testRunInitCreatesResourceDirectory()
    {
        $output = MakerRunner::run(['make.php', 'init']);

        $resourcePath = self::$projectRoot . '/kpf-resource';
        $configFile = self::$projectRoot . '/.kpfconfig.json';

        $this->assertDirectoryExists($resourcePath);
        $this->assertFileExists($configFile);
        $this->assertStringContainsString('kpf-resource created', $output);
    }

    protected function tearDown(): void
    {
        @unlink(Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'CliTestController.php');
        @unlink(Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'Demo/Welcome.php');
        @rmdir(Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'Demo');

        @unlink(Application::getDirectory()->get(Constant::DIR_MODEL) . 'CliTestModel.php');

        @unlink(Application::getDirectory()->get(Constant::DIR_VIEW) . 'clitestview.twig');
        @unlink(Application::getDirectory()->get(Constant::DIR_VIEW) . 'demo/welcome.twig');
        @rmdir(Application::getDirectory()->get(Constant::DIR_VIEW) . 'demo');

        // init 관련 정리
        $configFile = self::$projectRoot . '/.kpfconfig.json';
        $resourcePath = self::$projectRoot . '/kpf-resource';

        if (is_file($configFile)) {
            @unlink($configFile);
        }

        if (is_dir($resourcePath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($resourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($iterator as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }
            @rmdir($resourcePath);
        }
    }
}