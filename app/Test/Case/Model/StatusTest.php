<?php

App::uses('Status','Model');

class StatusTest extends CakeTestCase{
    
    public $fixtures = array('app.status','app.user','app.entity','app.friend');
    
    public function setUp(){
        parent::setUp();
        $this->Status = ClassRegistry::init('Status');
    }

