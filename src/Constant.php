<?php

namespace Kpf;


class Constant
{
    const DOT = '.';
    const NONE = '';

    const CONFIG_EXTENSION = 'json';
    const COMMON_CONFIG_FILE = 'configure.json';
    const TEMPLATE_EXTENSION = 'twig';
    const COMMON_DB = 'common.db';

    const DIR_CONFIG = 'config';
    const DIR_CONTROLLER = 'controllers';
    const DIR_MODEL = 'models';
    const DIR_VIEW = 'views';
    const DIR_TEMP = '_tmp';
    const DIR_LOG = 'logs';
    const DIR_HOME = 'html';
    const DIR_COMPILE = 'compile';

    const KEY_NAMESPACE = 'namespace';
    const KEY_DB_SET = 'dbSet';
    const KEY_ROUTE = 'route';
    const KEY_SECURE = 'secure';
    const KEY_ALLOW_IP = 'allowIps';

    const MYSQL_PORT = '3306';
    const LOG_DEFAULT = "system";
    const NS_DEFAULT = "example";
}