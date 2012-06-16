<?php

class User extends AppModel{

    public $name = 'User';

    public function getIdByTwitterId($twitter_id){
        /**
         * acquires id with given $twitter_id
         * returns int if secceed
         * returns false if fail
         */
        
        $id = $this->find(
                          'first',
                          array('conditions'=>array(
                                                    'User.twitter_id'=>$twitter_id
                                                    ),
                                'fields'=>array('User.id')
                                )
                          );
        
        return $id ? $id['User']['id'] : false;
    }

    public function getTokens($user_id){
        
        /**
         * returns token pare as an array
         * returns false if acquired none
         */

        $tokens = $this->findById(
                                  $user_id,
                                  array('User.token','User.token_secret')
                                  );
        return $tokens ? $tokens : false;
    }
    
    public function isInitialized($user_id){
        
        /**
         * check if user with given $user_id has already imported statuses
         * return boolean
         */

        $result = $this->findById(
                                  $user_id,
                                  array('User.initialized_flag')
                                  );

        return $result['User']['initialized_flag'];
    }


    public function existByTwitterId($twitter_id){
        /**
         * checks if user with given twitter id exists on User model
         * returns boolean
         */
        
        return $this->getIdByTwitterId($twitter_id) ? true : false;

    }

}