<?php

/**
 * Controller/AjaxController.php
 */

class AjaxController extends AppController{
    
    // chose the layout to render
    public $layout = 'ajax';
    
    public $components = array('Parameter');

    public function beforeFilter(){

        parent::beforeFilter();
        
        // make all the actions need authentication
        $this->Auth->deny();

        // reject non-ajax requests
        if(!$this->request->isAjax()){
            echo "bad request";
            exit;
        }

        $this->autoRender = false;
    
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

        // initialization
        $noStatusAtAll = false;
        $continue = false;
        $statuses = array();
        $following_list = array();
        $ret = array();
        
        $user = $this->Auth->user();
        $token = $this->User->getTokens($user['id']);
            
        ///////////////////////////////                           
        // acquire and save statuses // 
        ///////////////////////////////                           
        
        // set params for api call
        $api_params = array(
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'screen_name'=>$user['Twitter']['screen_name']
                            );
        
        // this is the oldest tweet's id of the statuses which have imported so far
        $max_id = $this->request->data('id_str_oldest');
                
        // configure parameters 
        if(!$max_id){

            /*
             * this is the case for first ajax request
             */
            
            // delete all the statuses whose pre_saved flag is true
            $this->Status->deletePreSavedStatus($user['id']);
            
            // set 100 as the number of statuses to acquire
            $api_params['count'] = 100;
            
            // acquire latest 100 statuses
            $statuses = $this->Twitter->get('statuses/user_timeline',$api_params);
            $statuses = json_decode($statuses['body'],true);

            // retrieve following list
            $following_list = json_decode($this->Twitter->get('friends/ids',array('user_id'=>$user['Twitter']['id'],'stringify_ids'=>true)),true);
            $this->Friend->saveFriends($user['id'],$following_list['ids']);
            
            if(count($statuses) == 0){
                // set the flag that there was no status to acquire at all
                $noStatusAtAll = true;
            }
            
        }else{
            
            // acquire 101 statuses which are older than the status with max_id
            $api_params['count'] = 101;
            $api_params['max_id'] = $max_id;
            
            // acquire 101 statuses older than max_id
            $statuses = $this->Twitter->get('statuses/user_timeline',$api_params);
            $statuses = json_decode($statuses['body'],true);
         
            // remove the newest status from result because it has been already saved in previous loop
            if(count($statuses)>0){
                
                array_shift($statuses);
           
            }
        }
        
        // save acquired data if any
        if(count($statuses)>0){
            
            foreach($statuses as $status){
            
                $this->Status->saveStatus($user,$status);

            }
       
        }
           
        ////////////////////////////////////                              
        // define the json data to return // 
        ////////////////////////////////////
        
        // determine whether continue the loop in ajax or not
        $continue = count($statuses) > 0 ? true : false;
    
        // number of statuses added to database
        $saved_count = count($statuses);

        $ret['continue'] = $continue;
        $ret['saved_count'] = $saved_count;
        $ret['noStatusAtAll'] = $noStatusAtAll;

        if($continue){
        
            // the status to show as one which is currently fetching
            $last_status = end($statuses);
            $text = $last_status['text'];       
            $id_str_oldest = $last_status['id_str'];

            $utc_offset = $last_status['user']['utc_offset'];
            $created_at = strtotime($last_status['created_at']);// convert its format to unix time
            $created_at += $utc_offset;// timezone equal to the one configured in user's twitter profile
            $created_at = date("Y年m月d日 - H:i",$created_at);

            $ret['id_str_oldest'] = $id_str_oldest;
            $ret['status'] = array(
                                   'date'=>$created_at,
                                   'text'=>$text
                                   );
        
        }else{
            
            if(!$noStatusAtAll){
                
                // turn initialized flag true in user model
                $this->User->id = $user['id'];
                $this->User->saveField('initialized_flag',true);
           
                // make statuses non-pre-saved
                $this->Status->savePreSavedStatus($user['id']);
            }
        
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
        $continue = true;
        $oldest_id_str = "";
        $updated_date = "";

        // recieve the status_id of the status which is oldest of all the statuses saved in last loop
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
            
            // delete pre-saved statuses
            $this->Status->deletePreSavedStatus($user['id']);

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

        if($tweets){

            // check id_str of oldest status
            $oldest_status = $this->getLastLine($tweets);
            $oldest_id_str = $oldest_status['id_str'];
        
            // set the destination value for created_at
            $latest_status = $this->Status->getLatestStatus($user['id'],1);
            $destination_time = $latest_status[0]['Status']['created_at'];
        
        
            // save lacking tweets if any
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
    
        }else{

            $continue = false;

        }

        if(!$continue){

            // make all the pre_saved statuses non-pre-saved
            $this->Status->savePreSavedStatus($user['id']);

        }

        $updated_time = $this->Status->getLastUpdatedTime($user['id']);
        $updated_date = date('Y-m-d　H:i:s',$updated_time+$user['Twitter']['utc_offset']);

        $ret = array(
                     'count_saved'=>$count_saved,
                     'continue'=>$continue,
                     'oldest_id_str'=>$oldest_id_str,
                     'updated_date'=>$updated_date
                     );
        
        echo json_encode($ret);

    }

    public function delete_status(){
        
        if(!$this->request->isPost()){
            echo "bad request";
            exit;
        }
        
        $status_id = $this->request->data('status_id_to_delete');
        $user_id = $this->Auth->user('id');
        $deleted = false;
        $owns = false;
        
        // check if user owns the status with $status_id
        if($this->User->ownsStatus($user_id,$status_id)){
            $owns = true;
            $this->Status->id = $status_id;
            
            // delete the status and switch the flag
            if($this->Status->delete()){
                $deleted = true;
            }
        
        }
        
        $ret = array(
                     'deleted'=>$deleted,
                     'owns'=>$owns
                     );
        
        echo json_encode($ret);
        
    }

    public function delete_account(){
       
        if(!$this->request->isPost()){

            echo "bad request";
            exit;

        }

        $user_id = $this->Auth->user('id');
        
        // initialize the flag representing if deleting went well 
        $deleted = false;

        if($this->User->deleteAccount($user_id)){
            $deleted = true;
        }
  
        sleep(2);
        $ret = array('deleted'=>$deleted);
        echo json_encode($ret);
        
    }

    public function switch_dashbord(){
        
        /**
         * creates a dashbord html code for requested action type
         * returns html
         */
        
        $action_type = $this->request->data('action_type');
       
        if(!$action_type){
            echo "action type is not specified";
            exit;
        }

        $user_id = $this->Auth->user('id');

        $date_list = $this->Status->getDateList($user_id,$action_type);
        
        // render to template
        $this->autoRender = true;
        $this->set('date_list',$date_list);
        $this->set('actionType',$action_type);
    }

    public function switch_term(){

        /**
         * retrieve the statuses if specified term
         * renders html
         */
        
        $user = $this->Auth->user();
        $user_id = $user['id'];
        $utc_offset = $user['Twitter']['utc_offset'];
        
        $fetchLatest = false;

        // fetch query string
        $date = $this->request->query['date'];
        $action_type = $this->request->query['action_type'];
        
        // check if date parameter is specified
        if($date === 'notSpecified'){
            $fetchLatest = true;
        }else{

            $date_type = $this->request->query['date_type'];
            
            // calculate start/end of term to fetch 
            $term = $this->Parameter->termToTime($date,$date_type,$utc_offset);
        }
        
        switch($action_type){

        case 'sent_tweets':
            
            if($fetchLatest){
                
                $statuses = $this->Status->getLatestStatus($user_id);
            
            }else{

                // fetch 10 statsues in specified term
                $statuses = $this->Status->getStatusInTerm($user_id,$term['begin'],$term['end']);
            
            }

            $last_status = $this->getLastLine($statuses);
            $oldest_timestamp = $last_status['Status']['created_at'];
        
            // check if any older status exists in user's timeline  
            $hasNext = $this->Status->hasOlderStatus($user_id,$oldest_timestamp);                    
           
            break;

        case 'home_timeline':

            if($fetchLatest){

                $statuses = $this->Status->getLatestTimeline($user_id);

            }else{

                // fetch 10 timeline in specified term
                $statuses = $this->Status->getTimelineInTerm($user_id,$term['begin'],$term['end']);

            }

            $last_status = $this->getLastLine($statuses);
            $oldest_timestamp = $last_status['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderTimeline($user_id,$oldest_timestamp);

            break;

        case 'public_timeline':

            if($fetchLatest){
                
                $statuses = $this->Status->getLatestPublicTimeline();

            }else{

                // fetch 10 timeline in specified term
                $statuses = $this->Status->getPublicTimelineInTerm($term['begin'],$term['end']);
            
            }

            $last_status = $this->getLastLine($statuses);
            $oldest_timestamp = $last_status['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderPublicTimeline($oldest_timestamp);

            break;

        default:

            break;
            
        }
        
        $this->autoRender = true;
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
        $destination_action_type = $this->request->data('destination_action_type');
        $user = $this->Auth->user();
 
        switch($destination_action_type){

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

        case 'public_timeline':
           
            // fetch older timeline
            $statuses = $this->Status->getOlderPublicTimeline($oldest_timestamp);
            
            // set created_at of last status 
            $last_status = $this->getLastLine($statuses);
            
            $oldest_timestamp = $last_status['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderPublicTimeline($oldest_timestamp);
            
            break;
        }
        
        $this->autoRender = true;
        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldest_timestamp);        
        $this->set('statuses',$statuses);
    
    }

    public function check_status_update(){
        
        /**
         * checks if there is any updatable tweet on twitter.com
         */
        
        $user = $this->Auth->user();
        $user_id = $user['id'];
                
        $params = array(
                        'user_id'=>$user['Twitter']['id'],
                        'count'=>1,
                        'include_rts'=>true,
                        );
        
        $latest_tweet = json_decode($this->Twitter->get('statuses/user_timeline',$params),true);
        $latest_status = $this->Status->getLatestStatus($user_id,1);
        
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
        
        // record the time updeted
        $this->Status->updateSavedTime($user_id);
        $updated_time = $this->Status->getLastUpdatedTime($user_id);
        $updated_date = date('Y-m-d　H:i:s',$updated_time+$user['Twitter']['utc_offset']);

        $ret['updated_date'] = $updated_date;
        
        echo json_encode($ret);
        
    }

    public function check_friend_update(){
        
        /**
         * checks if there is any friend on twitter
         */
        
        if(!$this->request->isPost()){

            echo "bad request";
            exit;

        }
        
        $user = $this->Auth->user();
        $user_id = $user['id'];

        // initialization
        $doUpdate = false;

        // fetch the list of user's friends
        $friends['db'] = $this->Friend->getFriendIds($user_id);

        // check if user has any friends imported
        if(!$friends['db']){

            // if not,do update
            $doUpdate = true;

        }else{
            
            // fetch same list from twitter
            $following_friends = json_decode($this->Twitter->get('friends/ids'),true);
            $friends['twitter'] = $following_friends['ids'];

            // compare the number of ids contained in each array
            $count_db = count($friends['db']);
            $count_twitter = count($friends['twitter']);

            if($count_db != $count_twitter){

                // if not equals
                $doUpdate = true;

            }else{
                
                // check if $friends['twitter'] contains any id that is not contained in $friends['db']
                foreach($friends['twitter'] as $my_tw_friend){

                    $idExists = in_array($my_tw_friend,$friends['db']);

                    if(!$idExists){

                        $doUpdate = true;
                        // if detected,break the loop
                        break;

                    }

                }  
                
            }
        }
        
        // decide update or not
        if($doUpdate){

            if(!isset($friends['twitter'])){
             
                $following_friends = json_decode($this->Twitter->get('friends/ids'),true);
                $friends['twitter'] = $following_friends['ids'];

            }

            // update friends list
            $this->Friend->updateFriends($user_id,$friends['twitter']);
        
        }else{
       
            // just update friends_updeted time
            $this->Friend->updateTime($user_id);
        
        }

        // get the total number of friends
        $count_friends = $this->Friend->getFriendNum($user_id);
        
        // get the updated time
        $updated_time = $this->Friend->getLastUpdatedTime($user_id);
        $updated_date = date('Y-m-d　H:i:s',$updated_time+$user['Twitter']['utc_offset']);

        // prepare the array to return
        $ret = array(
                     'updated'=>$doUpdate,
                     'count_friends'=>$count_friends,
                     'updated_date'=>$updated_date
                     );

        echo json_encode($ret);

    }

}