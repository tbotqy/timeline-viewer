<?php

class Friend extends AppModel{
    
    public $name = 'Friend';
    
    public $belongsTo = array(
                              'User'=>array(
                                            'className'=>'User',
                                            'foreignKey'=>'user_id',
                                            'dependent'=>false
                                            )
                              );
    
    public function getLastUpdatedTime($user_id){

        /**
         * returns the record value in User.friends_updated
         */
        
        $this->User->unbindAllModels();

        $user = $this->User->findById($user_id);
        
        return $user['User']['friends_updated'];

    }

    public function getFriendNum($user_id){
    
        /**
         * returns the total number of friends belonging to specified user
         */

        return $this->find(
                           'count',
                           array(
                                 'conditions'=>array(
                                                     'Friend.user_id'=>$user_id
                                                     )
                                 )
                           );

    }

    public function getFriendIds($user_id){
        
        /**
         * retrieves twitter id that user is following 
         * @param int $user_id
         * @return nothing
         */
        
        $conditions = array('Friend.user_id'=>$user_id);
        $fields = array('Friend.following_twitter_id');
        
        $ret = $this->find(
                           'list',
                           array(
                                 'conditions'=>$conditions,
                                 'fields'=>$fields
                                 )
                           );
        
        return $this->checkNum($ret);

    }

    public function saveFriends($user_id,$id_list){

        /**
         * saves given $id_list
         * @param int $user_id
         * @param array $id_list
         * @return nothing
         */

        foreach($id_list as $id){
            $this->create();
            $data = array(
                          'user_id'=>$user_id,
                          'following_twitter_id'=>$id,
                          'created'=>time()
                          );
            $this->save($data);
        }
        
        $this->updateTime($user_id);
    }

    public function updateFriends($user_id,$id_list){
        
        /**
         * updates friends list with ids in given $id_list
         * @param int $user_id
         * @param array $id_list
         * @return nothing
         */

        // delete existing records related to this user
        $delete_conditions = array('Friends.user_id'=>$user_id);
        $this->deleteAll($delete_conditions);

        // insert new data
        $this->saveFriends($user_id,$id_list);

    }

    public function updateTime($user_id){

        /**
         * updates User.friends_updated
         */
        
        $this->User->id = $user_id;
        $this->User->saveField('friends_updated',time());
        
    }
}


