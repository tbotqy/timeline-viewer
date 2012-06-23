<?php

App::uses('UsersController','Controller');

class UsersControllerTest extends CakeTestCase{


    public function setUp(){
        parent::setUp();
        $this->ctr = new UsersController();
    }

    public function testIsTen(){

        $num = 10;
        $this->assertTrue($this->ctr->isTen($num));
        $num = 11;
        $this->assertFalse($this->ctr->isTen($num));
        
    }
}