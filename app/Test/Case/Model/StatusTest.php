<?php

App::uses('Status','Model');

class StatusTest extends CakeTestCase{
    
    public $fixtures = array('app.status','app.user','app.entity','app.friend');
    
    public function setUp(){
        parent::setUp();
        $this->Status = ClassRegistry::init('Status');
    }
    /*
    public function testGetCreatedAtList(){
        $user_id = 1;
        $mode = "home_timeline";

        $result = $this->Status->getCreatedAtList($user_id,$mode);

        $this->assertTrue($result,$result);

    }
    */

    public function testHasOlderTimeline(){

        $user_id = 1;
        $timestamp = 1338576538;

        $result = $this->Status->hasOlderTimeline($user_id,$timestamp);

        $this->assertTrue($result);

    }

    /*
    public function testHasOlderStatus(){

        $user_id = 2;

        $timestamp = 1276075805;
        $this->assertFalse($this->Status->hasOlderStatus($user_id,$timestamp),$timestamp);

        $timestamp = 0;
        $this->assertFalse($this->Status->hasOlderStatus($user_id,$timestamp),$timestamp);
        
        $timestamp = 1335628794;
        $this->assertFalse($this->Status->hasOlderStatus($user_id,$timestamp),$timestamp);

        $timestamp = 1335628795;
        $this->assertTrue($this->Status->hasOlderStatus($user_id,$timestamp),$timestamp);
                                 
    }
    */
}