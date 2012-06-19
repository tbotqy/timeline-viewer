<?php

App::uses('Status','Model');

class StatusTest extends CakeTestCase{
    
    public $fixtures = array('app.status','app.user','app.entity');
    
    public function setUp(){
        parent::setUp();
        $this->Status = ClassRegistry::init('Status');
    }

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
    
}