<?php

namespace Kpf;

use Kpf\Exception\ConfigException;
use Kpf\Helper\ArrayMerge;
use Kpf\Helper\ArraySearch;
use Kpf\Helper\File;

class Config
{
    protected static $configItem = array();
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
        if ($isCliCheck) $this->prepareCli();

        $configFile = $this->getCommonConfigFile();
        if (!is_file($configFile)) {
            throw new ConfigException("Missing configuration file: configure.json");
        }
        $this->commonConfig = $this->loadFile($configFile);
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
     */
    public function common(string $key = '')
    {
        if ($key) {
            return ArraySearch::searchValueByKey($key, $this->commonConfig);
        } else {
            return $this->commonConfig;
        }
    }

    public static function getDefaultConfigure(array $configParams = [])
    {
        $defaultConfigure = [
            "description" => "Kubernetes PHP Framework",
            "charset" => "utf-8",
            "development" => "1",
            "debugMode" => 1,
            "displayErrors" => "on",
            "secure" => "on",
            "allowIps" => ["127.0.0.1"],
            "namespace" => "",
            "dirs" => [
                "config" => "config",
                "controller" => "controllers",
                "model" => "models",
                "view" => "views",
                "temp" => "_tmp",
                "log" => "logs",
                "home" => "html",
                "compile" => "_tmp/compile"
            ],
            "templateExtension" => "twig",
            "dbSet" => "common.db",
            "route" => "common.route",
            "installed" => 0
        ];

        return ArrayMerge::recursive_distinct($defaultConfigure, $configParams);
    }

    /**
     * @throws ConfigException
     */
    public function prepareCli()
    {
//        $backtrace = debug_backtrace();
//        $backtrace = array_pop($backtrace);
        if ($this->isCli() == true) {
            if (empty($_SERVER['REMOTE_ADDR'])) {
                $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            }
            array_shift($_SERVER['argv']);
            $_SERVER['argc']--;

            if($_SERVER['argc'] > 0) {
                $_SERVER['REQUEST_URI'] = $_SERVER['argv'][0];
                $urlInfo = parse_url($_SERVER['REQUEST_URI']);
                if(!empty($urlInfo['host'])) $_SERVER['HTTP_HOST'] = $urlInfo['host'];
                if(!empty($urlInfo['path'])) $_SERVER['REQUEST_URI'] = $urlInfo['path'];
                if(!empty($urlInfo['query'])) {
                    $_SERVER['QUERY_STRING'] = $urlInfo['query'];
                    parse_str($urlInfo['query'], $_GET);
                }
            }
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
    public function loadConfig(string $configFileName, string $findKey = Constant::NONE)
    {
        $configDir = Application::getDirectory()->get(Constant::DIR_CONFIG);
        $configFile = $configFileName . Constant::DOT . Constant::CONFIG_EXTENSION;
        $fullPath = $configDir . Directory::DIRECTORY_SEPARATOR . $configFile;

        if (!is_file($fullPath)) {
            throw new ConfigException($configFileName . new Error(Error::NOT_FOUND_CONFIG) . "(" . $configFile . ")");
        }

        if (!isset(self::$configItem[$configFileName])) {
            self::$configItem[$configFileName] = $this->loadFile($fullPath);
        }

        if ($findKey) {
            return ArraySearch::searchValueByKey($findKey, self::$configItem[$configFileName]);
        } else {
            return self::$configItem[$configFileName];
        }
    }

    public function getDbConfigFilename()
    {
        return $this->common(Constant::KEY_DB_SET) . Constant::DOT . Constant::CONFIG_EXTENSION;
    }

    public function getRouteConfigFilename()
    {
        return $this->common(Constant::KEY_ROUTE) . Constant::DOT . Constant::CONFIG_EXTENSION;
    }

    public static function config(string $configFileName)
    {
        return Application::getConfig()->loadConfig($configFileName);
    }

    public static function global(string $key)
    {
        return Application::getConfig()->common($key);
    }
}