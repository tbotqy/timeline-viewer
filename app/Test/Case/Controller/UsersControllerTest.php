<?php

App::uses('UsersController','Controller');

class UsersControllerTest extends ControllerTestCase{
   
    public $fixtures = array('app.user','app.friend','app.status','app.entity');

    public function setUp(){
        parent::setUp();
        //$this->ctr = new UsersController();
    }

    public function testTest(){

        $ret = $this->testAction('/users/test');
        debug($ret);
        
    }
}