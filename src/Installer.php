<?php

namespace Kpf;

use Kpf\Exception\Exception;
use Kpf\Helper\ArrayMerge;
use Kpf\Helper\File;

class Installer
{
    /**
     * @param $namespace
     * @throws Exception
     * @throws Exception\ConfigException
     * @throws Exception\DirectoryException
     */
    public function install($namespace)
    {
        Application::setNamespace($namespace);
        if (!Application::getConfig()->common('installed')) {
            $dirs = Application::getConfig()->common("dirs");
            foreach($dirs as $dir){
                $chmod = 0755;
                if($dir==Constant::DIR_TEMP || $dir==Constant::DIR_LOG || $dir==Constant::DIR_COMPILE) $chmod = 0777;
                $this->makeDir( Application::getDirectory()->get($dir), $chmod );
            }

            $this->createDbConfigure();

            $this->createRouteConfigure();

            $newConfigure = ArrayMerge::recursive_distinct(Application::getConfig()->common(), array('installed' => '1'));
            $configFileName = Application::getConfig()->getCommonConfigFile();
            File::put_json_pretty($configFileName, $newConfigure);

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
        $configFileName = Application::getDirectory()->get(Constant::DIR_CONFIG) . Application::getConfig()->common(Constant::KEY_DB_SET)  . Constant::DOT . Constant::CONFIG_EXTENSION;
        File::put_json_pretty($configFileName, $dbConfigure);
    }

    public function makeDir(string $dir, $chmod = 0755): bool
    {
        if (!is_dir($dir)) return mkdir($dir, $chmod, true);
        return true;
    }

    protected function createRouteConfigure(){
        $baseRoute = array(
            "default" => "/",
            "routing" => array(
                "/" => "main/main",
                "/test/detail/([A-Za-z0-9]+)" => "test/detail/item"
            )
        );

        $configFileName = Application::getDirectory()->get(Constant::DIR_CONFIG) . Application::getConfig()->common(Constant::KEY_ROUTE)  . Constant::DOT . Constant::CONFIG_EXTENSION;
        File::put_json_pretty($configFileName, $baseRoute);
    }

    protected function baseDbConfigure(): array
    {
        return array(
            "driver" => "mysqli",
            "host" => "localhost",
            "user" => "user",
            "password" => "password",
            "database" => "dbname",
            "port" => "3306",
            "charset" => "utf8"
        );
    }

    protected function createSampleController()
    {
        $resourceFile = dirname(__DIR__) . "/resource/controllers/Main/Main.php.txt";
        $content = File::load($resourceFile);
        $content = str_replace('<<namespace>>',ucfirst(Application::getNamespace()),$content);
        $targetFile = Application::getDirectory()->get(Constant::DIR_CONTROLLER)."Main/Main.php";
        File::write($targetFile, $content);

        $resourceFile = dirname(__DIR__) . "/resource/controllers/_sys/Test.php.txt";
        $content = File::load($resourceFile);
        $content = str_replace('<<namespace>>',ucfirst(Application::getNamespace()),$content);
        $targetFile = Application::getDirectory()->get(Constant::DIR_CONTROLLER)."_sys/Test.php";
        File::write($targetFile, $content);
    }

    protected function createSampleModel()
    {
        $resourceFile = dirname(__DIR__) . "/resource/models/Account/Member.php.txt";
        $content = File::load($resourceFile);
        $content = str_replace('<<namespace>>',ucfirst(Application::getNamespace()),$content);
        $targetFile = Application::getDirectory()->get(Constant::DIR_MODEL)."Account/Member.php";
        File::write($targetFile, $content);
    }

    protected function createSampleTemplate()
    {
        $resourceFile = dirname(__DIR__) . "/resource/views/base.twig.txt";
        $content = File::load($resourceFile);
        $targetFile = Application::getDirectory()->get(Constant::DIR_VIEW)."base.twig";
        File::write($targetFile, $content);

        $resourceFile = dirname(__DIR__) . "/resource/views/main/main.twig.txt";
        $content = File::load($resourceFile);
        $targetFile = Application::getDirectory()->get(Constant::DIR_VIEW)."main/main.twig";
        File::write($targetFile, $content);
    }

    protected function createSampleIndex()
    {
        $resourceFile = dirname(__DIR__) . "/resource/html/index.php.txt";
        $content = File::load($resourceFile);
        $content = str_replace('<<namespace>>',ucfirst(Application::getNamespace()),$content);
        $targetFile = Application::getDirectory()->get(Constant::DIR_HOME)."index.php";
        File::write($targetFile, $content);
    }

    public static function success()
    {
        echo "OK.\n";
    }

    public static function fail( $errMessage = '' )
    {
        echo $errMessage."\n";
        echo "FAIL.\n";
    }
}