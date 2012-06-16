<?php

class Status extends AppModel{
    
    public $name = 'Status';
    public $hasMany = array(
                            'Entity' => array(
                                              'className'=>'Entity',
                                              'foreignKey'=>'status_id_str',
                                              'order'=>'Entity.id',
                                              'dependent'=>true
                                              )
                            );

    public function getLatestStatus(int $user_id,int $limit = '10'){

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

    public function getStatusInTerm(int $user_id,int $begin,int $end,$order = 'DESC',$limit = '10'){
        
        /**
         * acquire user's statuses in specified term
         * returns array if there is any
         * returns false if there is no status in specified term
         */

        $statuses = $this->find(
                                'all',
                                array(
                                      'conditions'=>array(
                                                          'Status.id'=>$user_id,
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