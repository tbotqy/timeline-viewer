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

    public function getDateList($user_id){
        
        $user_data = $this->User->findById($user_id);
        $status_date_list = $this->getListOfCreatedAt($user_id);
        
        // classify them by date
        $utc_offset = $user_data['User']['utc_offset'];
       
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
    
    public function getListOfCreatedAt($user_id){

        $ret = $this->find(
                           'list',
                           array(
                                 'conditions'=>array(
                                                     'Status.user_id'=>$user_id
                                                     ),
                                 'fields'=>array(
                                                 'Status.created_at'
                                                 ),
                                 'order'=>'Status.created_at DESC'
                                 )
                           );
    
        return $this->checkNum($ret);

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

    public function getStatusOlderThanId($user_id,$threshold_id,$limit = '10'){
        
        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array(
                                                          'Status.user_id'=>$user_id,
                                                          'Status.id >'=>$threshold_id
                                                          ),
                                      'limit'=>$limit,
                                      'order'=>'Status.created ASC'
                                      )
                                );
        
        return $this->checkNum($statuses);
    }

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
     
        // return result
        return $this->checkNum($statuses);
        
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