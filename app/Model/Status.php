<?php

/**
 * Model/Status.php
 */

class Status extends AppModel{
    
    public $name = 'Status';
        
    public $hasMany = array(
                            'Entity' => array(
                                              'dependent'=>true
                                              )
                            );
    
    public $belongsTo = array(
                              'User'=>array(
                                            'dependent'=>false
                                            )
                              );

    ///////////////////////////////////////////
    // functions to retrieve latest statuses //
    ///////////////////////////////////////////
    
    public function getLatestStatus($user_id,$limit = 10){

        /**
         * acquire user's latest statsues
         * returns array if there is any
         * returns false if there is nothing to return
         */

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array(
                                                          'Status.user_id'=>$user_id,
                                                          'Status.pre_saved' => false
                                                          ),
                                      'limit'=>$limit,
                                      'order'=>'Status.created_at DESC'
                                      )
                                );
        // return result
        return $this->checkNum($statuses);
        
    }

    public function getLatestTimeline($user_id,$limit = 10){
        
        /**
         * retrieve statuses tweeted by those whom user is following on twitter
         * returns $limit statuses ordering in created_at DESC 
         * @param array list of twitter id user is following
         * @return array just like mentioning above 
         */

        // get user's friend ids
        $twitter_ids = $this->User->Friend->getFriendIds($user_id);
        // find user_ids IN $twitter_ids
        $user_ids = $this->User->getIdByTwitterId($twitter_ids);

        $ret = $this->find(
                           'all',
                           array(
                                 'conditions'=>array(
                                                     'Status.user_id'=>$user_ids,
                                                     'Status.pre_saved' => false,
                                                     ),
                                 'order'=>'Status.created_at DESC',
                                 'limit'=>$limit
                                 )
                           );

        return $this->checkNum($ret);

    }


    public function getLatestPublicTimeline($limit = 10){
      
        /**
         * retrieve everybody's statuses in database
         * @return array if exist, else false
         */

        $ids = $this->User->getIds();

        $ret = $this->find(
                           'all',
                           array(
                                 'conditions'=>array(
                                                     'Status.user_id'=>$ids,
                                                     'Status.pre_saved' => false
                                                     ),
                                 'order'=>'Status.created_at DESC',
                                 'limit'=>$limit
                                 )
                           );

        return $this->checkNum($ret);

    }

    ////////////////////////////////////////////
    // functions to retrieve statuses in term //
    ////////////////////////////////////////////
    
    public function getStatusInTerm($user_id,$begin,$end,$order = 'DESC',$limit = 10){
        
        /**
         * acquire specified user's statuses in specified term(from $bein to $end)
         * @param int $user_id, $begin, $end
         * @return array if there is any,false if there is no status in specified term
         */

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array(
                                                          'Status.user_id'=>$user_id,
                                                          'Status.created_at >=' => $begin,
                                                          'Status.created_at <=' => $end,
                                                          'Status.pre_saved' => false
                                                          ),
                                      'limit'=>$limit,
                                      'order'=>'Status.created_at '.$order
                                      )
                                );
     
        return $this->checkNum($statuses);
        
    }

    public function getTimelineInTerm($user_id,$begin,$end,$order = 'DESC',$limit = 10){
        
        /**
         * acquire user's friends' statuses in specified term
         * returns array if there is any
         * returns false if there is no status in specified term
         */
        
        // get user's friend ids
        $twitter_ids = $this->User->Friend->getFriendIds($user_id);
        // find user_ids IN $twitter_ids
        $user_ids = $this->User->getIdByTwitterId($twitter_ids);

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array(
                                                          'Status.user_id'=>$user_ids,
                                                          'Status.created_at >=' => $begin,
                                                          'Status.created_at <=' => $end,
                                                          'Status.pre_saved' => false,
                                                          ),
                                      'limit'=>$limit,
                                      'order'=>'Status.created_at '.$order
                                      )
                                );

        // return result
        return $this->checkNum($statuses);
        
    }

    public function getPublicTimelineInTerm($begin,$end,$order = 'DESC',$limit = 10){
        
        /**
         * acquire all the user's statuses in specified term
         * returns array if there is any
         * returns false if there is no status in specified term
         */
        
        // get user's friend ids
        $ids = $this->User->getIds();

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array(
                                                          'Status.user_id'=>$ids,
                                                          'Status.created_at >=' => $begin,
                                                          'Status.created_at <=' => $end,
                                                          'Status.pre_saved' => false
                                                          ),
                                      'limit'=>$limit,
                                      'order'=>'Status.created_at '.$order
                                      )
                                );

        // return result
        return $this->checkNum($statuses);
        
    }

    public function getDateList($user_id,$mode="sent_tweets"){

        /**
         * creates date list of user's statuses
         * @param int $user_id
         * @return array
         */
       
        // fetch created_at value of the statuses
        $status_date_list = $this->getCreatedAtList($user_id,$mode);
        
        // get utc_offset for user
        $user_data = $this->User->findById($user_id);
        $utc_offset = $user_data['User']['utc_offset'];

        // classify them by date       
        foreach($status_date_list as $key=>$created_at){
            $created_at += $utc_offset;

            $year = date('Y',$created_at);
            $month = date('n',$created_at);
            $day = date('j',$created_at);

            //$sum_by_year[$year] = isset($sum_by_year[$year]) ? $sum_by_year[$year]+1 : 1;
            //$sum_by_month[$year][$month] = isset($sum_by_month[$year][$month]) ? $sum_by_month[$year][$month]+1 : 1;
            $sum_by_day[$year][$month][$day] = isset($sum_by_day[$year][$month][$day]) ? $sum_by_day[$year][$month][$day]+1 : 1;
            
        }
        
        return $this->checkNum($sum_by_day);

    }
    
    public function getCreatedAtList($user_id,$mode,$order = 'DESC'){

        /**
         * creates the list of posted timestamp of user's tweets
         * @param int $user_id
         * @order string
         */
       
        switch($mode){
        case 'sent_tweets':
            $ret = $this->find(
                               'list',
                               array(
                                     'conditions'=>array(
                                                         'Status.user_id'=>$user_id,
                                                         'Status.pre_saved' => false
                                                         ),
                                     'fields'=>array(
                                                     'Status.created_at'
                                                     ),
                                     'order'=>'Status.created_at '.$order
                                     )
                               );
            break;
        case 'home_timeline':

            // get user's friend ids
            $twitter_ids = $this->User->Friend->getFriendIds($user_id);
            // find user_ids IN $twitter_ids
            $user_ids = $this->User->getIdByTwitterId($twitter_ids);
        
            $ret = $this->find
                (
                 'list',
                 array
                 (
                  'conditions'=>array(
                                      'Status.user_id'=>$user_ids,
                                      'Status.pre_saved'=>false
                                      ),
                  'fields'=>array(
                                  'Status.created_at'
                                  ),
                  'order'=>'Status.created_at '.$order
                  )
                 );
                       
            return $ret;

            break;
        case 'public_timeline':
            $ids = $this->User->getIds();
            
            $ret = $this->find
                (
                 'list',
                 array
                 (
                  'conditions'=>array(
                                      'Status.user_id'=>$ids,
                                      'Status.pre_saved' => false
                                      ),
                  'fields'=>array(
                                  'Status.created_at'
                                  ),
                  'order'=>'Status.created_at '.$order
                  )
                 );
                       
            return $ret;

            break;
        default:
            return false;
        }

        return $this->checkNum($ret);

    }

    //////////////////////////////////////////
    // functions to retrieve older statuses //
    //////////////////////////////////////////

    public function getOlderStatus($user_id,$threshold_timestamp,$limit = 10){
        
        /**
         * retrieve statuses whose created_at value is smaller than specified $threshold_timestamp
         * @param string $user_id
         * @param int $threshold_timestamp
         * @param int $limit
         * @return array if retrieved any status, otherwise false
         */
                
        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array(
                                                          'Status.user_id'=>$user_id,
                                                          'Status.created_at <'=>$threshold_timestamp,
                                                          'Status.pre_saved' => false
                                                          ),
                                      'limit'=>$limit,
                                      'order'=>'Status.created_at DESC'
                                      )
                                );
        
        return $this->checkNum($statuses);
    }

    public function getOlderTimeline($user_id,$timestamp,$limit = 10){

        // get user's friend ids
        $twitter_ids = $this->User->Friend->getFriendIds($user_id);
        // find user_ids IN $twitter_ids
        $user_ids = $this->User->getIdByTwitterId($twitter_ids);

        // retrieve statuses
        $conditions = array(
                            'Status.user_id' => $user_ids,
                            'Status.created_at <' => $timestamp,
                            'Status.pre_saved' => false,
                            );

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>$conditions,
                                      'order'=>'Status.created_at DESC',
                                      'limit'=>$limit
                                      )
                                );

        return $this->checkNum($statuses);
    }

    public function getOlderPublicTimeline($timestamp,$limit = 10){

        $ids = $this->User->getIds();

        // retrieve statuses
        $conditions = array(
                            'Status.user_id' => $ids,
                            'Status.created_at <' => $timestamp,
                            'Status.pre_saved' => false
                            );

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>$conditions,
                                      'order'=>'Status.created_at DESC',
                                      'limit'=>$limit
                                      )
                                );
        return $this->checkNum($statuses);
    }

    /////////////////////////////////
    // functions to save something //
    /////////////////////////////////
    
    public function saveStatus($user,$status){
        
        // convert date format 
        $created_at = strtotime($status['created_at']);
        
        // this value doesn't always exist in returned data.
        $possibly_sensitive = isset($status['possibly_sensitive']) ? $status['possibly_sensitive'] : false;
                
        // create an array to pass to model
        $status_to_save = array(
                                'user_id'=>$user['id'],
                                'twitter_id'=>$user['Twitter']['id'],
                                'status_id_str'=>$status['id_str'],
                                'in_reply_to_status_id_str'=>$status['in_reply_to_status_id_str'],
                                'in_reply_to_user_id_str'=>$status['in_reply_to_user_id_str'],
                                'in_reply_to_screen_name'=>$status['in_reply_to_screen_name'],
                                'place_full_name'=>$status['place']['full_name'],// optional value
                                'retweet_count'=>$status['retweet_count'],// int
                                'created_at'=>$created_at,
                                'source'=>$status['source'],
                                'text'=>$status['text'],
                                'possibly_sensitive'=>$possibly_sensitive,// boolean
                                'pre_saved'=>true,
                                'created'=>time()
                                );

        // primary key ++
        $this->create();
        
        // save the status
        $this->save($status_to_save);
        
        // save entities belonging to this status
        $this->Entity->saveEntities($this->id,$status);
        
    }

    public function savePreSavedStatus($user_id){

        /**
         * make all the statuses specified user has non-pre-saved
         */

        $this->updateAll(
                         array(
                               'Status.pre_saved'=>false
                               ),
                         array(
                               'Status.user_id'=>$user_id,
                               'Status.pre_saved'=>true
                               )
                         );
        
        $this->updateSavedTime($user_id);
        
    }

    public function deletePreSavedStatus($user_id){
        /**
         * delete the statuses which are set as pre-saved if any
         */
        
        $count_pre_saved = $this->find(
                                       'count',
                                       array(
                                             'Status.user_id'=>$user_id,
                                             'Status.pre_saved'=>true
                                             )
                                       );
        
        if($count_pre_saved > 0){
            return $this->deleteAll(
                                    array(
                                          'Status.user_id'=>$user_id,
                                          'Status.pre_saved'=>true
                                          )
                                    );
        }else{
            return false;
        }
        
    }
    

    
    ///////////////////////
    // boolean functions //
    ///////////////////////

    public function hasOlderStatus($user_id,$timestamp){

        /**
         * checks if user has status whose created_at is smaller than specified $timestamp
         * @param int $user_id
         * @param int $timestamp
         * @param string $mode, which indicates for what kind of statuses this method check
         * @return boolean
         */

        $conditions = array(
                            'Status.user_id'=>$user_id,
                            'Status.created_at <' => $timestamp,
                            'Status.pre_saved' => false
                            );

        $count_older_status = $this->find(
                                          'count',
                                          array(
                                                'conditions'=>$conditions
                                                )
                                          );
        
        return ($count_older_status > 0) ? true : false;
            
    }

    public function hasOlderTimeline($user_id,$timestamp){
       
        /**
         * checks if model has any status older than $timestamp ,posted by user's friends 
         * @param int $user_id
         * @param $timestamp int
         * @return boolean
         */

        // get user's friend ids
        $twitter_ids = $this->User->Friend->getFriendIds($user_id);
        // find user_ids IN $twitter_ids
        $user_ids = $this->User->getIdByTwitterId($twitter_ids);

        $conditions = array(
                            'Status.user_id' => $user_ids,
                            'Status.created_at <' => $timestamp,
                            'Status.pre_saved' => false,
                            );

        $count = $this->find(
                             'count',
                             array(
                                   'conditions'=>$conditions
                                   )
                             );
        
        return $count > 0 ? true : false;
    }

    public function hasOlderPublicTimeline($timestamp){
       
        /**
         * checks if model has any status older than $timestamp 
         * @param $timestamp int
         * @return boolean
         */

        // get the ids of all the users
        $ids = $this->User->getIds();

        $conditions = array(
                            'Status.user_id' => $ids,
                            'Status.created_at <' => $timestamp,
                            'Status.pre_saved' => false
                            );

        $count = $this->find(
                             'count',
                             array(
                                   'conditions'=>$conditions
                                   )
                             );
        return $count > 0 ? true : false;
    }

    ///////////
    // utils //
    ///////////

    public function getStatusNum($user_id){

        /**
         * count the  number of specified user's statuses saved
         * @param int $user_id
         * @return int if found, otherwise false
         */

        return $this->find(
                           'count',
                           array(
                                 'conditions'=>array(
                                                     'Status.user_id'=>$user_id,
                                                     'Status.pre_saved'=>false
                                                     )
                                 )
                           );

    }

    public function getLastUpdatedTime($user_id){

        /**
         * check when specified user's status was updated
         * @param int $user_id
         * @return int (unixtime)
         */

        $this->User->unbindAllModels();
        
        $user = $this->User->findById($user_id);
        
        return $user['User']['statuses_updated'];
    }

    public function updateSavedTime($user_id){
        /**
         * update User.statuses_updated to current unixtime
         */

        return $this->User->updateAll(
                                      array(
                                            'User.statuses_updated'=>time()
                                            ),
                                      array(
                                            'User.id'=>$user_id
                                            )
                                      );

    }

    public function unbindAllModels(){

        foreach($this->hasMany as $model => $inner){
            $hasMany[] = $model;
        }

        foreach($this->belongsTo as $model => $inner){
            $belongsTo[] = $model;
        }

        return $this->unbindModel(
                                  array(
                                        'hasMany'=>$hasMany,
                                        'belongsTo'=>$belongsTo
                                        )
                                  );

    }

}