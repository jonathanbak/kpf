<?php

namespace Kpf;

use Kpf\Exception\DirectoryException;

class Directory
{
    const DIRECTORY_SEPARATOR = '/';

    protected $rootDir = Constant::NONE;
    protected $configDir;
    protected $controllerDir;
    protected $modelDir;
    protected $viewDir;
    protected $tempDir;
    protected $logDir;
    protected $homeDir;
    protected $compileDir;

    /**
     * @param $dir
     * @return string
     * @throws DirectoryException
     */
    public function get($dir): string
    {
        $dirPath = [];
        $dirPath[] = $this->root();
        switch ($dir) {
            case Constant::DIR_CONFIG:
                $dirPath[] = $this->getConfig();
                break;
            case Constant::DIR_CONTROLLER:
                $dirPath[] = $this->getController();
                break;
            case Constant::DIR_MODEL:
                $dirPath[] = $this->getModel();
                break;
            case Constant::DIR_VIEW:
                $dirPath[] = $this->getView();
                break;
            case Constant::DIR_TEMP:
                $dirPath[] = $this->getTemp();
                break;
            case Constant::DIR_LOG:
                $dirPath[] = $this->getLog();
                break;
            case Constant::DIR_HOME:
                $dirPath[] = $this->getHome();
                break;
            case Constant::DIR_COMPILE:
                $dirPath[] = $this->getCompile();
                break;
        }

        return implode(Directory::DIRECTORY_SEPARATOR, $dirPath) . Directory::DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     * @throws DirectoryException
     */
    public function root(): string
    {
        if ($this->rootDir == Constant::NONE) throw new DirectoryException(Error::REQUIRE_ROOT_DIR);
        return $this->rootDir;
    }

    public function setRoot($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function setHome($homeDir)
    {
        $this->homeDir = $homeDir;
    }

    public function setController($controllerDir)
    {
        $this->controllerDir = $controllerDir;
    }

    public function setModel($modelDir)
    {
        $this->modelDir = $modelDir;
    }

    public function setView($viewDir)
    {
        $this->viewDir = $viewDir;
    }

    public function setTemp($tempDir)
    {
        $this->tempDir = $tempDir;
    }

    public function setLog($logDir)
    {
        $this->logDir = $logDir;
    }

    public function setConfig($configDir)
    {
        $this->configDir = $configDir;
    }

    public function setCompile($compileDir)
    {
        $this->compileDir = $compileDir;
    }

    public function getConfig(): string
    {
        return $this->configDir ? $this->configDir : Constant::DIR_CONFIG;
    }

    public function getController(): string
    {
        return $this->controllerDir ? $this->controllerDir : Constant::DIR_CONTROLLER;
    }

    public function getModel(): string
    {
        return $this->modelDir ? $this->modelDir : Constant::DIR_MODEL;
    }

    public function getView(): string
    {
        return $this->viewDir ? $this->viewDir : Constant::DIR_VIEW;
    }

    public function getTemp(): string
    {
        return $this->tempDir ? $this->tempDir : Constant::DIR_TEMP;
    }

    public function getLog(): string
    {
        return $this->logDir ? $this->logDir : Constant::DIR_LOG;
    }

    public function getCompile(): string
    {
        return $this->compileDir ? $this->compileDir : Constant::DIR_COMPILE;
    }

    public function getHome(): string
    {
        return $this->homeDir ? $this->homeDir : Constant::DIR_HOME;
    }
}
