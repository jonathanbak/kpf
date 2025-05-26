<?php

namespace Kpf;

use Kpf\Exception\RouterException;
use Kpf\Helper\Uri;

class Router extends Singleton
{
    protected $routes = [];
    protected $defaultUri = "/";

    protected $namespace = "";

    protected function setNamespace(string $namespace): void
    {
        $this->namespace = trim($namespace, '\\');
    }

    protected function setDefaultUri(string $uri): void
    {
        $this->defaultUri = $uri;
    }

    /**
     * GET 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     */
    protected function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * POST 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     */
    protected function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * @param $methods
     * @param string $uri
     * @param $action
     * @return void
     */
    protected function addRoute($methods, string $uri, $action): void
    {
        $methods = is_array($methods) ? $methods : [$methods];
        $this->routes[] = [$methods, $uri, $action];
    }

    /**
     * GET,POST 모든 방식 라우팅 정보 입력
     * @param $uri
     * @param $action
     * @deprecated
     */
    protected function any($uri, $action)
    {
        $verbs = ['GET', 'POST'];
        $this->addRoute($verbs, $uri, $action);
    }

    /**
     * 라우팅 실행 - URI 와 클래스를 매칭하여 실행한다
     * @param $currentUri
     * @return false|mixed
     * @deprecated
     */
    protected function execute($currentUri)
    {
        $uri = $currentUri ?? Uri::get();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        return $this->dispatch($uri, $method);
    }

    private function resolve(array $currentUri, string $method): ?array
    {
        foreach ($this->routes as [$methods, $uri, $action]) {
            $pattern = '/^' . str_replace('/', '\/', ltrim($uri, '/')) . '$/i';
            if (preg_match($pattern, implode('/', $currentUri), $matches) && in_array(strtoupper($method), $methods)) {
                array_shift($matches);
                return [$action, $matches];
            }
        }
        return null;
    }

    /**
     * @throws RouterException
     */
    protected function dispatch(array $currentUri, string $method)
    {
        $result = $this->resolve($currentUri, $method);

        if ($result !== null) {
            [$action, $params] = $result;
            if (is_callable($action)) {
                return call_user_func_array($action, $params);
            }
            $currentUri = Uri::get($action);
        }

        if (count($currentUri) === 1 && empty($currentUri[0])) {
            $currentUri = Uri::get($this->defaultUri);
        }

        return $this->callClassByUri($currentUri);
    }

    /**
     * @param array $currentUri
     * @return mixed
     * @throws RouterException
     */
    private function callClassByUri(array $currentUri)
    {
        $namespace = $this->namespace;
        $classParts = $this->ucFirstArray($currentUri);
        $class = $namespace . '\\Controller\\' . implode('\\', $classParts);

        if (class_exists($class)) {
            $instance = new $class();
            if (!method_exists($instance, 'main')) {
                throw new RouterException("Method 'main' not found in $class", 404);
            }
            return $instance->main();
        }

        $method = array_pop($currentUri);
        $classParts = $this->ucFirstArray($currentUri);
        $class = $namespace . '\\Controller\\' . implode('\\', $classParts);

        if (!class_exists($class)) {
            throw new RouterException("Class not found: $class", 404);
        }

        $instance = new $class();
        if (!method_exists($instance, $method)) {
            throw new RouterException("Method '$method' not found in $class", 404);
        }

        return call_user_func([$instance, $method]);
    }

    private function ucFirstArray(array $segments): array
    {
        return array_map('ucfirst', $segments);
    }
}