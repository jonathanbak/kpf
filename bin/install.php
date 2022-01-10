<?php
$vendorDir = dirname(dirname(dirname(__DIR__)));
require($vendorDir.'/autoload.php');

use Kpf\Application;
use Kpf\Installer;

try {
    define('ROOT_DIR', getcwd());
    $baseDir = ROOT_DIR ;
    $namespace = !empty($_SERVER['argv'][1])? $_SERVER['argv'][1] : "";
    Application::install($baseDir, $namespace);
    Installer::success();
} catch (\Exception $e) {
    Installer::fail($e->getMessage());
}