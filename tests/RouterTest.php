<?php declare(strict_types=1);

namespace Kpf\Tests;

use Kpf\Application;
use Kpf\Helper\Uri;
use Kpf\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        Application::init($baseDir);
    }

    public function testExecute(): void
    {
        $baseDir = dirname(__DIR__) . '/example';
        $currentUri = Uri::get("/");
        try{
            Router::execute($currentUri);
            $this->assertTrue(true);
        }catch(\Exception $e){
            $this->assertStringContainsString("Not Found Class File",$e->getMessage());
        }
    }


}