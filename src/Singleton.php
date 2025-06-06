<?php
namespace Kpf;

class Singleton
{
    protected static $instances = array();

    public static function getInstance() {
        $class = get_called_class();
        if ( empty( self::$instances[$class] ) ) {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }

    public static function __callStatic($method, $args)
    {
        $instance = self::getInstance();
        return call_user_func_array(array($instance, $method), $args);
    }

    public static function resetInstance(): void
    {
        $class = get_called_class();
        unset(self::$instances[$class]);
    }

}