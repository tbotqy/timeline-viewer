<?php

/**
 * Controller/AjaxController.php
 */

class AjaxController extends AppController{
    
    // chose the layout to render
    public $layout = 'ajax';
    
    // set the models to be used
    public $uses = array('User','Status','Entity');
    
    public function beforeFilter(){
        parent::beforeFilter();
    }
            
    public function acquire_statuses(){

        /** 
         * calls twitter api to retrieve user's twitter statuses
         * returns json string
         */
               
        if(!( $this->request->is('ajax') && $this->request->is('post'))){
            echo "bad request";
            exit;
        }
        
        $this->autoRender = false;
        $this->Twitter->initialize($this);

        $user = $this->Auth->user();
        $token = $this->User->getTokens($user['id']);
            
        //                           
        // acquire and save statuses 
        //                           

        // set params for api call
        $api_params = array(
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'screen_name'=>$user['Twitter']['screen_name']
                            );
        
        // this is the oldest tweet's id among those which have imported so far
        $max_id = $this->request->data('id_str_oldest');
                
        // configure parameters 
        if(!$max_id){
            // this is the case for first ajax request

            // turn initialized flag true in user model
            /*
              [ToDo] uncomment this when release
              $this->User->updateAll(
              array('initialized_flag'=>true),
              array('User.id'=>$user['id'])
              );
            */
            // set 100 as the number of statuses to acquire
            $api_params['count'] = 100;
            
            // acquire latest 100 statuses
            $statuses = $this->Twitter->get('statuses/user_timeline',$api_params);
            $statuses = json_decode($statuses['body'],true);

        }else{
            
            // acquire 101 statuses which are older than the status with max_id
            $api_params['count'] = 101;
            $api_params['max_id'] = $max_id;
            
            // acquire 101 statuses older than max_id
            $statuses = $this->Twitter->get('statuses/user_timeline',$api_params);
            $statuses = json_decode($statuses['body'],true);
         
            // remove the newest status from result because it has been already saved in previous loop
            array_shift($statuses);
        }
        
        // save acquired data if any
        if($statuses){
        
            $this->Status->saveStatuses($user,$statuses);
       
        }
           
        //                                
        // define the json data to return 
        //                                
        
        // determine whether continue the loop in ajax or not
        $continue = count($statuses) > 0 ? true : false;
    
        // number of statuses added to database
        $saved_count = count($statuses);
        
        // the status to show as one which is currently fetching
        $last_status = end($statuses);
        $text = $last_status['text'];       
        $id_str_oldest = $last_status['id_str'];

        $utc_offset = $last_status['user']['utc_offset'];
        $created_at = strtotime($last_status['created_at']);// convert its format to unix time
        $created_at -= 32400;// [ToDo] check if this process is necessary or not -> fix server's timezone offset
        $created_at += $utc_offset;// timezone equal to the one configured in user's twitter profile
        $created_at = date("Y/m/d - H:i",$created_at);
      
        $ret = array(
                     'continue' => $continue,
                     'saved_count' => $saved_count,
                     'id_str_oldest' => $id_str_oldest,
                     'status' => array(
                                       'date'=>$created_at,
                                       'text'=>$text
                                       )
                     );
        
        // return json
        echo json_encode($ret);
    }

    public function switch_term(){

        /**
         * retrieve the statuses if specified term
         * renders html
         */
        
        $user_id = $this->Auth->user('id');
        $user = $this->Auth->user();
        $utc_offset = $user['Twitter']['utc_offset'];

        // fetch query string
        $date = $this->request->query['date'];
        $date_type = $this->request->query['date_type'];
       
        // calculate start/end of term to fetch 
        $term = $this->termToTime($date,$date_type,$utc_offset);
        
        // fetch user's twitter account info
        $user_data = $this->User->findById($user_id);

        // fetch 10 statsues in specified term
        $statuses = $this->Status->getStatusInTerm($user_id,$term['begin'],$term['end']);
        
        $itr = count($statuses) - 1;
        $oldest_timestamp = $statuses[$itr]['Status']['created_at'];
        $hasNext = $this->Status->hasOlderStatus($user_id,$oldest_timestamp);        
        
        $this->set('oldest_timestamp',$oldest_timestamp);
        $this->set('hasNext',$hasNext);
        $this->set('statuses',$statuses);       
        $this->set('user_data',$user_data);
    }

    public function read_more(){
        
        /**
         * called when read more button is clicked
         * receives Status.id to start retrieving
         * renders html
         */

        if(!$this->request->is('ajax')){
            // reject the request
            echo 'bad request';
            exit;
        }

        // fetch more 10 statuses whose id is greater than last status id
        $oldest_timestamp = $this->request->data('oldest_timestamp');       
        $user = $this->Auth->user();

        // fetch user's twitter account info
        $user_data = $this->User->findById($user['id']);
        
        $statuses = $this->Status->getOlderStatus($user['id'],$oldest_timestamp);
        $itr = count($statuses)-1;
        $oldest_timestamp = $statuses[$itr]['Status']['created_at'];

        $hasNext = $this->Status->hasOlderStatus($user['id'],$oldest_timestamp);
        
        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldest_timestamp);        
        $this->set('statuses',$statuses);
        $this->set('user_data',$user_data);
    }
}