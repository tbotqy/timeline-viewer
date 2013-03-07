<?php

/**
 * Model/User.php
 */

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

    ////////////////////////////////////////
    // functions to retrieve single value //
    ////////////////////////////////////////
    
    public function getIds(){
      
        /**
         * gets all the valid user ids
         * @return array
         */

        $this->unbindAllModels();

        $cachedData = Cache::read('User.getIds');
        $lastCreatedTime = $this->getLastCreatedTime();

        $fetchFleshData = true;

        if($cachedData){

            // compare the total number of ids
            if( count($cachedData) == $this->getTotalUserNum() ){

                // compare the 'created' value
                if( Cache::read('User.created_largest') == $lastCreatedTime ){
                    $fetchFleshData = false;
                }
            }
        }else{
            $fetchFleshData = true;
        }

        if($fetchFleshData){

            // fetch flesh data
            $ret = $this->find(
                               'list',
                               array(
                                     'conditions'=>array(
                                                         'User.deleted_flag'=>false
                                                         ),
                                     'fields'=>array(
                                                     'User.id'
                                                     ),
                                     'order'=>'User.id ASC',
                                     'recursive'=>-1
                                     )
                               );
        }else{
            $ret = $cachedData;
        }
        
        // update cache 
        Cache::write('User.getIds',$ret);
        Cache::write('User.created_largest',$lastCreatedTime);
        
        return $ret;

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
                                                     'User.id'=>$user_id,
                                                     'User.deleted_flag'=>false
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

        $tokens = $this->find(
                              'first',
                              array(
                                    'conditions'=>array(
                                                        'User.id'=>$user_id,
                                                        'User.deleted_flag'=>false
                                                        ),
                                    'fields'=>array(
                                                    'User.token','User.token_secret'
                                                    )
                                    )                                                        
                              );
        
        return $tokens ? $tokens : false;
    }

    public function getIdByTwitterId($twitter_id){
        
        /**
         * acquires id with given $twitter_id
         * @param string or array $twitter_id
         * @return int/array if secceed otherwise false
         */

        $this->unbindAllModels();
        
        if(is_array($twitter_id)){
            
            $ids = $this->find(
                               'all',
                               array('conditions'=>array(
                                                         'User.twitter_id'=>$twitter_id,
                                                         'User.deleted_flag'=>false
                                                         ),
                                     'fields'=>array('User.id')
                                     )
                               );
            if($ids){
                // format an array to return
                $ret = array();
                foreach($ids as $id){
                    $ret[] = $id['User']['id'];
                }
                return $ret;
            }else{
                return false;
            }
            
        }else{

            $id = $this->find(
                              'first',
                              array('conditions'=>array(
                                                        'User.twitter_id'=>$twitter_id,
                                                        'User.deleted_flag'=>false
                                                        ),
                                    'fields'=>array('User.id')
                                    )
                              );
        
            return $id ? $id['User']['id'] : false;
        }
    }

    public function getLastCreatedTime(){

        $result = $this->find('first',
                              array(
                                    'conditions'=>array(
                                                        'User.deleted_flag'=>false
                                                        ),
                                    'fields'=>array('User.created'),
                                    'order'=>'User.created DESC',
                                    'recursive'=>-1
                                    )
                              );
        
        return $result ? $result['User']['created'] : false;

    }

    public function getLastUpdatedTime($user_id){

        /**
         * check when specified user's profile was updated
         * @param int $user_id
         * @return int (unixtime)
         */

        $this->unbindAllModels();
        
        $user = $this->findById($user_id);
        
        return $user['User']['updated'];
    }
    
    public function getTotalUserNum(){

        $ret = $this->find('count',
                           array(
                                 'conditions'=>array(
                                                     'User.deleted_flag'=>false
                                                     ),
                                 'recursive'=>-1
                                 )
                           );
        
        return $ret;
    }



    public function getGoneUsers(){

        /**
         * retrieve all the users that has already left from our service 
         */

        $this->unbindAllModels();

        $ret = $this->find(
                           'all',
                           array(
                                 'conditions'=>array(
                                                     'User.deleted_flag'=>true
                                                     )
                                 )
                           );
        return $ret;

    }

    public function getActiveUsers(){

        $this->unbindAllModels();

        $ret = $this->find(
                           'all',
                           array(
                                 'conditions'=>array(
                                                     'User.deleted_flag'=>false
                                                     ),
                                 'order'=>'User.created DESC'
                                 )
                           );

        return $ret;

    }

    ///////////////////////////////////////////////
    // functions to save/update/delete something //
    ///////////////////////////////////////////////
                            
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
                              'deleted_flag'=>false,
                              'created'=>time()
                              );
        
        return $this->save($data_to_save) ? true : false;

    }

    public function deleteAccount($user_id,$physically_delete = false){
        
        /**
         * delete all the data related to User
         */

        if($physically_delete){

            // delete all related data
            $this->Status->deleteAll(
                                     array(
                                           'Status.user_id'=>$user_id
                                           )
                                     );
            $this->Friend->deleteAll(
                                     array(
                                           'Friend.user_id'=>$user_id
                                           )
                                     );
            
            return $this->delete($user_id);
            
        }else{
            // just switch the delete flag
            $this->id = $user_id;
            return $this->saveField('deleted_flag',true);
        }
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
        
        return $this->save($data) ? true : false;
        
    }
   
    public function updateTime($user_id){

        return $this->updateAll(
                                array(
                                      'User.updated'=>time()
                                      ),
                                array(
                                      'User.id'=>$user_id
                                      )
                                );
    }

    ///////////////////////
    // boolean functions //
    ///////////////////////

    public function ownsStatus($user_id,$status_id){

        /**
         * checks if user owns the status with its id equals $status_id
         * @param int $user_id / $status_id
         * @return boolean
         */

        $result = $this->Status->find(
                                      'count',
                                      array(
                                            'conditions'=>array(
                                                                'Status.user_id'=>$user_id,
                                                                'Status.id'=>$status_id,
                                                                'Status.pre_saved'=>false
                                                                )
                                            )
                                      );

        return $result > 0 ? true : false;
    }

    public function hasFriendList($user_id){

        /**
         * checks if user has any following list
         * @param int $user_id
         * @return boolean
         */

        return $this->Friend->getFriendIds($user_id);

    }

    public function hasRegisteredFriend($user_id){
        
        /**
         * check if user with given $user_id has any friend registering this app
         * @param int $user_id
         * @return boolean
         */
        
        if($this->hasFriendList($user_id)){
            $twitter_ids = $this->Friend->getFriendIds($user_id);
            return $this->getIdByTwitterId($twitter_ids);
        }else{
            return false;
        }
        
    }
    
    public function userExists($user_id){

        /**
         * checks if the user with given $user_id exists
         */

        $result = $this->find(
                              'first',
                              array(
                                    'conditions'=>array(
                                                        'User.id'=>$user_id,
                                                        'User.deleted_flag'=>false
                                                        )
                                    )
                              );

        return $result;

    }
    
    public function isInitialized($user_id){
        
        /**
         * check if user with given $user_id has already imported statuses
         * @param int $user_id
         * @return boolean
         */

        $this->unbindAllModels();

        $result = $this->find(
                              'first',
                              array(
                                    'conditions'=>array(
                                                        'User.id'=>$user_id,
                                                        'User.deleted_flag'=>false
                                                        ),
                                    'fields'=>array(
                                                    'User.initialized_flag'
                                                    )
                                    )
                              );

        return $result['User']['initialized_flag'];
    }

    public function existByTwitterId($twitter_id){// I think the name not so  good

        /**
         * checks if user with given twitter id exists on User model
         * @param string $twitter_id
         * @return boolean
         */
        
        return $this->getIdByTwitterId($twitter_id) ? true : false;

    }

    ////////////
    // others //
    ////////////

    public function unbindAllModels(){
        
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