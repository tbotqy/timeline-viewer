<?php

App::uses('Friend','Model');

class FriendTest extends CakeTestCase{
    
    public $fixtures = array('app.friend','app.user');

    public function setUp(){
        parent::setUp();
        $this->Friend = ClassRegistry::init('Friend');
    }
    
    public function testGetFriendIds(){

        $user_id = 1;
        $result = $this->Friend->getFriendIds($user_id);
        $this->assertEquals(count($result),879);
    }


}