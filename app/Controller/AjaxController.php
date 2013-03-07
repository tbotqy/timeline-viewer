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
        // only allow some methods,which are accessed from /public_timedline
        $this->Auth->allow('read_more','switch_term');

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
        $followingList = array();
        $ret = array();
        
        $user = $this->Auth->user();
        $token = $this->User->getTokens($user['id']);
            
        ///////////////////////////////                           
        // acquire and save statuses // 
        ///////////////////////////////                           
        
        // set params for api call
        $apiParams = array(
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'screen_name'=>$user['Twitter']['screen_name']
                            );
        
        // this is the oldest tweet's id of the statuses which have imported so far
        $maxId = $this->request->data('id_str_oldest');
                
        // configure parameters 
        if(!$maxId){

            /*
             * this is the case for first ajax request
             */
            
            // delete all the statuses whose pre_saved flag is true
            $this->Status->deletePreSavedStatus($user['id']);
            
            // set 100 as the number of statuses to acquire
            $apiParams['count'] = 100;
            
            // acquire latest 100 statuses
            $statuses = $this->Twitter->get('statuses/user_timeline',$apiParams);
            
            // retrieve following list
            $followingList = $this->Twitter->get('friends/ids',array('user_id'=>$user['Twitter']['id'],'stringify_ids'=>true));
            $this->Friend->saveFriends($user['id'],$followingList['ids']);
            
            if(count($statuses) == 0){
                // set the flag that there was no status to acquire at all
                $noStatusAtAll = true;
            }
            
        }else{
            
            // acquire 101 statuses which are older than the status with max_id
            $apiParams['count'] = 101;
            $apiParams['max_id'] = $maxId;
            
            // acquire 101 statuses older than max_id
            $statuses = $this->Twitter->get('statuses/user_timeline',$apiParams);
            
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
        $savedCount = count($statuses);

        $ret['continue'] = $continue;
        $ret['saved_count'] = $savedCount;
        $ret['noStatusAtAll'] = $noStatusAtAll;

        if($continue){
        
            // the status to show as one which is currently fetching
            $lastStatus = end($statuses);
            $text = $lastStatus['text'];       
            $idStrOldest = $lastStatus['id_str'];

            $createdAt = strtotime($lastStatus['created_at']);// convert its format to unix time
            $utcOffset = $lastStatus['user']['utc_offset'];
            $createdAt = $this->convertTimeToDate($createdAt,$utcOffset,"Y年m月d日 - H:i");
            
            $ret['id_str_oldest'] = $idStrOldest;
            $ret['status'] = array(
                                   'date'=>$createdAt,
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
        $countSaved = 0;
        $continue = true;
        $oldestIdStr = "";
        $updatedDate = "";

        // recieve the status_id of the status which is oldest of all the statuses saved in last loop
        $maxId = $this->request->data('oldest_id_str');
        
        if($maxId){
            
            // set params for api request            
            $params = array(
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'count'=>101,
                            'user_id'=>$user['Twitter']['id'],
                            'max_id'=>$maxId
                            );
                           
            // fetch tweets via api
            $tweets = $this->Twitter->get('statuses/user_timeline',$params);
            
            // delete duplicating status from $tweets if $maxId was set
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
            $tweets = $this->Twitter->get('statuses/user_timeline',$params);

        }

        if($tweets){

            // check id_str of oldest status
            $oldestStatus = $this->getLastLine($tweets);
            $oldestIdStr = $oldestStatus['id_str'];
        
            // set the destination value for created_at
            $latestStatus = $this->Status->getLatestStatus($user['id'],1);
            $destinationTime = $latestStatus[0]['Status']['created_at'];
        
            // save lacking tweets if any
            foreach($tweets as $tweet){

                if(strtotime($tweet['created_at']) > $destinationTime){
               
                    // save it
                    $this->Status->saveStatus($user,$tweet);
                    $countSaved++;

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

        $updatedTime = $this->Status->getLastUpdatedTime($user['id']);
        $updatedDate = $this->convertTimeToDate($updatedTime,$user['Twitter']['utc_offset']);

        $ret = array(
                     'count_saved'=>$countSaved,
                     'continue'=>$continue,
                     'oldest_id_str'=>$oldestIdStr,
                     'updated_date'=>$updatedDate
                     );
        
        echo json_encode($ret);

    }

    public function delete_status(){
        
        if(!$this->request->isPost()){
            echo "bad request";
            exit;
        }
        
        $statusId = $this->request->data('status_id_to_delete');
        $userId = $this->Auth->user('id');
        $deleted = false;
        $owns = false;
        
        // check if user owns the status with $statusId
        if($this->User->ownsStatus($userId,$statusId)){
            $owns = true;
            $this->Status->id = $statusId;
            
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

        $userId = $this->Auth->user('id');
        
        // initialize the flag representing if deleting went well 
        $deleted = false;

        if($this->User->deleteAccount($userId,true)){
            $deleted = true;
        }
  
        sleep(2);
        $ret = array('deleted'=>$deleted);
        echo json_encode($ret);
        
    }
    
    public function delete_him(){

        if(!$this->request->isPost()){
            echo "bad request";
            exit;
        }

        $destinationUserId = $this->request->data('dest_id');

        if($this->User->deleteAccount($destinationUserId,true)){
            echo "OK";
        }else{
            echo "NG";
        }
        
    }

    public function switch_dashbord(){
        
        /**
         * creates a dashbord html code for requested action type
         * returns html
         */
        
        $actionType = $this->request->data('action_type');
       
        if(!$actionType){
            echo "action type is not specified";
            exit;
        }

        $userId = $this->Auth->user('id');

        $dateList = $this->Status->getDateList($userId,$actionType);
        
        // render to template
        $this->autoRender = true;
        $this->set('date_list',$dateList);
        $this->set('actionType',$actionType);
    }

    public function switch_term(){

        /**
         * retrieve the statuses if specified term
         * renders html
         */
        
        if($this->Auth->loggedIn()){
            $user = $this->Auth->user();
            $userId = $user['id'];
            $utcOffset = $user['Twitter']['utc_offset'];
        }else{
            $utcOffset = 32400;
        }

        $fetchLatest = false;

        // fetch query string
        $date = $this->request->query['date'];
        $actionType = $this->request->query['action_type'];
        
        // check if date parameter is specified
        if($date === 'notSpecified'){
            $fetchLatest = true;
        }else{

            $dateType = $this->request->query['date_type'];
            
            // calculate start/end of term to fetch 
            $term = $this->Parameter->termToTime($date,$dateType,$utcOffset);
        }
        
        switch($actionType){

        case 'tweets':
            
            if($fetchLatest){
                
                $statuses = $this->Status->getLatestStatus($userId);
            
            }else{

                // fetch 10 statsues in specified term
                $statuses = $this->Status->getStatusInTerm($userId,$term['begin'],$term['end']);
            
            }

            $lastStatus = $this->getLastLine($statuses);
            $oldestTimestamp = $lastStatus['Status']['created_at'];
        
            // check if any older status exists in user's timeline  
            $hasNext = $this->Status->hasOlderStatus($userId,$oldestTimestamp);                    
           
            break;

        case 'home_timeline':

            if($fetchLatest){

                $statuses = $this->Status->getLatestTimeline($userId);

            }else{

                // fetch 10 timeline in specified term
                $statuses = $this->Status->getTimelineInTerm($userId,$term['begin'],$term['end']);

            }

            $lastStatus = $this->getLastLine($statuses);
            $oldestTimestamp = $lastStatus['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderTimeline($userId,$oldestTimestamp);

            break;

        case 'public_timeline':

            if($fetchLatest){
                
                $statuses = $this->Status->getLatestPublicTimeline();

            }else{

                // fetch 10 timeline in specified term
                $statuses = $this->Status->getPublicTimelineInTerm($term['begin'],$term['end']);
            
            }

            $lastStatus = $this->getLastLine($statuses);
            $oldestTimestamp = $lastStatus['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderPublicTimeline($oldestTimestamp);

            break;

        default:

            break;
            
        }
        
        $this->autoRender = true;
        $this->set('oldest_timestamp',$oldestTimestamp);
        $this->set('hasNext',$hasNext);
        $this->set('statuses',$statuses);       

    }

    public function read_more(){
        
        /**
         * called when read more button is clicked
         * receives Status.id to start retrieving
         * renders html
         */

        $oldestTimestamp = $this->request->data('oldest_timestamp');
        $destinationActionType = $this->request->data('destination_action_type');
        $user = $this->Auth->user();
 
        switch($destinationActionType){

        case 'tweets':
            // fetch older statuses
            $statuses = $this->Status->getOlderStatus($user['id'],$oldestTimestamp);

            // set created_at of last status 
            $lastStatus = $this->getLastLine($statuses);
            $oldestTimestamp = $lastStatus['Status']['created_at'];

            // check if any older status exists
            $hasNext = $this->Status->hasOlderStatus($user['id'],$oldestTimestamp);
           
            break;

        case 'home_timeline':
           
            // fetch older timeline
            $statuses = $this->Status->getOlderTimeline($user['id'],$oldestTimestamp);
            
            // set created_at of last status 
            $lastStatus = $this->getLastLine($statuses);
            
            $oldestTimestamp = $lastStatus['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderTimeline($user['id'],$oldestTimestamp);
            
            break;

        case 'public_timeline':
           
            // fetch older timeline
            $statuses = $this->Status->getOlderPublicTimeline($oldestTimestamp);
            
            // set created_at of last status 
            $lastStatus = $this->getLastLine($statuses);
            
            $oldestTimestamp = $lastStatus['Status']['created_at'];
            
            // check if any older status exists in user's timeline
            $hasNext = $this->Status->hasOlderPublicTimeline($oldestTimestamp);
            
            break;
        }
        
        $this->autoRender = true;
        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldestTimestamp);        
        $this->set('statuses',$statuses);
    
    }

    public function check_status_update(){
        
        /**
         * checks if there is any updatable tweet on twitter.com
         */
        
        $user = $this->Auth->user();
        $userId = $user['id'];
                
        $params = array(
                        'user_id'=>$user['Twitter']['id'],
                        'count'=>1,
                        'include_rts'=>true,
                        );
        
        $latestTweet = $this->Twitter->get('statuses/user_timeline',$params);
        $latestStatus = $this->Status->getLatestStatus($userId,1);
        
        $latestTweetCreatedAt = strtotime($latestTweet[0]['created_at']);
        $latestStatusCreatedAt = $latestStatus[0]['Status']['created_at'];

        // compare created_at
        if($latestTweetCreatedAt > $latestStatusCreatedAt){
        
            // updatable
            $ret = array('result'=>true);
        
        }else{

            // no tweet updatable
            $ret = array('result'=>false);

        }
        
        // record the time updeted
        $this->Status->updateSavedTime($userId);
        $updatedTime = $this->Status->getLastUpdatedTime($userId);
        $updatedDate = $this->convertTimeToDate($updatedTime,$user['Twitter']['utc_offset']);

        $ret['updated_date'] = $updatedDate;
        
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
        $userId = $user['id'];

        // initialization
        $doUpdate = false;

        // fetch the list of user's friends
        $friends['db'] = $this->Friend->getFriendIds($userId);

        // check if user has any friends imported
        if(!$friends['db']){

            // if not,do update
            $doUpdate = true;

        }else{
            
            // fetch same list from twitter
            $followingFriends = $this->Twitter->get('friends/ids');
            $friends['twitter'] = $followingFriends['ids'];

            // compare the number of ids contained in each array
            $countDb = count($friends['db']);
            $countTwitter = count($friends['twitter']);

            if($countDb != $countTwitter){

                // if not equals
                $doUpdate = true;

            }else{
                
                // check if $friends['twitter'] contains any id that is not contained in $friends['db']
                foreach($friends['twitter'] as $myTwitterFriend){

                    $idExists = in_array($myTwitterFriend,$friends['db']);

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
             
                $followingFriends = $this->Twitter->get('friends/ids');
                $friends['twitter'] = $followingFriends['ids'];

            }

            // update friends list
            $this->Friend->updateFriends($userId,$friends['twitter']);
        
        }else{
       
            // just update friends_updeted time
            $this->Friend->updateTime($userId);
        
        }

        // get the total number of friends
        $countFriends = $this->Friend->getFriendNum($userId);
        
        // get the updated time
        $updatedTime = $this->Friend->getLastUpdatedTime($userId);
        $updatedDate = $this->convertTimeToDate($updatedTime,$user['Twitter']['utc_offset']);

        // prepare the array to return
        $ret = array(
                     'updated'=>$doUpdate,
                     'count_friends'=>$countFriends,
                     'updated_date'=>$updatedDate
                     );
        
        echo json_encode($ret);

    }
    
    public function check_profile_update(){
        
        /**
         * checks if there is any difference in user profile between our service and twitter
         */

        if(!$this->request->isPost()){
            echo "bad request";
            exit;
        }

        // initialization
        $updatedValue = array();
        $updated = false;// represents whether any valule was updated or not
        $user = $this->Auth->user();
        $userId = $user['id'];
        $utcOffset = $user['Twitter']['utc_offset'];
        // list of values to be checked 
        $checkList = array('name','screen_name','profile_image_url_https','time_zone','utc_offset','lang');        
        $ret = array();

        // fetch profile on twitter
        $tw = $this->Twitter->get('account/verify_credentials',array('skip_status'=>true));
        
        // fetch profile on db
        $this->User->unbindAllModels();
        $db = $this->User->findById($userId);
        
        // check for each value in list
        foreach($checkList as $valueName){
            $checkable = array_key_exists($valueName,$db['User']) && array_key_exists($valueName,$tw);
            if($checkable){
            
                // compare
                if($tw[$valueName] != $db['User'][$valueName]){
                    
                    // if differ, update the value
                    $this->User->id = $userId;
                    
                    if($this->User->saveField($valueName,$tw[$valueName])){
                    
                        $updated = true;
                        $updatedValue[$valueName] = $tw[$valueName];
                    
                    }
                }
            }
        }

        $this->User->updateTime($userId);
        
        $ret = array(
                     'updated'=>$updated,
                     'updated_date'=>$this->convertTimeToDate($this->User->getLastUpdatedTime($userId),$utcOffset)
                     );
        
        if($updated){

            $ret['updated_value'] = $updatedValue;
       
            // update logging data
            $loginData = $this->Auth->user();

            foreach($updatedValue as $name=>$value){

                $loginData['Twitter'][$name] = $value;

            }

            $this->Session->write('Auth.User',$loginData);

        }

        echo json_encode($ret);
    }

}
