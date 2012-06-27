<?php

class Status extends AppModel{
    
    public $name = 'Status';
    
    public $hasMany = array(
                            'Entity' => array(
                                              'className'=>'Entity',
                                              'foreignKey'=>'status_id',
                                              'order'=>'Entity.id',
                                              'dependent'=>true
                                              )
                            );

    public $belongsTo = array(
                              'User'=>array(
                                            'className'=>'User',
                                            'foreignKey'=>'user_id',
                                            'dependent'=>true
                                            )
                              );

    // this value is set when any action named get* has executed
    // represents if retrieved data has neighbor record in each condition
    public $hasNext = "";

    public function getStatusInTerm($user_id,$begin,$end,$order = 'DESC',$limit = '10'){
        
        /**
         * acquire user's statuses in specified term
         * returns array if there is any
         * returns false if there is no status in specified term
         */

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array(
                                                          'Status.user_id'=>$user_id,
                                                          'Status.created_at >=' => $begin,
                                                          'Status.created_at <=' => $end
                                                          ),
                                      'limit'=>$limit,
                                      'order'=>'Status.created_at '.$order
                                      )
                                );
        // check if neighbor record exists



     
        // return result
        return $this->checkNum($statuses);
        
    }

    public function getFollowingStatuses($following_id_list,$limit = 10){
        
        /**
         * retrieve statuses tweeted by those whom user is following on twitter
         * returns $limit statuses ordering in created_at DESC 
         * @param array list of twitter id user is following
         * @return array just like mentioning above 
         */

        $ret = $this->find(
                           'all',
                           array(
                                 'conditions'=>array(
                                                     'Status.twitter_id'=>$following_id_list
                                                     ),
                                 'order'=>'Status.created_at DESC',
                                 'limit'=>$limit
                                 )
                           );

        return $this->checkNum($ret);

    }

    public function getDateList($user_id){

        /**
         * creates date list of user's statuses
         * @param int $user_id
         * @return array
         */
        
        $user_data = $this->User->findById($user_id);
        $status_date_list = $this->getCreatedAtList($user_id);
        
        // classify them by date
        $utc_offset = $user_data['User']['utc_offset'];
       
        foreach($status_date_list as $key=>$created_at){
            $created_at += $utc_offset;

            $year = date('Y',$created_at);
            $month = date('n',$created_at);
            $day = date('j',$created_at);

            $sum_by_year[$year] = isset($sum_by_year[$year]) ? $sum_by_year[$year]+1 : 1;
            $sum_by_month[$year][$month] = isset($sum_by_month[$year][$month]) ? $sum_by_month[$year][$month]+1 : 1;
            $sum_by_day[$year][$month][$day] = isset($sum_by_day[$year][$month][$day]) ? $sum_by_day[$year][$month][$day]+1 : 1;

        }
        
        //return $this->checkNum($sum_by_day);

    }
    
    private function getCreatedAtList($user_id,$order = 'DESC'){

        /**
         * creates the list of posted timestamp of user's tweets
         * @param int $user_id
         * @order string
         */

        $ret = $this->find(
                           'list',
                           array(
                                 'conditions'=>array(
                                                     'Status.user_id'=>$user_id
                                                     ),
                                 'fields'=>array(
                                                 'Status.created_at'
                                                 ),
                                 'order'=>'Status.created_at '.$order
                                 )
                           );
    
        return $this->checkNum($ret);

    }

    public function getLatestStatus($user_id,$limit = '10'){

        /**
         * acquire user's latest statsues
         * returns array if there is any
         * returns false if there is nothing to return
         */

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array('Status.user_id'=>$user_id),
                                      'limit'=>$limit,
                                      'order'=>'Status.created_at DESC'
                                      )
                                );
        // return result
        return $this->checkNum($statuses);
        
    }

    public function getOlderStatus($user_id,$threshold_timestamp,$limit = '10'){
        
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
                                                          'Status.created_at <'=>$threshold_timestamp
                                                          ),
                                      'limit'=>$limit,
                                      'order'=>'Status.created_at DESC'
                                      )
                                );
        
        return $this->checkNum($statuses);
    }

    public function getOlderTimeline($twitter_id_list,$timestamp,$limit = 10){

        // retrieve statuses
        $conditions = array(
                            'Status.twitter_id' => $twitter_id_list,
                            'Status.created_at <' => $timestamp
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
                                        

    public function saveStatuses($user,$statuses){

        foreach($statuses as $status){

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
                                    'created'=>time()
                                    );

            // primary key ++
            $this->create();
            // save the status
            $this->save($status_to_save);
               
            // save entities belong to this status
            $this->Entity->saveEntities($this->id,$status,$user);
            
        }
    }

    public function hasOlderTimeline($following_list,$timestamp){
        /**
         * checks if model has any status posted by $following_list and older than $timestamp
         * @param array $following_list
         * @param $timestamp int
         * @return boolean
         */

        $conditions = array(
                            'Status.twitter_id' => $following_list,
                            'Status.created_at <' => $timestamp
                            );

        $count = $this->find(
                             'count',
                             array(
                                   'conditions'=>$conditions
                                   )
                             );
        
        return $count > 0 ? true : false;
    }

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
                            'Status.created_at <' => $timestamp
                            );

        $count_older_status = $this->find(
                                          'count',
                                          array(
                                                'conditions'=>$conditions
                                                )
                                          );
        
        return ($count_older_status > 0) ? true : false;
            
    }

    private function checkNum($statuses){

        /**
         * check if given $statuses contains any status
         * returns array if there is any
         * returns false if none
         */
     
        if(count($statuses) > 0){
            return $statuses;
        }else{
            return false;
        }
   
    }

}