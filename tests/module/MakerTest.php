<?php

namespace KpfTest\Module;

use Kpf\Application;
use Kpf\Constant;
use Kpf\Maker;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';

class MakerTest extends TestCase
{
    const FORCE_OVERWRITE = true;

    public function testMakeController()
    {
        Maker::make('controller', 'TestSample', self::FORCE_OVERWRITE);
        $path = Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'TestSample.php';
        $this->assertFileExists($path);
        $this->assertStringContainsString('class TestSample', file_get_contents($path));
    }

    public function testMakeModel()
    {
        Maker::make('model', 'SampleModel', self::FORCE_OVERWRITE);
        $path = Application::getDirectory()->get(Constant::DIR_MODEL) . 'SampleModel.php';
        $this->assertFileExists($path);
        $this->assertStringContainsString('class SampleModel', file_get_contents($path));
    }

    public function testMakeView()
    {
        Maker::make('view', 'TestSample', self::FORCE_OVERWRITE);
        $path = Application::getDirectory()->get(Constant::DIR_VIEW) . 'testsample.twig';
        $this->assertFileExists($path);
        $this->assertStringContainsString('{{ memo }}', file_get_contents($path));
    }

    public function testMakePage()
    {
        Maker::make('page', 'Unit/PageTest', self::FORCE_OVERWRITE);
        $controllerPath = Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'Unit/PageTest.php';
        $viewPath = Application::getDirectory()->get(Constant::DIR_VIEW) . 'unit/pagetest.twig';

        $this->assertFileExists($controllerPath);
        $this->assertFileExists($viewPath);
    }

    public function testTemplateOverrideWithLocalResource(): void
    {
        // Ensure resource is copied for override test
        Maker::initResource();
        $resourcePath = Maker::getProjectRoot() . '/kpf-resource/views/blank.twig.txt';
        file_put_contents($resourcePath, '{{ override_test }}');

        Maker::make('view', 'OverrideTest', self::FORCE_OVERWRITE);
        $path = Application::getDirectory()->get(Constant::DIR_VIEW) . 'overridetest.twig';
        $this->assertFileExists($path);
        $this->assertStringContainsString('{{ override_test }}', file_get_contents($path));
    }

    public function testGetProjectRootWithoutEnvFallsBack()
    {
        // 백업
        $originalGlobal = $GLOBALS['EXAMPLE_ROOT'] ?? null;
        $originalServer = $_SERVER['EXAMPLE_ROOT'] ?? null;

        // 환경변수 제거
        unset($GLOBALS['EXAMPLE_ROOT']);
        unset($_SERVER['EXAMPLE_ROOT']);

        // 후보 경로 중 하나로 테스트
        $testRoot = dirname(__DIR__, 2);  // 또는 dirname(__DIR__, 4)로 교체 가능
        $tempConfig = $testRoot . '/configure.json';

        // 임시 파일 생성
        file_put_contents($tempConfig, json_encode(['test' => true]));

        // Maker 인스턴스 및 테스트 실행
        $maker = new Maker();
        $result = $this->invokeMethod($maker, 'getProjectRoot');

        $this->assertEquals(realpath($testRoot), $result);

        // 정리
        @unlink($tempConfig);

        // 환경변수 복원
        if ($originalGlobal !== null) {
            $GLOBALS['EXAMPLE_ROOT'] = $originalGlobal;
        } else {
            unset($GLOBALS['EXAMPLE_ROOT']);
        }

        if ($originalServer !== null) {
            $_SERVER['EXAMPLE_ROOT'] = $originalServer;
        } else {
            unset($_SERVER['EXAMPLE_ROOT']);
        }
    }

    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    public static function tearDownAfterClass(): void
    {
        // Clean up generated files
        @unlink(Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'TestSample.php');
        @unlink(Application::getDirectory()->get(Constant::DIR_MODEL) . 'SampleModel.php');
        @unlink(Application::getDirectory()->get(Constant::DIR_VIEW) . 'testsample.twig');
        @unlink(Application::getDirectory()->get(Constant::DIR_CONTROLLER) . 'Unit/PageTest.php');
        @unlink(Application::getDirectory()->get(Constant::DIR_VIEW) . 'unit/pagetest.twig');
        @unlink(Application::getDirectory()->get(Constant::DIR_VIEW) . 'overridetest.twig');

        // Clean up custom resource and config
        $projectRoot = Maker::getProjectRoot();
        @unlink($projectRoot . '/kpf-resource/views/blank.twig.txt');
        @rmdir($projectRoot . '/kpf-resource/views');
        @rmdir($projectRoot . '/kpf-resource/models');
        @rmdir($projectRoot . '/kpf-resource/controllers');
        // 재귀적으로 kpf-resource 삭제
        self::deleteDirectory($projectRoot . '/kpf-resource');
        @unlink($projectRoot . '/.kpfconfig.json');
    }

    protected static function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                self::deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}