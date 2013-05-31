<?php

App::uses('PublicDate','Model');

class PublicDateTest extends CakeTestCase{

    public $fixtures = array('app.publicDate');

    public function setUp(){

        parent::setUp();

        $this->PublicDate = ClassRegistry::init('PublicDate');
    }

    public function testConvertTimeToDate(){

        // 1369661473 = 2013/5/27
        $result = $this->PublicDate->convertTimeToDate(1369661473);
        $this->assertEquals($result,'2013/5/27');

        // 547651873 = 1987/5/10
        $result = $this->PublicDate->convertTimeToDate(547651873);
        $this->assertEquals($result,'1987/5/10');
    }

    public function testDateExists(){

        // 1368144000 = 2013/5/10 0:0:0
        $result = $this->PublicDate->dateExists(1368144000);
        $this->assertTrue($result);
      
        // 1356912000 = 2012/12/31
        $result = $this->PublicDate->dateExists(1356912000);
        $this->assertTrue($result);

        // 1318204800 = 2011/10/10 0:0:0
        $result = $this->PublicDate->dateExists(1318204800);
        $this->assertTrue($result);
        
        // 1368111600 = 2000/5/10
        $result = $this->PublicDate->dateExists(1368111600);
        $this->assertFalse($result);
      
    }
}