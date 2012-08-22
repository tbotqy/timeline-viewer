<?php

/**
 * Controller/UsersController.php
 */

class UsersController extends AppController{

    public $layout = 'common';
    
    public $components = array('Parameter');

    public function beforeFilter(){

        parent::beforeFilter();
        
        $this->Auth->allow('index','login','authorize','callback','logout','we_are_sorry_but','under_construction','browser','test');
        
    }

    public function index(){

        /**
         * checks if user is logged in.
         */
        
        if($this->Auth->loggedIn()){
    
            $this->redirect('/your/home_timeline');
        
        }else{
            $this->set('showFooter',true);         
            $this->render('login');
        }
    }

    public function login(){

        /**
         * just shows the view for login.
         */

        $this->set('showFooter',true);
        
        if($this->Auth->loggedIn()){
        
            $this->redirect('/');
        
        }
        
    }

    public function logout(){
        
        /**
         * log the user out
         */

        if($this->Auth->logout()){
        
            $this->redirect('/');
       
        }
    }

    public function authorize(){

        /**
         * redirect user to api.twitter.com to make Twitter OAuth process.
         * Aquire request token and redirect user to authentication screen on twitter
         */

        // get request token 
        $client = $this->Twitter->createClient();
        
        $requestToken = $client->getRequestToken
            (
             'https://api.twitter.com/oauth/request_token',
             'http://' . env('HTTP_HOST') . '/twitter/callback'
             );

        // check if request token was successfully acquired
        if($requestToken){
            
            // redirect to api.twitter.com
            $this->Session->write
                (
                 'twitter_request_token',
                 $requestToken
                 );
            
            $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
            
        }else{
            
            // if failed in acquiring request token
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

        $verifyCredentials = $this->Twitter->get('account/verify_credentials',array(),$tokens);
        $verifyCredentials = json_decode($verifyCredentials,true);
        
        /////////////////////////////////////////////////////////////////
        // check if user with authorized twitter id exists in database //
        /////////////////////////////////////////////////////////////////

        if($this->User->existByTwitterId($verifyCredentials['id_str'])){

            // get user id
            $userId = $this->User->getIdByTwitterId($verifyCredentials['id_str']);
            
            // check if stored tokens are up-to-date by comparing with acquired tokens
            $storedTokens = $this->User->getTokens($userId);
            if( $storedTokens['User']['token'] != $tokens['token'] || $storedTokens['User']['token_secret'] != $tokens['token_secret'] ){
                // if not, update them
                $this->User->updateTokens($userId,$tokens);
            }

        }else{
            
            // check if user's twitter profile is protected
            $protected = $verifyCredentials['protected'];
            if($protected){
                $this->Session->write('redirected',true);
                return $this->redirect('/we_are_sorry_but');
            }else{
                // register if user hasn't registered yet
                $this->User->register($tokens,$verifyCredentials);
            }
        }

        ///////////////////////////////////// 
        // execute login and redirect user //
        /////////////////////////////////////

        // prepare user data to get logged in
        $userDataForLogin = array();

        $userId = $this->User->getIdByTwitterId($verifyCredentials['id_str']);
        $this->User->unbindAllModels();
        $registoredUserData = $this->User->findById($userId);
        
        $userDataForLogin = array(
                                     'id'=>$userId,
                                     'Twitter'=>array('id'=>$registoredUserData['User']['twitter_id'])
                                     );

        $loginValueList = array(
                                  'name',
                                  'screen_name',
                                  'profile_image_url_https',
                                  'time_zone',
                                  'utc_offset',
                                  'lang'
                                  );
        
        foreach($loginValueList as $val){
            $userDataForLogin['Twitter'][$val] = $registoredUserData['User'][$val];
        }

        // log the user in
        if($this->Auth->login($userDataForLogin)){
          
            $userId = $this->Auth->user('id');
                    
            // check if user has aleady imported his status on twitter
            $hasBeenInitialized = $this->User->isInitialized($userId);
            
            if(!$hasBeenInitialized){
        
                $this->redirect('/statuses/import');
            
            }else{
                
                $this->redirect('/your/home_timeline');
            
            }
        }else{

            echo "could not login";

        }
    }

    public function we_are_sorry_but(){
        
        // aporogize user for our system policy if user was redirected
        if($this->Session->read('redirected')){
        
            $this->Session->delete('redirected');
            $this->set('showFooter',true);
        }else{
        
            return $this->redirect('/');
        
        }
        
    }

    public function under_construction(){

        if(!Configure::read('underConstruction')){
            $this->redirect('/');
        }

        $this->set('title_for_layout','Timedline | メンテナンス中です');
        
        // tell that site is under construction
        $this->set('showFooter',true);

    }

    public function browser(){

        // tell that user's browser is out of support

        if($this->browserOk){
            $this->redirect('/');
        }

        $this->set('title_for_layout','Timedline | 対応ブラウザについて');
        
        $this->set('showFooter',true);

    }

    public function sent_tweets(){
        
        /**
         * shows the teewts sent by logged-in user
         */

        $this->rejectUnInitialized();

        $this->set('title_for_layout','Timedline | あなたのツイート');

        // initialization
        $userData = array(); 
        $statuses = array();
        $oldestTimestamp = "";      
        $dateList = array();
        $hasNext = false;
        
        // load user info 
        $user = $this->Auth->user();

        // fetch user's twitter account info
        $userData = $this->User->findById($user['id']);

        // check if requested uri includes query string
        $term = isset($this->params['pass']['0']) ? $this->params['pass']['0'] : false;
        
        if($term){
          
            // check the type of given term
            $dateType = $this->Parameter->getParamType($term);

            // load user's utc offset
            $utcOffset = $userData['User']['utc_offset'];
            
            // convert given term from string to unixtime
            $term = $this->Parameter->termToTime($term,$dateType,$utcOffset);
            
            // fetch statuses in specified term
            $statuses = $this->Status->getStatusInTerm($this->Auth->user('id'),$term['begin'],$term['end'],'DESC',$limit = '10');
            
        }else{

            // fetch user's latest 10 statuses
            $statuses = $this->Status->getLatestStatus($user['id']);

        }
                
        // get primary key of last status in fetched array
        $num = count($statuses)-1;
        $oldestTimestamp = $statuses[$num]['Status']['created_at'];
        $hasNext = $this->Status->hasOlderStatus($user['id'],$oldestTimestamp);

        // create the list of all the statuses user has.           
        $dateList = $this->Status->getDateList($user['id']);
        
        if(!$statuses){
            $this->set('showFooter',true);
        }
        
        $this->set('user_data',$userData); 
        $this->set('statuses',$statuses);
        $this->set('oldest_timestamp',$oldestTimestamp);      
        $this->set('date_list',$dateList);
        $this->set('hasNext',$hasNext);
    }
    
    public function home_timeline(){

        /**
         * shows the home timeline 
         * acquire user's hometimeline via API 
         */
        
        $this->rejectUnInitialized();

        $this->set('title_for_layout','Timedline | ホームタイムライン');

        // initialization
        $statuses = array();
        $dateList = array();
        $hasNext = false;
        $oldestTimestamp = "";
        $errorType = "";

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
                $dateType = $this->Parameter->getParamType($term);

                // fetch user's twitter account info
                $userData = $this->User->findById($user['id']);
        
                // load user's utc offset
                $utcOffset = $userData['User']['utc_offset'];
            
                // convert given term from string to unixtime
                $term = $this->Parameter->termToTime($term,$dateType,$utcOffset);
            
                // fetch statuses in specified term
                $statuses = $this->Status->getTimelineInTerm($user['id'],$term['begin'],$term['end']);
            
            }else{

                // fetch statuses filtering with each twitter ids in $following_list
                $statuses = $this->Status->getLatestTimeline($user['id']);

            }

            if($statuses){

                // get oldest status's created_at timestamp
                $lastStatus = $this->getLastLine($statuses);
                $oldestTimestamp = $lastStatus['Status']['created_at'];
   
                $hasNext = $this->Status->hasOlderTimeline($user['id'],$oldestTimestamp);
          
                $dateList = $this->Status->getDateList($user['id'],'home_timeline');
                
            }

        }else{
         
            if(!$hasFriendList){

                $errorType = "noFriendList";

            }elseif(!$hasRegisteredFriend){

                $errorType = "noRegisteredFriend";

            }

            $this->set('showFooter',true);

        }

        $this->set('error_type',$errorType);
        $this->set('statuses',$statuses);
        $this->set('date_list',$dateList);
        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldestTimestamp);
        
    }

    public function public_timeline(){
        
        /**
         * shows the public timeline, the line of tweets presented by all the users registered 
         */
        
        $this->rejectUninitialized();

        $this->set('title_for_layout','Timedline | パブリックタイムライン');

        // initialization
        $statuses = array();
        $dateList = array();
        $oldestTimestamp = "";
        $hasNext = false;
        $noStatusAtAll = false;

        $userId = $this->Auth->user('id');

        // check if requested uri includes query string
        $term = isset($this->params['pass']['0']) ? $this->params['pass']['0'] : false;
        
        if($term){

            // fetch statuses whose created_at is between $term
                    
            // check the type of given term
            $dateType = $this->Parameter->getParamType($term);
            
            // fetch user's twitter account info
            $userData = $this->User->findById($userId);
        
            // load user's utc offset
            $utcOffset = $userData['User']['utc_offset'];
            
            // convert given term from string to unixtime
            $term = $this->Parameter->termToTime($term,$dateType,$utcOffset);
            
            // fetch statuses in specified term
            $statuses = $this->Status->getPublicTimelineInTerm($term['begin'],$term['end']);

        }else{

            // fetch tweets
            $statuses = $this->Status->getLatestPublicTimeline();

        }

        if($statuses){

            // get oldest status's created_at timestamp
            $lastStatus = $this->getLastLine($statuses);
            $oldestTimestamp = $lastStatus['Status']['created_at'];
            
            // check if there are more public statuses
            $hasNext = $this->Status->hasOlderPublicTimeline($oldestTimestamp);
            
        }else{
            
            $noStatusAtAll = true;
       
            $this->set('showFooter',true);
        }

        // get date list
        $dateList = $this->Status->getDateList($userId,'public_timeline');
        
        $this->set('statuses',$statuses);
        $this->set('date_list',$dateList);
        $this->set('hasNext',$hasNext);
        $this->set('oldest_timestamp',$oldestTimestamp);
        
    }

    public function configurations(){
        
        /**
         * offers view for user configurations
         */
       
        $this->rejectUnInitialized();

        $this->set('title_for_layout','Timedline | データ管理');

        $user = $this->Auth->user();
        $userId = $user['id'];
        $utcOffset = $user['Twitter']['utc_offset'];
        
        $countStatuses = $this->Status->getStatusNum($userId);
        $countFriends = $this->Friend->getFriendNum($userId);
        $dateFormat = "Y/m/d - H:i:s";

        $statusUpdatedTime = $this->convertTimeToDate($this->Status->getLastUpdatedTime($userId),$utcOffset);
        $friendUpdatedTime = $this->convertTimeToDate($this->Friend->getLastUpdatedTime($userId),$utcOffset);
 
        $profileUpdatedTime = $this->convertTimeToDate($this->User->getLastUpdatedTime($userId),$utcOffset);

        $this->set('count_statuses',$countStatuses);
        $this->set('count_friends',$countFriends);
        $this->set('status_updated_time',$statusUpdatedTime);
        $this->set('friend_updated_time',$friendUpdatedTime);
        $this->set('profile_updated_time',$profileUpdatedTime);
        $this->set('user',$user);
        $this->set('showFooter',true);
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