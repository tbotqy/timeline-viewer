<?php

/**
 * Controller/UsersController.php
 */

class UsersController extends AppController{

    public $layout = 'common';
    
    public $components = array('Parameter');

    public function beforeFilter(){

        parent::beforeFilter();
        
        $this->Auth->allow('index','login','authorize','callback','logout','we_are_sorry_but');

    }

    public function index(){

        /**
         * checks if user is logged in.
         */
        
        if($this->Auth->loggedIn()){
    
            $this->redirect('/users/sent_tweets');
        
        }else{
        
            $this->redirect('/users/login');
        
        }

    }

    public function login(){

        /**
         * just shows the view for login.
         */
        
        if($this->Auth->loggedIn()){
        
            $this->redirect('/users/index');
        
        }
        
    }

    public function logout(){
        
        /**
         * log the user out
         */

        if($this->Auth->logout()){
        
            $this->redirect('/users/');
       
        }
    }

    public function authorize(){

        /**
         * redirect user to api.twitter.com to make Twitter OAuth process.
         * Aquire request token -> redirect user to authentication screen on twitter
         */
        
        // get request token 
        $client = $this->Twitter->createClient();
        $requestToken = $client->getRequestToken
            (
             'https://api.twitter.com/oauth/request_token',
             'http://' . $_SERVER['HTTP_HOST'] . '/users/callback'
             );
        
        // check if request token was successfully acquired
        if($requestToken){
            
            // redirect to api.twitter.com
            $this->Session->write
                (
                 'twitter_request_token',
                 $requestToken
                 );
            
            /* test mode */ $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
            //$this->redirect('https://api.twitter.com/oauth/authenticate?oauth_token=' . $requestToken->key);
        
        }else{
            // if failed in acquiring request token, tell the user that something is wrong with twitter.com
            $this->Session->setFlash('failed in connecting to twitter. Please try again later.');
        }
    }

    public function callback(){

        /**
         * callback action for Twitter OAuth.
         */
        
        // this action presents nothing
        $this->autoRender = false;
        
        // aqcuire request token from session
        $requestToken = $this->Session->read('twitter_request_token');
        $client = $this->createClient();
        
        // fetch access token for this user
        $accessToken = $client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);
        
        // check if access token was successfully acquired
        if(!$accessToken){

            // if failed in fetching access token,show the error message
            $this->Session->setFlash('Failed in connecting to api.twitter.com. Please try again later.');
            return ;
        }
        
        // fetch user's twitter account information
        $tokens['token'] = $accessToken->key;
        $tokens['token_secret'] = $accessToken->secret;

        $verify_credentials = $this->Twitter->get('account/verify_credentials',array(),$tokens);
        $verify_credentials = json_decode($verify_credentials,true);
        
        /////////////////////////////////////////////////////////////////
        // check if user with authorized twitter id exists in database //
        /////////////////////////////////////////////////////////////////

        if($this->User->existByTwitterId($verify_credentials['id_str'])){

            // get user id
            $user_id = $this->User->getIdByTwitterId($verify_credentials['id_str']);
            
            // check if stored tokens are up-to-date by comparing with acquired tokens
            $stored_tokens = $this->User->getTokens($user_id);
            if( $stored_tokens['User']['token'] != $tokens['token'] || $stored_tokens['User']['token_secret'] != $tokens['token_secret'] ){
                // if not, update them
                $this->User->updateTokens($user_id,$tokens);
            }

        }else{
            
            // check if user's twitter profile is protected
            $protected = $verify_credentials['protected'];
            if($protected){
                $this->Session->write('redirected',true);
                return $this->redirect('/users/we_are_sorry_but');
            }else{
                // register if user hasn't registered yet
                $this->User->register($tokens,$verify_credentials);
            }
        }

        ///////////////////////////////////// 
        // execute login and redirect user //
        /////////////////////////////////////

        // prepare user data to get logged in
        $user['id'] = $this->User->getIdByTwitterId($verify_credentials['id_str']);
        $user['Twitter']['id'] = $verify_credentials['id_str'];
        $user['Twitter']['screen_name'] = $verify_credentials['screen_name'];
        $user['Twitter']['profile_image_url_https'] = $verify_credentials['profile_image_url_https'];
        $user['Twitter']['utc_offset'] = $verify_credentials['utc_offset'];
        
        // log the user in
        if($this->Auth->login($user)){
          
            $user_id = $this->Auth->user('id');
                    
            // check if user has aleady imported his status on twitter
            $hasBeenInitialized = $this->User->isInitialized($user_id);
            
            if(!$hasBeenInitialized){
        
                $this->redirect('/statuses/import');
            
            }else{
                
                $this->redirect('/users/sent_tweets');
            
            }
        }else{

            echo "could not login";

        }
    }

    public function we_are_sorry_but(){
        
        // aporogize user for our system policy if user was redirected
        if($this->Session->read('redirected')){
        
            $this->Session->delete('redirected');
        
        }else{
        
            return $this->redirect('/');
        
        }
        
    }

    public function sent_tweets(){
        
        /**
         * shows the teewts sent by logged-in user
         */

        $this->rejectUnInitialized();

        // initialization
        $user_data = array(); 
        $statuses = array();
        $oldest_timestamp = "";      
        $date_list = array();
        $hasNext = false;
        
        // load user info 
        $user = $this->Auth->user();

        // fetch user's twitter account info
        $user_data = $this->User->findById($user['id']);

        // check if requested uri includes query string
        $term = isset($this->params['pass']['0']) ? $this->params['pass']['0'] : false;
        
        if($term){
          
            // check the type of given term
            $date_type = $this->Parameter->getParamType($term);

            // load user's utc offset
            $utc_offset = $user_data['User']['utc_offset'];
            
            // convert given term from string to unixtime
            $term = $this->Parameter->termToTime($term,$date_type,$utc_offset);
            
            // fetch statuses in specified term
            $statuses = $this->Status->getStatusInTerm($this->Auth->user('id'),$term['begin'],$term['end'],'DESC',$limit = '10');
            
        }else{

            // fetch user's latest 10 statuses
            $statuses = $this->Status->getLatestStatus($user['id']);

        }
                
        // get primary key of last status in fetched array
        $num = count($statuses)-1;
        $oldest_timestamp = $statuses[$num]['Status']['created_at'];
        $hasNext = $this->Status->hasOlderStatus($user['id'],$oldest_timestamp);

        // create the list of all the statuses user has.           
        $date_list = $this->Status->getDateList($user['id']);
        
        $this->set('user_data',$user_data); 
        $this->set('statuses',$statuses);
        $this->set('oldest_timestamp',$oldest_timestamp);      
        $this->set('date_list',$date_list);
        $this->set('hasNext',$hasNext);

    }

    public function home_timeline(){

        /**
         * shows the home timeline 
         * acquire user's hometimeline via API 
         */
        
        $this->rejectUnInitialized();

        // initialization
        $statuses = array();
        $date_list = array();
        $hasNext = false;
        $oldest_timestamp = "";
        $error_type = "";

        // load user's account info
        $user = $this->Auth->user();

        // check if there is any friend
        $hasFriendList = $this->User->hasFriendList($user['id']);
        $hasRegisteredFriend = $this->User->hasRegisteredFriend($user['id']);
        if($hasFriendList && $hasRegisteredFriend){
 
            // check if requested uri includes query string
            $term = isset($this->params['pass']['0']) ? $this->params['pass']['0'] : false;
        
            if($term){
          
                // check the type of given term
                $date_type = $this->Parameter->getParamType($term);

                // fetch user's twitter account info
                $user_data = $this->User->findById($user['id']);
        
                // load user's utc offset
                $utc_offset = $user_data['User']['utc_offset'];
            
                // convert given term from string to unixtime
                $term = $this->Parameter->termToTime($term,$date_type,$utc_offset);
            
                // fetch statuses in specified term
                $statuses = $this->Status->getTimelineInTerm($user['id'],$term['begin'],$term['end']);
            
            }else{

                // fetch statuses filtering with each twitter ids in $following_list
                $statuses = $this->Status->getLatestTimeline($user['id']);

            }

            if($statuses){

                // get oldest status's created_at timestamp
                $last_status = $this->getLastLine($statuses);
                $oldest_timestamp = $last_status['Status']['created_at'];
   
                $hasNext = $this->Status->hasOlderTimeline($user['id'],$oldest_timestamp);
          
                $date_list = $this->Status->getDateList($user['id'],'home_timeline');

            }

        }else{
         
            if(!$hasFriendList){

                $error_type = "noFriendList";

            }elseif(!$hasRegisteredFriend){

                $error_type = "noRegisteredFriend";

            }

        }

        $this->set('error_type',$error_type);
        $this->set('statuses',$statuses);
        $this->set('date_list',$date_list);
        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldest_timestamp);
        
    }

    public function public_timeline(){
        
        /**
         * shows the public timeline, the line of tweets presented by all the users registered 
         */
        
        $this->rejectUninitialized();

        // initialization
        $statuses = array();
        $date_list = array();
        $oldest_timestamp = "";
        $hasNext = false;
        $noStatusAtAll = false;

        $user_id = $this->Auth->user('id');

        // check if requested uri includes query string
        $term = isset($this->params['pass']['0']) ? $this->params['pass']['0'] : false;
        
        if($term){

            // fetch statuses whose created_at is between $term
                    
            // check the type of given term
            $date_type = $this->Parameter->getParamType($term);
            
            // fetch user's twitter account info
            $user_data = $this->User->findById($user_id);
        
            // load user's utc offset
            $utc_offset = $user_data['User']['utc_offset'];
            
            // convert given term from string to unixtime
            $term = $this->Parameter->termToTime($term,$date_type,$utc_offset);
            
            // fetch statuses in specified term
            $statuses = $this->Status->getPublicTimelineInTerm($term['begin'],$term['end']);

        }else{

            // fetch tweets
            $statuses = $this->Status->getLatestPublicTimeline();

        }

        if($statuses){

            // get oldest status's created_at timestamp
            $last_status = $this->getLastLine($statuses);
            $oldest_timestamp = $last_status['Status']['created_at'];
            
            // check if there are more public statuses
            $hasNext = $this->Status->hasOlderPublicTimeline($oldest_timestamp);
            
        }else{
            
            $noStatusAtAll = true;
       
        }

        // get date list
        $date_list = $this->Status->getDateList($user_id,'public_timeline');
        
        $this->set('statuses',$statuses);
        $this->set('date_list',$date_list);
        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldest_timestamp);
        
    }

    public function configurations(){
        
        /**
         * offers view for user configurations
         */
       
        $this->rejectUnInitialized();

        $user = $this->Auth->user();
        $user_id = $user['id'];
        $utc_offset = $user['Twitter']['utc_offset'];

        $count_statuses = $this->Status->getStatusNum($user_id);
        $count_friends = $this->Friend->getFriendNum($user_id);

        $status_updated_time = date("Y-m-d　H:i:s",$this->Status->getLastUpdatedTime($user_id)+$utc_offset);
        $friend_updated_time = date("Y-m-d　H:i:s",$this->Friend->getLastUpdatedTime($user_id)+$utc_offset);
 
        $this->set('count_statuses',$count_statuses);
        $this->set('count_friends',$count_friends);
        $this->set('status_updated_time',$status_updated_time);
        $this->set('friend_updated_time',$friend_updated_time);
        $this->set('user',$user);
    }

    private function rejectUnInitialized(){

        /**
         * rediect the user whose statuses are not initialized to import screen
         */
        
        if(!$this->userIsInitialized){
        
            return $this->redirect('/statuses/import');
        
        }

    }

}