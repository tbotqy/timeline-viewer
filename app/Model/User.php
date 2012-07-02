<?php

class User extends AppModel{

    public $name = 'User';

    public $hasMany = array(
                            'Friend'=>array(
                                            'className'=>'Friend',
                                            'foreignKey'=>'user_id',
                                            'dependent'=>true
                                            ),
                            
                            'Status'=>array(
                                            'className'=>'Status',
                                            'foreignKey'=>'user_id',
                                            'dependent'=>true
                                            )
                            );
                            
    public function register($tokens,$verify_credentials){
 
        /**
         * register new user
         * @param array $tokens
         * @param array $verify_credentials
         * @return true if success otherwise false
         */
        

        // user's data to save
        $data_to_save = array(
                              'twitter_id'=>$verify_credentials['id_str'],
                              'name'=>$verify_credentials['name'],
                              'screen_name'=>$verify_credentials['screen_name'],
                              'profile_image_url_https'=>$verify_credentials['profile_image_url_https'],
                              'time_zone'=>$verify_credentials['time_zone'],
                              'utc_offset'=>$verify_credentials['utc_offset'],
                              'created_at'=>strtotime($verify_credentials['created_at']),
                              'lang'=>$verify_credentials['lang'],
                              'token'=>$tokens['token'],
                              'token_secret'=>$tokens['token_secret'],
                              'token_updated'=>0,
                              'initialized_flag'=>0,
                              'created'=>time()
                              );
        
        return $this->save($data_to_save) ? true : false;

    }
    
    public function updateTokens($user_id,$tokens){

        /**
         * update user's tokens for OAuth
         * @param int $user_id
         * @param array $tokens which contains both token/token_secret
         * @return true if seccess otherwise false
         */

        $data = array(
                      'id' =>  $user_id,
                      'token' => $tokens['token'],
                      'token_secret' => $tokens['token_secret'],
                      'token_updated' => time(),
                      'updated' => time()
                      );
        
        return $this->User->save($data) ? true : false;
    }

    public function deleteAccount($user_id){
        /**
         * delete all the data related to User
         */

        // delete all related data
        return $this->delete($user_id,true);
    }


    public function getIdByTwitterId($twitter_id){
        /**
         * acquires id with given $twitter_id
         * @param string $twitter_id
         * @return int if secceed otherwise false
         */

        $this->unbindAllModels();
        
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

    public function getTwitterId($user_id){
        /**
         * acquires twitter id with given $user_id
         * @param int $user_id
         * @return string user's twitter id
         */

        $this->unbindAllModels();

        $twitter_id = find(
                           'first',
                           array('conditions'=>array(
                                                     'User.id'=>$user_id
                                                     ),
                                 'fields'=>array('User.twitter_id')
                                 )
                           );

        return $twitter_id ? $twitter_id['User']['twitter_id'] : false;

    }

    public function getTokens($user_id){
        
        /**
         * @param int $user_id 
         * @return array of token pare if there is any token for $user_id
         * @return false if acquired none
         */
        
        $this->unbindAllModels();

        $tokens = $this->findById(
                                  $user_id,
                                  array('User.token','User.token_secret')
                                  );
        return $tokens ? $tokens : false;
    }
    
    public function isInitialized($user_id){
        
        /**
         * check if user with given $user_id has already imported statuses
         * @param int $user_id
         * @return boolean
         */

        $this->unbindAllModels();

        $result = $this->findById(
                                  $user_id,
                                  array('User.initialized_flag')
                                  );

        return $result['User']['initialized_flag'];
    }

    public function existByTwitterId($twitter_id){
        /**
         * checks if user with given twitter id exists on User model
         * @param string $twitter_id
         * @return boolean
         */
        
        return $this->getIdByTwitterId($twitter_id) ? true : false;

    }

    private function unbindAllModels(){
        
        foreach($this->hasMany as $model=>$inner){
            $hasMany[] = $model;
        }
        
        return $this->unbindModel(
                                  array(
                                        'hasMany'=>$hasMany
                                        )
                                  );
    }

}
