<?php

namespace Kpf;

use Kpf\Exception\ConfigException;
use Kpf\Helper\ArraySearch;
use Kpf\Helper\File;

class Config
{
    static $configItem = array();
    protected $commonConfig;

    /**
     * 디렉토리 셋팅
     * @param bool $isCliCheck
     * @throws ConfigException
     * @throws Exception\DirectoryException
     * @throws Exception\Exception
     */
    public function init(bool $isCliCheck = true)
    {
        if ($isCliCheck) $this->cli();
        Application::getDirectory()->setConfig($this->common('dirs.config'));
        Application::getDirectory()->setController($this->common('dirs.controller'));
        Application::getDirectory()->setModel($this->common('dirs.model'));
        Application::getDirectory()->setView($this->common('dirs.view'));
        Application::getDirectory()->setTemp($this->common('dirs.temp'));
        Application::getDirectory()->setLog($this->common('dirs.log'));
        Application::getDirectory()->setHome($this->common('dirs.home'));
        Application::getDirectory()->setCompile($this->common('dirs.compile'));
    }

    /**
     * @return string
     * @throws Exception\DirectoryException
     */
    public function getCommonConfigFile(): string
    {
        $configDir = Application::getDirectory()->root();

        return $configDir . Directory::DIRECTORY_SEPARATOR . Constant::COMMON_CONFIG_FILE;
    }

    /**
     * @param string $key
     * @return array|string|int
     * @throws ConfigException
     * @throws Exception\DirectoryException
     * @throws Exception\Exception
     */
    public function common(string $key = '')
    {
        $configDir = Application::getDirectory()->root();

        if (!is_file($configDir . Directory::DIRECTORY_SEPARATOR . Constant::COMMON_CONFIG_FILE)) {
            $this->createCommonConfig($configDir . Directory::DIRECTORY_SEPARATOR . Constant::COMMON_CONFIG_FILE);
        }

        $this->commonConfig = empty($this->commonConfig) ? $this->loadFile($configDir . Directory::DIRECTORY_SEPARATOR . Constant::COMMON_CONFIG_FILE) : $this->commonConfig;

        if ($key) {
            return ArraySearch::searchValueByKey($key, $this->commonConfig);
        } else {
            return $this->commonConfig;
        }
    }

    /**
     * @param $configFileName
     * @throws Exception\Exception
     */
    public function createCommonConfig($configFileName)
    {
        $namespace = ucfirst(Application::getNamespace());
        $defaultConfigure = array(
            "description" => "Kubernetes PHP Framework",
            "charset" => "utf-8",
            "development" => "1",
            "displayErrors" => "on",
            "secure" => "on",
            "allowIps" => array(
                "127.0.0.1"
            ),
            "namespace" => $namespace,
            "dirs" => array(
                "config" => "config",
                "controller" => "controllers",
                "model" => "models",
                "view" => "views",
                "temp" => "_tmp",
                "log" => "logs",
                "home" => "html",
                "compile" => "_tmp/compile"
            ),
            "templateExtension" => "twig",
            "dbSet" => "common.db",
            "route" => "common.route",
            "installed" => 0
        );

        File::put_json_pretty($configFileName, $defaultConfigure);

    }

    /**
     * @throws ConfigException
     */
    public function cli()
    {
        $backtrace = debug_backtrace();
        $backtrace = array_pop($backtrace);
        if ($this->isCli() == true && $backtrace['function'] != 'spl_autoload_call' && $backtrace['function'] != 'include') {
            if (empty($_SERVER['REMOTE_ADDR'])) {
                $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            }
            array_shift($_SERVER['argv']);
            array_shift($_SERVER['argv']);
            $_SERVER['argc']--;
//            $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '');
            $_SERVER['REQUEST_METHOD'] = 'GET';
        }
    }

    /**
     * @return bool
     */
    public function isCli(): bool
    {
        return php_sapi_name() == "cli" ? true : false;
    }

    /**
     * @param $configFile
     * @return array
     * @throws ConfigException
     */
    protected function loadFile($configFile): array
    {
        if (!is_file($configFile)) {
            throw new ConfigException(new Error(Error::NOT_FOUND_CONFIG) . "(" . $configFile . ")");
        }
        $configure = file_get_contents($configFile);
        return json_decode($configure, true);
    }

    /**
     * @param string $configFileName
     * @param string $findKey
     * @return mixed|string
     * @throws ConfigException
     */
    public function load(string $configFileName, string $findKey = Constant::NONE)
    {
        $configDir = Application::getDirectory()->get(Constant::DIR_CONFIG);
        $configFile = $configFileName . Constant::DOT . Constant::CONFIG_EXTENSION;

        if (!is_file($configDir . Directory::DIRECTORY_SEPARATOR . $configFile)) {
            throw new ConfigException($configFileName . new Error(Error::NOT_FOUND_CONFIG) . "(" . $configFile . ")");
        }

        self::$configItem[$configFileName] = !isset(self::$configItem[$configFileName]) ? $this->loadFile($configDir . Directory::DIRECTORY_SEPARATOR . $configFile) : self::$configItem[$configFileName];

        if ($findKey) {
            return ArraySearch::searchValueByKey($findKey, self::$configItem[$configFileName]);
        } else {
            return self::$configItem[$configFileName];
        }
    }

    public static function config(string $configFileName)
    {
        return Application::getConfig()->load($configFileName);
    }

    public static function global(string $key)
    {
        return Application::getConfig()->common($key);
    }
}