<?php declare(strict_types=1);

namespace KpfTest\Module;

use Kpf\Exception\OutputException;
use Kpf\Model;
use PHPUnit\Framework\TestCase;
use Example\Model\Account\Member;

require_once __DIR__ . '/bootstrap.php';

final class ModelSampleTest extends TestCase
{
    public function testFindReturnsExpectedData(): void
    {
        $member = new Member();
        $data = $member->find();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('memo', $data);

        $this->assertEquals('Jonathan Bak 2', $data['name']);
        $this->assertEquals('jonathanbak@gmail.com', $data['id']);
    }

    /**
     * 테스트: 설정 기반 select 연결 확인
     * 설정값은 common.db 로 가정
     */
    public function testSelectFromConfig(): void
    {
        $this->expectException(\MySQLiLib\Exception::class);
        $MySQL = new Model();
        $MySQL->select('common.db');
        $this->assertNotEmpty($MySQL);
    }
}