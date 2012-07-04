s<?php
/*
 * Controller/UsersController.php
 */

class UsersController extends AppController{

    public $layout = 'common';
    public $uses = array('User','Status','Entity','Friend');
    public $components = array('Twitter','Url');

    public function beforeFilter(){
        $this->Auth->allow('index','login','authorize','callback','logout','hoge');
        parent::beforeFilter();
    }

    public function test(){

        $this->autoRender = false;
        
        $this->User->deleteAccount(2);
        
            
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
         */

        $client = $this->Twitter->createClient();
        $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/users/callback');

        if($requestToken){
            
            // redirect to api.twitter.com
            $this->Session->write('twitter_request_token', $requestToken);
            /* test mode */ $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
            //$this->redirect('https://api.twitter.com/oauth/authenticate?oauth_token=' . $requestToken->key);
        }else{
            $this->Session->setFlash('failed in connecting to twitter. Please try again later.');
        }
    }

    public function callback(){

        /**
         * callback action for Twitter OAuth.
         */
        
        // aqcuire request token from session
        $requestToken = $this->Session->read('twitter_request_token');
        $client = $this->createClient();
        
        // fetch access token for this user
        $accessToken = $client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);
        
        if(!$accessToken){

            // if failed in fetching access token,show the error message
            $this->Session->setFlash('Failed in connecting to api.twitter.com. Please try again later.');
            return ;
        }

        $tokens['token'] = $accessToken->key;
        $tokens['token_secret'] = $accessToken->secret;

        $verify_credentials = $this->Twitter->get('account/verify_credentials',array(),$tokens);
        $verify_credentials = json_decode($verify_credentials,true);
        
        //
        // check if user with authorized twitter id exists 
        //

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
            
            // register if user doesn't have account
            $this->User->register($tokens,$verify_credentials);
        }

        // 
        // execute login and redirect user
        // 

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
        }
    }

    
    public function sent_tweets(){
        
        /**
         * shows the teewts sent by logged-in user
         */

        // load user info 
        $user = $this->Auth->user();

        // fetch user's twitter account info
        $user_data = $this->User->findById($user['id']);

        // check if requested uri includes query string
        $term = isset($this->params['pass']['0']) ? $this->params['pass']['0'] : false;
        
        if($term){
          
            // check the type of given term
            $date_type = $this->Url->getParamType($term);

            // load user's utc offset
            $utc_offset = $user_data['User']['utc_offset'];
            
            // convert given term from string to unixtime
            $term = $this->termToTime($term,$date_type,$utc_offset);
            
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

        // load user's account info
        $user = $this->Auth->user();
        
        // check if requested uri includes query string
        $term = isset($this->params['pass']['0']) ? $this->params['pass']['0'] : false;
        
        if($term){
          
            // check the type of given term
            $date_type = $this->Url->getParamType($term);

            // fetch user's twitter account info
            $user_data = $this->User->findById($user['id']);
        
            // load user's utc offset
            $utc_offset = $user_data['User']['utc_offset'];
            
            // convert given term from string to unixtime
            $term = $this->termToTime($term,$date_type,$utc_offset);
            
            // fetch statuses in specified term
            $statuses = $this->Status->getTimelineInTerm($user['id'],$term['begin'],$term['end']);
            
        }else{

            // fetch statuses filtering with each twitter ids in $following_list
            $statuses = $this->Status->getLatestTimeline($user['id']);

        }
        
        // get oldest status's created_at timestamp
        $last_status = $this->getLastLine($statuses);
        $oldest_timestamp = $last_status['Status']['created_at'];
   
        $hasNext = $this->Status->hasOlderTimeline($user['id'],$oldest_timestamp);
          
        $date_list = $this->Status->getDateList($user['id'],'home_timeline');
                  
        $this->set('statuses',$statuses);
        $this->set('date_list',$date_list);
        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldest_timestamp);
    }

    public function configurations(){
        /**
         * offers view for user configurations
         */
        $user = $this->Auth->user();

        $this->set('user',$user);
    }

}