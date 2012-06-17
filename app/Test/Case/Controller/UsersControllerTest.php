<?php

App::uses('UsersController','Controller');

class UsersControllerTest extends CakeTestCase{


    public function setUp(){
        parent::setUp();
        $this->ctr = new UsersController();
    }

    public function testIsTen(){
        
        $result = $this->ctr->isTen(10);
        $this->assertEquals('true',$result);
        $result = $this->ctr->isTen(11);
        $this->assertEquals('false',$result);
        
    }


}