<?php

/**
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    // chose the layout to render
    public $layout = 'common';
    
    // set the models to be used
    public $uses = array('User','Status','Entity');
    
    // this stuff helps me !
    public $helpers = array('Text');

    public function beforeFilter(){
        $this->Auth->deny('import');
        parent::beforeFilter();
    }

    public function test(){
        $api_params = array('include_entities'=>true);
        $statuses = $this->Twitter->get('statuses/user_timeline',$api_params);
        $statuses = json_decode($statuses['body'],true);
        
        foreach($statuses as $status){
            $entities = $status['entities'];
            foreach($entities as $type=>$val){
                
                if(count($val)>0){
                    echo "saved<br/>";
                }
                
            }
        }

    }
    
    public function import(){

        /*
         * show the screen for operating import method
         */
        
        $user =  $this->Auth->user();
        $this->set('screen_name',$user['Twitter']['screen_name']);
        $this->set('profile_image',$user['Twitter']['profile_image_url_https']);
    }

    public function acquire_statuses(){

        /** 
         * calls twitter api to retrieve user's twitter statuses
         * interacts with JavaScript with ajax
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

    public function read_more(){
        
        $this->layout = 'ajax';

        if(!$this->request->is('ajax')){
            // reject that request
            echo 'bad request';
            exit;
        }

        // fetch more 10 statuses whose id is greater than last status id
        $last_status_id = $this->request->data('last_status_id');       
        $user = $this->Auth->user();
        $twitter_id = $user['Twitter']['id'];
        
        // fetch user's twitter account info
        $user_data = $this->User->findById($user['id']);
        
        $statuses = $this->Status->getStatusOlderThanId($user['id'],$last_status_id);
        
        $itr = count($statuses)-1;
        $last_status_id = $statuses[$itr]['Status']['id'];
        $this->set('last_status_id',$last_status_id);        
        $this->set('statuses',$statuses);
        $this->set('user_data',$user_data);
    }

    public function switch_term(){

        /*
         * change which term of statuses to show
         * returns html responce
         */

        $this->layout = 'ajax';
        $user = $this->Auth->user();
        $twitter_id = $user['Twitter']['id'];
        $utc_offset = $user['Twitter']['utc_offset'];

        // fetch query string
        $date = $this->request->query['date'];
        $date_type = $this->request->query['date_type'];
       
        // calculate start/end of term to fetch 
        $term = $this->termToTime($date,$date_type,$utc_offset);
        
        // fetch user's twitter account info
        $user_data = $this->User->findById($user['id']);

        // fetch 10 statsues in specified term
        $statuses = $this->Status->getStatusInTerm($user['id'],$term['begin'],$term['end']);
        
        $itr = count($statuses) - 1;
        
        $last_status_id = $statuses[$itr]['Status']['id'];

        $this->set('statuses',$statuses);
        $this->set('last_status_id',$last_status_id);
        $this->set('user_data',$user_data);
        $this->render('switch_term');
    }

}