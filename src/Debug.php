<?php

namespace Kpf;


class Debug extends Singleton
{
    private $logFileName = "{site}_debug_{date}.log";

    protected function __construct()
    {
        $logDir = Application::getDirectory()->get(Constant::DIR_LOG);
        $httpHost = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : Constant::LOG_DEFAULT;
        $this->logFileName = $logDir . str_replace('{site}', $httpHost, str_replace('{date}', date("Ymd"), $this->logFileName));

        if (is_dir($logDir) == false) {
            mkdir($logDir, 0777);
        }
        if(!file_exists($this->logFileName)){
            touch($this->logFileName);
            chmod($this->logFileName, 0777);
        }
    }

    /**
     * @param $messages
     * @param string $logGroup
     * @throws Exception\ConfigException
     * @throws Exception\DirectoryException
     * @throws Exception\Exception
     */
    protected function write($messages, string $logGroup = 'common')
    {
        $baseDir = Application::getDirectory()->root();
        $backtrace = debug_backtrace();
        $callerFileName = "";
        foreach ($backtrace as $k => $firstBacktrace) {
            if (isset($firstBacktrace['file']) && strripos($firstBacktrace['file'], $baseDir) !== false) {
                $callerFileName = isset($firstBacktrace['file']) ? str_replace($baseDir, '', $firstBacktrace['file']) : '';
                if (isset($firstBacktrace['line'])) $callerFileName .= " (" . $firstBacktrace['line'] . ")";
            }else if(isset($firstBacktrace['file']) && strripos($firstBacktrace['file'], "kpf/tests") !== false) {
                $callerFileName = isset($firstBacktrace['file']) ? basename($firstBacktrace['file']) : '';
                if (isset($firstBacktrace['line'])) $callerFileName .= " (" . $firstBacktrace['line'] . ")";
            }
        }
//        $messages = $self->convertCharset($messages, 'utf8');
        if (is_array($messages)) {
            $messages = json_encode($messages);
//            $messages = $this->unicode_decode($messages);
        }

        $messages = "[" . $callerFileName . "] - " . $messages;

        $debugMode = Application::getConfig()->common('debugMode');
        if ($debugMode) $this->_log($messages, $logGroup);

        $displayErrors = Application::getConfig()->common('displayErrors');
        if ($displayErrors == 'on' || $displayErrors == '1') {
            if (php_sapi_name() == "cli") echo "[" . date("Y-m-d H:i:s") . "](" . $logGroup . ")" . $messages . "\n";
            else echo "<pre style='background-color:black;color:#eee;'>[" . date("Y-m-d H:i:s") . "](" . $logGroup . ")" . $messages . "<br></pre>";
        }
    }

    protected function _log($messages, $logGroup = 'common')
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR']?? '127.0.0.1';
        $messages = "[" . date("Y-m-d H:i:s") . "] (" . $logGroup . ") " . $remoteAddr . " - " . $messages . "\n";
        file_put_contents($this->logFileName, $messages, FILE_APPEND);
    }
}