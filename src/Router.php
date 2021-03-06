<?php

namespace Kpf;

use Kpf\Exception\RouterException;
use Kpf\Helper\Uri;

class Router extends Singleton
{
    protected $routes = [];
    private $config = null;
    private $defaultUri = "";

    public function __construct()
    {
        $routeConfigName = Application::getConfig()->common(Constant::KEY_ROUTE);
        $this->config = Application::getConfig()->load($routeConfigName);
        $this->defaultUri = $this->config['default'];
        if(!empty($this->config['routing'])){
            foreach($this->config['routing'] as $uri => $routing){
                $this->any($uri, $routing);
                if($uri == $this->defaultUri) {
                    $this->defaultUri = $routing;
                }
            }
        }
    }

    /**
     * GET 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     */
    protected function get($uri, $action)
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * POST 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     */
    protected function post($uri, $action)
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * 라우팅 정보 추가
     * @param $method
     * @param $uri
     * @param $action
     */
    protected function addRoute($method, $uri, $action)
    {
        $this->routes[] = array($method, $uri, $action);
    }

    /**
     * GET,POST 모든 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     */
    protected function any($uri, $action)
    {
        $verbs = array('GET', 'POST');
        return $this->addRoute($verbs, $uri, $action);
    }

    /**
     * 라우팅 실행 - URI 와 클래스를 매칭하여 실행한다
     * @param $currentUri
     * @return false|mixed
     * @throws Exception\ConfigException
     * @throws Exception\DirectoryException
     * @throws Exception\Exception
     * @throws RouterException
     */
    protected function execute($currentUri)
    {
        $customParams = array();

        if (count($this->routes) === 0) {

        } else {
            $arrUri = $currentUri? $currentUri : Uri::get();
            foreach ($this->routes as $key => $route) {
                list($method, $uri, $action) = $route;
                if (!is_array($method)) $method = array($method);
                if (preg_match('/^\//i', $uri, $tmpMatch)) {
                    $uri = substr($uri, 1);
                }
                if (preg_match('/^' . str_replace('/', '\/', $uri) . '$/i', implode('/', $arrUri), $tmpMatch) && in_array(strtoupper($_SERVER['REQUEST_METHOD']), $method)) {
                    if (is_object($action)) {
                        $currentUri = $action;
                        array_shift($tmpMatch);
                        $customParams = $tmpMatch;
                    } else {
                        $currentUri = Uri::get($action);
                    }
                }else {

                }
            }
        }
        if (is_object($currentUri)) {
            return call_user_func_array($currentUri, $customParams);
        } else {
            if (count($currentUri) == 1 && empty($currentUri[0])) {
                $currentUri = Uri::get($this->defaultUri);
            }
            return $this->callClassByUri($currentUri);
        }
    }

    /**
     * URI 와 Class 매칭
     * @param $currentUri
     * @return false|mixed
     * @throws Exception\ConfigException
     * @throws Exception\DirectoryException
     * @throws Exception\Exception
     * @throws RouterException
     */
    protected function callClassByUri($currentUri)
    {
        $siteNamespace = Application::getConfig()->common(Constant::KEY_NAMESPACE);
        $loadClassName = $siteNamespace . '\\Controller\\' . implode('\\', $this->ucFirstArray($currentUri));
        if (class_exists($loadClassName)) {
            $callClass = new $loadClassName();
            $methodList = get_class_methods($callClass);
            $START_METHOD = 'main';
            if (!in_array($START_METHOD, $methodList)) {
                throw new RouterException("Not Found File - " . $loadClassName, 404);
            }
            return call_user_func_array(array($callClass, $START_METHOD), array());
        } else {
            $method = array_pop($currentUri);
            $loadClassName = $siteNamespace . '\\Controller\\' . implode('\\', $this->ucFirstArray($currentUri));
            if (!class_exists($loadClassName)) {
                throw new RouterException("Not Found Class File - " . $loadClassName, 404);
            }
            $callClass = new $loadClassName();
            $methodList = get_class_methods($callClass);
            if (!in_array($method, $methodList)) throw new RouterException("Not Found File - " . $loadClassName, 404);
            return call_user_func_array(array($callClass, $method), array());
        }
    }

    /**
     * 배열 값을 각각 ucfirst 하여 돌려줌
     * @param $values
     * @return array
     */
    protected function ucFirstArray($values): array
    {
        $response = array();
        foreach ($values as $val) {
            $response[] = ucfirst($val);
        }
        return $response;
    }
}