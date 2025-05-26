<?php

namespace Kpf;

use Kpf\Helper\File;
use Kpf\Helper\ArrayMerge;

class Installer
{
    protected $rootDir;
    protected $namespace;
    protected $config;
    protected $directory;

    /**
     * 1단계: 설치 초기 셋업 (configure.json 생성)
     */
    public function setup(string $rootDir, string $namespace): void
    {
        $this->rootDir = rtrim($rootDir, '/');
        $this->namespace = ucfirst($namespace);

        File::makeDir($this->rootDir);

        $defaultConfigure = Config::getDefaultConfigure(["namespace" => $this->namespace]);

        File::put_json_pretty($this->rootDir . '/' . Constant::COMMON_CONFIG_FILE, $defaultConfigure);
    }

    /**
     * 2단계: 실제 설치
     */
    public function install()
    {
        $this->directory = new Directory();
        $this->directory->setRoot($this->rootDir);

        $this->config = new Config();
        $this->config->init();

        if (!$this->config->common('installed')) {
            $dirs = $this->config->common('dirs');

            foreach ($dirs as $dir) {
                $chmod = in_array($dir, [Constant::DIR_TEMP, Constant::DIR_LOG, Constant::DIR_COMPILE]) ? 0777 : 0755;
                File::makeDir($this->directory->get($dir), $chmod);
            }

            $this->createDbConfigure();
            $this->createRouteConfigure();

            $newConfigure = ArrayMerge::recursive_distinct($this->config->common(), ['installed' => '1']);
            File::put_json_pretty($this->config->getCommonConfigFile(), $newConfigure);

            $this->createSampleController();
            $this->createSampleModel();
            $this->createSampleTemplate();
            $this->createSampleIndex();

            echo "Install Complete.\n";
        } else {
            echo "Already installed.\n";
        }
    }

    protected function createDbConfigure()
    {
        $dbConfigure = $this->baseDbConfigure();
        $fileName = $this->directory->get(Constant::DIR_CONFIG) . $this->config->getDbConfigFilename();
        File::put_json_pretty($fileName, $dbConfigure);
    }

    protected function createRouteConfigure()
    {
        $baseRoute = [
            "default" => "/",
            "routing" => [
                "/" => "main/main",
                "/test/detail/([A-Za-z0-9]+)" => "test/detail/item"
            ]
        ];

        $fileName = $this->directory->get(Constant::DIR_CONFIG) . $this->config->getRouteConfigFilename();
        File::put_json_pretty($fileName, $baseRoute);
    }

    protected function baseDbConfigure(): array
    {
        return [
            "driver" => "mysqli",
            "host" => "localhost",
            "user" => "user",
            "password" => "password",
            "database" => "dbname",
            "port" => "3306",
            "charset" => "utf8"
        ];
    }

    protected function createSampleController()
    {
        $namespace = ucfirst($this->config->common('namespace'));
        foreach ([
                     'Main/Main.php.txt' => 'Main/Main.php',
                     '_sys/Test.php.txt' => '_sys/Test.php'
                 ] as $template => $target) {
            $templatePath = dirname(__DIR__) . "/resource/controllers/" . $template;
            $targetPath = $this->directory->get(Constant::DIR_CONTROLLER) . $target;
            $content = str_replace('<<namespace>>', $namespace, File::load($templatePath));
            File::write($targetPath, $content);
        }
    }

    protected function createSampleModel()
    {
        $namespace = ucfirst($this->config->common('namespace'));
        $template = dirname(__DIR__) . "/resource/models/Account/Member.php.txt";
        $target = $this->directory->get(Constant::DIR_MODEL) . "Account/Member.php";
        $content = str_replace('<<namespace>>', $namespace, File::load($template));
        File::write($target, $content);
    }

    protected function createSampleTemplate()
    {
        $views = [
            'base.twig.txt' => 'base.twig',
            'main/main.twig.txt' => 'main/main.twig'
        ];

        foreach ($views as $template => $target) {
            $templatePath = dirname(__DIR__) . "/resource/views/" . $template;
            $targetPath = $this->directory->get(Constant::DIR_VIEW) . $target;
            File::write($targetPath, File::load($templatePath));
        }
    }

    protected function createSampleIndex()
    {
        $namespace = ucfirst($this->config->common('namespace'));
        $template = dirname(__DIR__) . "/resource/html/index.php.txt";
        $target = $this->directory->get(Constant::DIR_HOME) . "index.php";
        $content = str_replace('<<namespace>>', $namespace, File::load($template));
        File::write($target, $content);
    }

    public static function success()
    {
        echo "OK.\n";
    }

    public static function fail($errMessage = '')
    {
        echo $errMessage . "\nFAIL.\n";
    }
}