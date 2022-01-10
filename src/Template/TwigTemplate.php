<?php

namespace Kpf\Template;

use Kpf\TemplateInterface;

class TwigTemplate implements TemplateInterface
{
    protected $templatePath;
    protected $cachePath;
    protected $params = array();
    protected $loader;
    protected $twig;

    /**
     * 템플릿 위치 지정
     * @param $path
     */
    public function setTemplate($path)
    {
        $this->templatePath = $path;
        $this->loader = new \Twig\Loader\FilesystemLoader($path);
    }

    /**
     * 템플릿 캐시 저장 위치 지정
     * @param $path
     */
    public function setCached($path)
    {
        $this->cachePath = $path;
        $this->twig = new \Twig\Environment($this->loader, [
            'cache' => $path,
            'auto_reload' => true
        ]);
    }

    /**
     * @param $extensionClassName
     */
    public function addExtension($extensionClassName)
    {
        $this->twig->addExtension(new $extensionClassName);
    }

    /**
     * 데이터 추가
     * @param array $params
     */
    public function assign($params = array())
    {
        foreach ($params as $key => &$value) {
            $this->params[$key] = $value;
        }
    }

    /**
     * 템플릿 렌더링, 출력
     * @param $templateFile 템플릿 파일명
     * @param array $params 렌더링시 사용할 데이터
     */
    public function display($templateFile, $params = array())
    {
        $datas = $this->params;

        foreach ($params as $key => $value) {
            $datas[$key] = $value;
        }
        $template = $this->twig->load($templateFile);
        echo $template->render($datas);
    }
}