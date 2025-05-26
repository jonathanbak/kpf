<?php

namespace Kpf;

use \Composer\Autoload\ClassLoader;
use Kpf\Exception\ConfigException;
use Kpf\Exception\DirectoryException;
use Kpf\Exception\Exception;
use Kpf\Helper\Uri;
use Kpf\Template\TwigTemplate;
use PSF\Template;

class Application extends Singleton
{
    protected $template;
    protected $autoloader;
    protected $config;
    protected $directory;
    protected $router;

    /**
     * 설정 로딩
     * @param string $rootDir 프로젝트 Root Dir 절대경로
     * @throws Exception
     * @throws Exception\SecurityException
     */
    protected function init(string $rootDir)
    {
        $this->boot($rootDir);
    }

    protected function boot(string $rootDir, ?Directory $dir = null, ?Config $cfg = null): void
    {
        $this->autoloader = ClassLoader::getRegisteredLoaders();

        $this->directory = $dir ?? new Directory();
        $this->config = $cfg ?? new Config();

        $this->directory->setRoot($rootDir);
        $this->config->init();
        $this->applyDirectoryConfig();

        if ($this->config->common('installed')) {
            $routeConfigList = $this->config->loadConfig($this->config->common(Constant::KEY_ROUTE));
            foreach($routeConfigList["routing"] as $uri => $routeAction) {
                Router::get($uri, $routeAction);
            }
            $siteNamespace = $this->config->common(Constant::KEY_NAMESPACE);
            Router::setNameSpace($siteNamespace);

            $this->autoload();
            $this->setTemplate(new TwigTemplate());
            Security::ruleStart();
        }
    }

    protected function applyDirectoryConfig(): void
    {
        $cfg = $this->config;

        $this->directory->set(Constant::DIR_CONFIG, $cfg->common('dirs.config'));
        $this->directory->set(Constant::DIR_CONTROLLER, $cfg->common('dirs.controller'));
        $this->directory->set(Constant::DIR_MODEL, $cfg->common('dirs.model'));
        $this->directory->set(Constant::DIR_VIEW, $cfg->common('dirs.view'));
        $this->directory->set(Constant::DIR_TEMP, $cfg->common('dirs.temp'));
        $this->directory->set(Constant::DIR_LOG, $cfg->common('dirs.log'));
        $this->directory->set(Constant::DIR_HOME, $cfg->common('dirs.home'));
        $this->directory->set(Constant::DIR_COMPILE, $cfg->common('dirs.compile'));
    }

    protected function getNamespace()
    {
        return $this->getConfig()->common(Constant::KEY_NAMESPACE);
    }

    /**
     * @method static Application getDirectory
     * @return Directory
     */
    protected function getDirectory(): Directory
    {
        return $this->directory;
    }

    protected function getConfig(): Config
    {
        return $this->config;
    }

    protected function setTemplate(TemplateInterface $template)
    {
        $this->template = $template;
        $templateDir = $this->getDirectory()->get(Constant::DIR_VIEW);
        $cacheDir = $this->getDirectory()->get(Constant::DIR_COMPILE);
        $this->template->setTemplate($templateDir);
        $this->template->setCached($cacheDir);
    }

    protected function getTemplate(): TemplateInterface
    {
        return $this->template;
    }

    protected function displayErrors()
    {
        $displayErrors = $this->getConfig()->common('displayErrors');
        if (!empty($displayErrors) && ($displayErrors == 1 || strtolower($displayErrors) == 'on')) {
            ini_set('display_errors', 'On');
        } else {
            ini_set('display_errors', 'Off');
        }
    }

    /**
     * autoload 등록
     * @throws ConfigException
     * @throws DirectoryException
     * @throws Exception
     */
    protected function autoload()
    {
        $siteNamespace = $this->getConfig()->common(Constant::KEY_NAMESPACE);

        $namespaces = [
            $siteNamespace. "\\Controller\\" => $this->getDir(Constant::DIR_CONTROLLER),
            $siteNamespace . "\\Model\\" => $this->getDir(Constant::DIR_MODEL)
            ];

        spl_autoload_register(function (string $class) use ($namespaces) {
            foreach($namespaces as $prefix => $baseDirectory){
                // does the class use the namespace prefix?
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    // no, move to the next registered autoloader
                    continue;
                }
                $relativeClass = substr($class, $len);
                $path = str_replace('\\', '/', $relativeClass) . '.php';
                // if the file exists, require it
                if (file_exists($baseDirectory . $path)) {
                    require $baseDirectory . $path;
                }
            }
        });
    }

    /**
     * set application directory
     * @param string $rootDir
     */
    protected function setRoot(string $rootDir)
    {
        $this->getDirectory()->setRoot($rootDir);
    }

    /**
     * @param string $dir
     * @return string
     * @throws DirectoryException
     */
    protected function getDir(string $dir): string
    {
        return $this->getDirectory()->get($dir);
    }

    protected function start()
    {
        $uri = Uri::get();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        return Router::dispatch($uri, $method);
    }

    /**
     * @param $rootDir
     * @param string $namespace
     * @deprecated
     */
    protected function install($rootDir, string $namespace = Constant::NS_DEFAULT)
    {
        $Installer = new Installer();
        $Installer->setup($rootDir, $namespace);

        $this->boot($rootDir);

        $Installer->install();
    }
}