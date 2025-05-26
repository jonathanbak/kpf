<?php
$paths = [
    __DIR__ . '/../../../../vendor/autoload.php', // vendor 안에서 실행될 경우
    __DIR__ . '/../vendor/autoload.php',   // 개발 루트에서 실행될 경우
    dirname(__DIR__, 4) . '/vendor/autoload.php', // Fallback
];

foreach ($paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

use Kpf\Application;
use Kpf\Installer;

try {
    define('ROOT_DIR', getcwd());
    $baseDir = ROOT_DIR ;
    $namespace = !empty($_SERVER['argv'][1])? $_SERVER['argv'][1] : "";
    if (empty($namespace)) {
        echo "Usage: php vendor/jonathanbak/kpf/bin/install.php <namespace>\n";
        exit(1);
    }
    $Installer = new Installer();
    $Installer->setup($baseDir, $namespace);
    // 2. 설정 기반 Application 초기화
    Application::boot($baseDir);

    // 3. 실제 설치
    $Installer->install();

    Installer::success();
} catch (\Exception $e) {
    Installer::fail($e->getMessage());
}