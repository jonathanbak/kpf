#!/usr/bin/env php
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

use Kpf\Bin\MakerRunner;
use Kpf\Installer;

try {
    $output = MakerRunner::run($_SERVER['argv']);
    echo $output;
    Installer::success();
} catch (\Throwable $e) {
    Installer::fail($e->getMessage());
}