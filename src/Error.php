<?php

namespace Kpf;


class Error
{
    const REQUIRE_ROOT_DIR = 1001;
    const INVALID_COMMAND = 1400;
    const NOT_FOUND_CONFIG = 1404;
    const REQUIRE_AUTOLOADER = 1500;


    protected $errCode;
    protected $errMsg;

    public function __construct($errCode)
    {
        $this->setErrorCode($errCode);
        $this->setErrorMessage($errCode);
    }

    public function setErrorCode($errCode)
    {
        $this->errCode = $errCode;
    }

    public function setErrorMessage($errCode)
    {
        $this->errMsg = $this->getErrorCodeToMessage($errCode);
    }

    public function getMessage()
    {
        return $this->errMsg;
    }

    public function getCode()
    {
        return $this->errCode;
    }

    /**
     *
     * @param $errCode
     * @return string   error message
     */
    public function getErrorCodeToMessage($errCode): string
    {
        //TODO 오류 메시지 설정파일.. 언어팩 사용할수 있게 추후 변경
        $msg = '알수없는 오류 입니다.';
        switch ($errCode) {
            case self::REQUIRE_ROOT_DIR:
                $msg = 'Root Directory 를 설정해주세요';
                break;
            case self::NOT_FOUND_CONFIG:
                $msg = '설정 파일을 찾을 수 없습니다';
                break;
            case self::INVALID_COMMAND:
                $msg = "http://domain/path 형태로 실행할 경로를 입력하세요";
                break;
            case self::REQUIRE_AUTOLOADER:
                $msg = "composer autoloader 를 입력해주세요";
                break;
        }

        return $msg;
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}