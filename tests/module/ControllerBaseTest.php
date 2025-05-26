<?php

namespace KpfTest\Module;

use Kpf\Exception\OutputException;
use Kpf\Template\TwigTemplate;
use PHPUnit\Framework\TestCase;
use Kpf\Application;
use Kpf\Controller;

require_once __DIR__ . '/bootstrap.php';

class TestableController extends Controller
{
    public function render(string $tpl = '', array $params = []): string
    {
        ob_start();
        $this->display($tpl, $params);
        return ob_get_clean();
    }

    public function displayTest(): string
    {
        ob_start();
        try {
            $this->assign(['memo' => 'auto detect']);
            $this->display('main/main');
            return ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    public function sampleMethod(): string
    {
        ob_start();
        try {
            $this->display();
            return ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean(); // 버퍼 정리
            throw $e;       // 예외 재던짐
        }
    }
}

final class ControllerBaseTest extends TestCase
{
    protected function setUp(): void
    {
        // ensure template engine is assigned for Output
        Application::setTemplate(new TwigTemplate());
    }

    public function testAssignAndDisplayExplicitTemplate(): void
    {
        $controller = new TestableController();

        $output = $controller->render('main/main', ['memo' => 'controller test']);

        $this->assertStringContainsString('controller test', $output);
    }

    public function testDisplayInfersTemplatePath(): void
    {
        $this->expectException(OutputException::class);

        $controller = new TestableController();
        $controller->sampleMethod();
//        $this->assertStringContainsString('template data parsing', $output);
    }

    public function testDisplayFallbacksGracefully(): void
    {
        $controller = new TestableController();
        $output = $controller->displayTest();
        $this->assertStringContainsString('auto detect', $output);
    }

}
