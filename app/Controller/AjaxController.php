<?php

/**
 * Controller/AjaxController.php
 */

class AjaxController extends AppController{
    
    // chose the layout to render
    public $layout = 'ajax';
    
    // set the models to be used
    public $uses = array('User','Status','Entity','Friend');
    
    public function beforeFilter(){
        parent::beforeFilter();
        $this->Twitter->initialize($this);
        
        // make all the actions need authentication
        $this->Auth->deny();

        // reject non-ajax requests
        if(!( $this->request->isAjax())){
            echo "bad request";
            exit;
        }

    }
            
    public function acquire_statuses(){

        /** 
         * calls twitter api to retrieve user's twitter statuses
         * returns json string
         */
               
        if(!$this->request->isPost()){
            echo "bad request";
            exit;
        }
        
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

            // retrieve following list
            $following_list = json_decode($this->Twitter->get('friends/ids',array('user_id'=>$user['Twitter']['id'],'stringify_ids'=>true)),true);
            $this->Friend->saveFriends($user['id'],$following_list['ids']);
            
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
        
            foreach($statuses as $status){
                $this->Status->saveStatus($user,$status);
            }
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
        $created_at += $utc_offset;// timezone equal to the one configured in user's twitter profile
        $created_at = date("Y年m月d日 - H:i",$created_at);
      
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
        $this->set('responce',json_encode($ret));
    }

    public function checkUpdate(){
        
        /**
         * checks if there is any updatable tweet on twitter.com
         */
        
        $this->autoRender = false;
        $user = $this->Auth->user();
        $params = array(
                        'user_id'=>$user['Twitter']['id'],
                        'count'=>1,
                        'include_rts'=>true,
                        );
        
        $latest_tweet = json_decode($this->Twitter->get('statuses/user_timeline',$params),true);
        $latest_status = $this->Status->getLatestStatus($user['id'],1);
        
        $latest_tweet_created_at = strtotime($latest_tweet[0]['created_at']);
        $latest_status_created_at = $latest_status[0]['Status']['created_at'];

        // compare created_at
        if($latest_tweet_created_at > $latest_status_created_at){
            // updatable
            $ret = array('result'=>true);
        }else{
            // no tweet updatable
            $ret = array('result'=>false);
        }
        
        echo json_encode($ret);
    }

    public function update_statuses(){
        
        /**
         * acquire tweets which exist in twitter.com but hasn't imported to our database yet
         */

        if(!$this->request->isPost()){
            echo "bad request";
            exit;
        }

        $user = $this->Auth->user();
        
        // initialization
        $count_saved = 0;
        $continue = false;
        $destination_time = "";
        $max_id = $this->request->data('oldest_id_str');
        
        if($max_id){
            
            // set params for api request            
            $params = array(
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'count'=>101,
                            'user_id'=>$user['Twitter']['id'],
                            'max_id'=>$max_id
                            );
                           
            // fetch tweets via api
            $tweets = json_decode($this->Twitter->get('statuses/user_timeline',$params),true);
        
            // delete duplicating status from $tweets if $max_id was set
            array_shift($tweets);
        
        }else{
            // set the destination value for created_at
            $latest_status = $this->Status->getLatestStatus($user['id'],1);
            $destination_time = $latest_status[0]['Status']['created_at'];
            
            // set params for api request            
            $params = array(
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'count'=>100,
                            'user_id'=>$user['Twitter']['id'],
                            );
                           
            // fetch tweets via api
            $tweets = json_decode($this->Twitter->get('statuses/user_timeline',$params),true);
        }

        // check id_str of oldest status
        $oldest_status = $this->getLastLine($tweets);
        $oldest_id_str = $oldest_status['id_str'];

        // save lacking tweets
        foreach($tweets as $tweet){
                
            if(strtotime($tweet['created_at']) > $destination_time){
                // save it
                $this->Status->saveStatus($user,$tweet);
                $count_saved++;

            }else{
                // stop saving
                $continue = false;
                break;
            }
        }
    
        $ret = array(
                     'destination_time'=>$destination_time,
                     'count_saved'=>$count_saved,
                     'continue'=>$continue,
                     'oldest_id_str'=>$oldest_id_str
                     );
        
        $this->set('responce',json_encode($ret));

    }

    public function switch_term(){

        /**
         * retrieve the statuses if specified term
         * renders html
         */

        $user = $this->Auth->user();
        $user_id = $user['id'];
        $utc_offset = $user['Twitter']['utc_offset'];

        // fetch query string
        $date = $this->request->query['date'];
        $date_type = $this->request->query['date_type'];
        $data_type = $this->request->query['data_type'];
        
        // calculate start/end of term to fetch 
        $term = $this->termToTime($date,$date_type,$utc_offset);
        
        switch($data_type){
        case 'sent_tweets':
            // fetch 10 statsues in specified term
            $statuses = $this->Status->getStatusInTerm($user_id,$term['begin'],$term['end']);
        
            $last_status = $this->getLastLine($statuses);
            $oldest_timestamp = $last_status['Status']['created_at'];
        
            // check if any older status exists in user's timeline  
            $hasNext = $this->Status->hasOlderStatus($user_id,$oldest_timestamp);                    
           
            break;

        case 'home_timeline':
            // fetch 10 timeline in specified term
            $statuses = $this->Status->getTimelineInTerm($user_id,$term['begin'],$term['end']);
            
            $last_status = $this->getLastLine($statuses);
            $oldest_timestamp = $last_status['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderTimeline($user_id,$oldest_timestamp);

            break;

        default:
            //[ToDo] show the view to notice that there is no data to show
            break;
            
        }


        
        $this->set('oldest_timestamp',$oldest_timestamp);
        $this->set('hasNext',$hasNext);
        $this->set('statuses',$statuses);       
    }

    public function read_more(){
        
        /**
         * called when read more button is clicked
         * receives Status.id to start retrieving
         * renders html
         */

        $oldest_timestamp = $this->request->data('oldest_timestamp');
        $destination_data_type = $this->request->data('destination_data_type');
        $user = $this->Auth->user();
 
        switch($destination_data_type){

        case 'sent_tweets':
            // fetch older statuses
            $statuses = $this->Status->getOlderStatus($user['id'],$oldest_timestamp);

            // set created_at of last status 
            $last_status = $this->getLastLine($statuses);
            $oldest_timestamp = $last_status['Status']['created_at'];

            // check if any older status exists
            $hasNext = $this->Status->hasOlderStatus($user['id'],$oldest_timestamp);
           
            break;

        case 'home_timeline':
           
            // fetch older timeline
            $statuses = $this->Status->getOlderTimeline($user['id'],$oldest_timestamp);
            
            // set created_at of last status 
            $last_status = $this->getLastLine($statuses);
            
            $oldest_timestamp = $last_status['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderTimeline($user['id'],$oldest_timestamp);
            
            break;
        }

        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldest_timestamp);        
        $this->set('statuses',$statuses);
    }

}