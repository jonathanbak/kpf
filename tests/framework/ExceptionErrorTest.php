<?php

namespace KpfTest\Framework;

use Kpf\Exception\Exception;
use Kpf\Error;
use PHPUnit\Framework\TestCase;

final class ExceptionErrorTest extends TestCase
{
    public function testExceptionWithMessageString(): void
    {
        $ex = new Exception("Custom Error Message", 500);

        $this->assertEquals("Custom Error Message", $ex->getMessage());
        $this->assertEquals(500, $ex->getCode());
    }

    public function testExceptionWithErrorObject(): void
    {
        $error = new Error(Error::NOT_FOUND_CONFIG);
        $ex = new Exception($error);

        $this->assertEquals($error->getMessage(), $ex->getMessage());
        $this->assertEquals($error->getCode(), $ex->getCode());
    }

    public function testErrorCodeToMessage(): void
    {
        $error = new Error(Error::REQUIRE_ROOT_DIR);
        $this->assertEquals('Root Directory 를 설정해주세요', $error->getMessage());

        $error = new Error(Error::NOT_FOUND_CONFIG);
        $this->assertEquals('설정 파일을 찾을 수 없습니다', $error->getMessage());

        $error = new Error(Error::INVALID_COMMAND);
        $this->assertEquals('http://domain/path 형태로 실행할 경로를 입력하세요', $error->getMessage());

        $error = new Error(Error::REQUIRE_AUTOLOADER);
        $this->assertEquals('composer autoloader 를 입력해주세요', $error->getMessage());
    }

    public function testUnknownErrorCodeReturnsDefaultMessage(): void
    {
        $error = new Error(999);
        $this->assertEquals('알수없는 오류 입니다.', $error->getMessage());
    }
}
