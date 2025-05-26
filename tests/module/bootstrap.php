<?php

use Kpf\Application;
use Kpf\Installer;

define('APP_ROOT', dirname(__DIR__,2));
require_once APP_ROOT . '/vendor/autoload.php';

$exampleRoot = APP_ROOT . '/'.$GLOBALS['EXAMPLE_ROOT'];
if (!is_dir($exampleRoot)) {
    throw new \RuntimeException("Example root directory not found: " . $exampleRoot);
}

// ✅ 공통설정파일 경로
$configFile = $exampleRoot . '/configure.json';
$exampleNameSpace = $GLOBALS['EXAMPLE_NAMESPACE'];

$configFile = $exampleRoot . '/configure.json';

// ✅ 설치 여부 확인
if (!is_file($configFile) || !json_decode(file_get_contents($configFile), true)['installed']) {
    // 설치 처리
    $installer = new Installer();
    $installer->setup($exampleRoot, $exampleNameSpace);   // configure.json 생성
    Application::boot($exampleRoot);
    $installer->install(); // 디렉토리 및 샘플 생성
}

Application::boot($exampleRoot);