<?php

App::uses('User','Model');

class UserTest extends CakeTestCase{
    
    public $fixtures = array('app.user');

    public function setUp(){
        parent::setUp();
        $this->User = ClassRegistry::init('User');
    }

    public function testGetIdByTwitterId(){
        
        $result = $this->User->getIdByTwitterId(565212883);
        $this->assertEquals($result,8);

        $result = $this->User->getIdByTwitterId(221);
        $this->assertEquals($result,false);
        
    }

    public function testGetTokens(){

        $id = 1;
        $result = $this->User->getTokens($id);

        $this->assertEquals($result,false);
        
        
        $id = 8;
        $result = $this->User->getTokens($id);
        $expected = $this->User->findById($id,array('token','token_secret'));

        $this->assertEquals($result,$expected);
    }
    
    public function testIsInitialized(){
        
        $id = 8;
        $result = $this->User->isInitialized($id);
        $this->assertEquals($result,false);

        $id = 100;
        $result = $this->User->isInitialized($id);
        $this->assertEquals($result,false);
    }
    
    public function testExistByTwitterId(){

        $users = $this->User->find('all');
        
        foreach($users as $single_user){
            $twitter_id = $single_user['User']['twitter_id'];
            
            $result = $this->User->existByTwitterId($twitter_id);
            $this->assertEquals($result,true);
        }
        
        $twitter_id = 1;
        $result = $this->User->existByTwitterId($twitter_id);
        $this->assertEquals($result,false);

    }
    
}