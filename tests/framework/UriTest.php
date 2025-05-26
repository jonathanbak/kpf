<?php

namespace KpfTest\Framework;

use Kpf\Helper\Uri;
use PHPUnit\Framework\TestCase;

final class UriTest extends TestCase
{
    public function testReverseDomain(): void
    {
        $this->assertEquals('com.example.www', Uri::reverseDomain('www.example.com'));
        $this->assertEquals('kr.co.naver', Uri::reverseDomain('naver.co.kr'));
        $this->assertEquals('org', Uri::reverseDomain('org'));
    }

    public function testGetUriWithoutQuery(): void
    {
        $_SERVER['REQUEST_URI'] = '/controller/action';

        $uriParts = Uri::get();

        $this->assertEquals(['controller', 'action'], $uriParts);
    }

    public function testGetUriWithLeadingSlash(): void
    {
        $_SERVER['REQUEST_URI'] = '//controller/action';

        $uriParts = Uri::get();

        $this->assertEquals(['controller', 'action'], $uriParts);
    }

    public function testGetUriWithQuery(): void
    {
        $_SERVER['REQUEST_URI'] = '/test/detail?id=5&name=jon';

        $uriParts = Uri::get();

        $this->assertEquals(['test', 'detail'], $uriParts);
        // $_GET 파싱 확인 제거됨 (보안 상 이유로 수정된 본문 기준)
    }

    public function tearDown(): void
    {
        unset($_SERVER['REQUEST_URI']);
    }
}
