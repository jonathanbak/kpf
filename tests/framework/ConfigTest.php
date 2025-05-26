<?php

namespace KpfTest\Framework;

use Kpf\Application;
use Kpf\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    private $config;
    private $rootDir;

    protected function setUp(): void
    {
        $this->rootDir = dirname(__DIR__,2) . '/example';
        if (!file_exists($this->rootDir)) {
            mkdir($this->rootDir, 0755, true);
        }

        // 기본 configure.json 생성
        file_put_contents($this->rootDir . '/configure.json', json_encode([
            'namespace' => 'Example',
            'installed' => 1,
            'dirs' => [
                'config' => 'config',
                'controller' => 'controllers',
                'model' => 'models',
                'view' => 'views',
                'temp' => '_tmp',
                'log' => 'logs',
                'home' => 'html',
                'compile' => '_tmp/compile'
            ],
            'dbSet' => 'common.db',
            'route' => 'common.route'
        ], JSON_PRETTY_PRINT));

        Application::boot($this->rootDir);
        $this->config = Application::getConfig();
    }

    public function testCommonReturnsFullConfig(): void
    {
        $config = $this->config->common();
        $this->assertArrayHasKey('dirs', $config);
        $this->assertEquals('Example', $config['namespace']);
    }

    public function testCommonNestedValue(): void
    {
        $this->assertEquals('controllers', $this->config->common('dirs.controller'));
    }

    public function testGetCommonConfigFile(): void
    {
        $expectedPath = $this->rootDir . '/configure.json';
        $this->assertEquals($expectedPath, $this->config->getCommonConfigFile());
    }

    public function testGetDbAndRouteFilenames(): void
    {
        $this->assertEquals('common.db.json', $this->config->getDbConfigFilename());
        $this->assertEquals('common.route.json', $this->config->getRouteConfigFilename());
    }

    public function testLoadConfigFile(): void
    {
        $configDir = $this->rootDir . '/config';
        if (!file_exists($configDir)) mkdir($configDir, 0755, true);

        file_put_contents($configDir . '/sample.json', json_encode([
            'enabled' => true,
            'driver' => 'memory'
        ], JSON_PRETTY_PRINT));

        $data = $this->config->loadConfig('sample');
        $this->assertTrue($data['enabled']);
        $this->assertEquals('memory', $data['driver']);
    }

    public function testLoadConfigFileWithKey(): void
    {
        $configDir = $this->rootDir . '/config';
        file_put_contents($configDir . '/feature.json', json_encode([
            'cache' => [ 'type' => 'file' ]
        ], JSON_PRETTY_PRINT));

        $value = $this->config->loadConfig('feature', 'cache.type');
        $this->assertEquals('file', $value);
    }

    public function testLoadConfigFileThrowsIfMissing(): void
    {
        $this->expectException(ConfigException::class);
        $this->config->loadConfig('not_exist');
    }

    protected function tearDown(): void
    {
        @unlink($this->rootDir . '/configure.json');
        @unlink($this->rootDir . '/config/sample.json');
        @unlink($this->rootDir . '/config/feature.json');
        @rmdir($this->rootDir . '/config');
    }
}