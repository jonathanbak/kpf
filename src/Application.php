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
    protected $ns;

    /**
     * 설정 로딩
     * @param string $rootDir 프로젝트 Root Dir 절대경로
     * @throws Exception
     * @throws Exception\SecurityException
     */
    protected function init(string $rootDir)
    {
        $autoLoader = ClassLoader::getRegisteredLoaders();
        $this->autoloader = $autoLoader;
        $this->directory = new Directory();
        $this->config = new Config();

        $this->directory->setRoot($rootDir);

        if ($this->config->common('installed')) {
            $this->config->init();
            $this->autoload();
            $this->setTemplate(new TwigTemplate());
            Security::ruleStart();
        } else {
            $this->config->init(false);
        }
    }

    protected function setNamespace($namespace)
    {
        $this->ns = $namespace;
    }

    protected function getNamespace()
    {
        return $this->ns;
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
//        $this->autoloader->setPsr4($siteNamespace . "\\", array($this->getDir(Constant::DIR_CONTROLLER)));
//        $this->autoloader->setPsr4($siteNamespace . "\\Model\\", array($this->getDir(Constant::DIR_MODEL)));

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
        $currentUri = Uri::get();
        Router::execute($currentUri);
    }

    /**
     * @param $rootDir
     * @param string $namespace
     * @throws Exception
     */
    protected function install($rootDir, string $namespace = Constant::NS_DEFAULT)
    {
        $this->directory = new Directory();
        $this->config = new Config();
        $this->directory->setRoot($rootDir);

        if(empty($namespace)) $namespace = Constant::NS_DEFAULT;
        $installer = new Installer();
        $installer->install($namespace);
    }
}