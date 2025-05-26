<?php

namespace KpfTest\Module;

use PHPUnit\Framework\TestCase;
use Example\Controller\Main\Main;

require_once __DIR__ . '/bootstrap.php';

final class ControllerSampleTest extends TestCase
{
    public function testDisplayReturnsHtmlOutput(): void
    {
        // Output 캡처
        ob_start();

        $controller = new Main();
        $controller->main();

        $output = ob_get_clean();

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Welcome to my awesome homepage', $output);
    }
}
