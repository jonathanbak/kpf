<?php

namespace Kpf\Helper;


use Kpf\Exception\Exception;

class File
{

    /**
     *
     * @param $fileName
     * @param array $datas
     * @throws Exception
     */
    public static function put_json_pretty($fileName, array $datas = []){
        $filePath = dirname($fileName);
        if(!is_dir($filePath)){
            throw new Exception('The file path not exist.');
        }
        file_put_contents($fileName, json_encode($datas, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX);
    }

    /**
     * @param $file
     * @return false|string
     */
    public static function load($file)
    {
        return file_get_contents($file);
    }

    public static function write($file, $content)
    {
        $filePath = dirname($file);
        if(!is_dir($filePath)){
            mkdir($filePath, 0755, true);
        }
        file_put_contents($file, $content, LOCK_EX);
    }
}