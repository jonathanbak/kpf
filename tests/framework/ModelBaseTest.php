<?php declare(strict_types=1);

namespace KpfTest\Framework;

use Kpf\Model;
use PHPUnit\Framework\TestCase;

final class ModelBaseTest extends TestCase
{
    protected static $MySQL = null;

    public function testConnect(): Model
    {
        list($host, $user, $password, $dbName, $dbPort) = array($GLOBALS['DB_HOST'],$GLOBALS['DB_USER'],$GLOBALS['DB_PASSWD'],$GLOBALS['DB_NAME'], isset($GLOBALS['DB_PORT'])? $GLOBALS['DB_PORT'] : '3306');
        $MySQL = new Model(['host'=>$host, 'user'=>$user, 'password'=>$password, 'database'=>$dbName, 'port'=>$dbPort]);

        // Assert
        $this->assertNotEmpty($MySQL);

        return $MySQL;
    }

    /**
     * @depends testConnect
     */
    public function testConstructor(Model $MySQL)
    {
//        $this->markTestSkipped( 'PHPUnit will skip this method' );
        $MySQL->query("DROP TABLE IF EXISTS tmp_table");

        $query = "CREATE TABLE `tmp_table` (
          `t_id` int(11) NOT NULL DEFAULT '0',
          `t_datetime` datetime DEFAULT NULL,
          PRIMARY KEY (`t_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8; ";

        $result = $MySQL->query($query);
        $this->assertEquals(true, $result);
    }

    /**
     * @depends testConnect
     */
    public function testQueryInsert(Model $MySQL)
    {
        $successCnt = 0;
        for($i=1; $i<=10; $i++){
            $query = "INSERT INTO `tmp_table` SET `t_id`=?, `t_datetime`= now();";
            $result = $MySQL->query($query, array($i));
            if($result) $successCnt++;
        }

        $this->assertEquals(10, $successCnt);
        return $MySQL;
    }

    /**
     * @depends testQueryInsert
     */
    public function testFetchWhile(Model $MySQL)
    {
        $query = "SELECT * FROM `tmp_table` WHERE t_id > ? LIMIT 2";
        $list = array();
        while($row = $MySQL->fetch($query, array(4))){
            var_dump($row);
            $list[] = $row;
        }

        $this->assertEquals(2, count($list));
        return $MySQL;
    }

    /**
     * 다중 반복문에서 fetch 를 사용하더라도 정상적으로 다음 쿼리를 가져옴
     * @depends testQueryInsert
     */
    public function testFetchWhile2(Model $MySQL)
    {
//        $this->markTestSkipped( 'PHPUnit will skip this method' );
        $query = "SELECT * FROM `tmp_table` WHERE t_id > ? LIMIT 2";
        $list = array();
        $MySQL->bind_param('i');
        while($row = $MySQL->fetch($query, array(5))){
            $list_1 = $row; $list_2 = array();
            $query2 = "SELECT * FROM `tmp_table` WHERE t_id = ?";
            $MySQL->bind_param('i');
            while($row = $MySQL->fetch($query2, array($row['t_id']))){
                $list_2[] = $row;
            }
            $list[] = array($list_1, $list_2);
        }

        $this->assertEquals(2, count($list));
        return $MySQL;
    }

    /**
     * @depends testQueryInsert
     */
    public function testFetchForeach(Model $MySQL)
    {
        $query = "SELECT * FROM `tmp_table` WHERE t_id > ? LIMIT 2";
        $list = array();
        $row = $MySQL->fetch($query, array(5));
        $list[] = $row;
        $row = $MySQL->fetch($query, array(5));
        $list[] = $row;

        $this->assertEquals(2, count($list));
        return $MySQL;
    }

    /**
     * Drop the test table.
     *
     * @depends testFetchForeach
     */
    public function testDropTable(Model $MySQL)
    {
        $result = $MySQL->query("DROP TABLE IF EXISTS tmp_table");
        $this->assertTrue($result);

        return $MySQL;
    }
}