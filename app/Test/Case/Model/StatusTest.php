<?php

App::uses('Status','Model');

class StatusTest extends CakeTestCase{
    
    public $fixtures = array('app.status');

    public function setUp(){
        parent::setUp();
        $this->User = ClassRegistry::init('Status');
    }

    
    
}