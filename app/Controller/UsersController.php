<?php
/*
 * Controller/UsersController.php
 */

class UsersController extends AppController{

    public $layout = 'common';
    public $uses = array('User','Status','Entity');
      
    public function beforeFilter(){
        $this->Auth->allow('index','login','authorize','callback','logout');
        parent::beforeFilter();
    }
    
    public function index(){

        /*
         * This action checks if user is logged in.
         */
        
        if($this->Auth->loggedIn()){
            $this->redirect('/users/sent_tweets');
        }else{
            $this->redirect('/users/login');
        }
    }

    public function login(){

        /*
         * This action just shows the view for login.
         */
        
        if($this->Auth->loggedIn()){
            $this->redirect('/users/index');
        }
        
    }

    public function logout(){
        
        // log the user out
        if($this->Auth->logout()){
            $this->redirect('/users/');
        }
    }

    public function authorize(){

        /*
         * This action takes Twitter OAuth process.
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

        /*
         * This is the callback action for Twitter OAuth.
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

            if( $stored_tokens['User']['token'] != $accessToken->key || $stored_tokens['User']['token_secret'] != $accessToken->secret ){

                // if not, update them
                $data = array(
                              'id' =>  $user_id,
                              'token' => $accessToken->key,
                              'token_secret' => $accessToken->secret,
                              'token_updated' => time(),
                              'updated' => time()
                              );
                
                $this->User->save($data);
            }
                      
        }else{
            // register if user doesn't have account

            // user's data to save
            $data_to_save = array(
                                  'twitter_id'=>$verify_credentials['id_str'],
                                  'name'=>$verify_credentials['name'],
                                  'screen_name'=>$verify_credentials['screen_name'],
                                  'profile_image_url_https'=>$verify_credentials['profile_image_url_https'],
                                  'time_zone'=>$verify_credentials['time_zone'],
                                  'utc_offset'=>$verify_credentials['utc_offset'],
                                  'created_at'=>strtotime($verify_credentials['created_at']),
                                  'lang'=>$verify_credentials['lang'],
                                  'token'=>$accessToken->key,
                                  'token_secret'=>$accessToken->secret,
                                  'token_updated'=>0,
                                  'initialized_flag'=>0,
                                  'created'=>time()
                                  );
            $this->User->save($data_to_save);
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
        /*
         * show the teewts sent by logged-in user
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
            $statuses = $this->Status->getStatusInTerm($user_id,$begin,$end,$order,$limit);
            
        }else{

            // fetch user's latest 10 statuses
            $statuses = $this->Status->getLatestStatus($user['id']);
        }
        
        // fetch entities and add them to $statuses
        $tmp_statuses = $statuses;
        $itr = 0;
        
        foreach($statuses as $status){
            $status_id = $status['Status']['status_id_str'];
            $entities = $this->Entity->find(
                                            'all',
                                            array(
                                                  'conditions'=>array('Entity.status_id_str'=>$status_id)
                                                  )
                                            );
            
            $tmp_statuses[$itr]['Status']['entities'] = $entities;
            $itr++;
        }

        $statuses = $tmp_statuses;
        
        // get primary key of last status in fetched array
        $num = count($statuses)-1;
        $last_status_id = $statuses[$num]['Status']['id'];

        //                                                             //
        // create the list of all the statuses user has.               //
        // the data created here is shown in the left area of the view //
        //                                                             //
        
        // fetch list of all the statuses
        $status_date_list = $this->Status->find(
                                                'list',
                                                array(
                                                      'conditions'=>array(
                                                                          'Status.twitter_id'=>$twitter_id
                                                                          ),
                                                      'fields'=>array(
                                                                      'Status.created_at'
                                                                      ),
                                                      'order'=>'Status.created_at DESC'
                                                      )
                                                );

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
        
        $this->set('user_data',$user_data); 
        $this->set('statuses',$statuses);
      
        //$this->set('sum_by_year',$sum_by_year);
        //$this->set('sum_by_month',$sum_by_month);
        $this->set('date_list',$sum_by_day);
        $this->set('last_status_id',$last_status_id);
    }

    public function home_timeline(){
        /*
         * shows the home timeline 
         * acquire user's hometimeline via API 
         */
        
        // load user's account info
        $user = $this->Auth->user();
        $twitter_id = $user['Twitter']['id'];

        // initialize Twitter component class
        $this->Twitter->initialize($this);
        
        //
        // fetch as much tweets as API can retrieve
        //
        
        $options = array('count'=>200,'include_rts'=>true);
        $statuses = $this->Twitter->get('statuses/home_timeline',$options);
        
        pr(json_decode($statuses,true));
    }
}