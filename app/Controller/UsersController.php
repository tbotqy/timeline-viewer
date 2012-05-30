<?php
/*
 * Controller/UsersController.php
 */

class UsersController extends AppController{

    /* settings */
    public $helpers = array('Html','Form','Session');
    public $components = array('Auth','Session');
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
            $this->redirect('/users/home');
        }else{
            $this->redirect('/users/login');
        }
    }

    public function login(){

        /*
         * This action just shows the view for login.
         */
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

        $client = $this->createClient();
        $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/users/callback');

        if( $requestToken ){
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

        if( !$accessToken ){
            // if failed in fetching access token
            // show the error message
            $this->Session->setFlash('Failed in connecting to api.twitter.com. Please try again later.');
        }else{

            $verify_credentials = $client->get($accessToken->key,$accessToken->secret,'https://api.twitter.com/1/account/verify_credentials.json');
            $verify_credentials = json_decode($verify_credentials);
            $user = array();//contain the information about the user to get logged in.
            $user['Twitter']['id'] = $verify_credentials->id_str;
            $user['Twitter']['screen_name'] = $verify_credentials->screen_name;
            $user['Twitter']['profile_image_url_https'] = $verify_credentials->profile_image_url_https;

            // check if user is already registered 
            $exist = $this->User->find(
                                       'count',
                                       array(
                                             'conditions'=>array(
                                                                 'twitter_id'=>$verify_credentials->id_str
                                                                 )
                                             )
                                       );

            if( $exist ){

                // check if stored tokens are up-to-date
                $stored_tokens = $this->User->findByTwitterId(
                                                              $verify_credentials->id_str,
                                                              array('User.id','User.token','User.token_secret')
                                                              );

                if( $stored_tokens['User']['token'] != $accessToken->key || $stored_tokens['User']['token_secret'] != $accessToken->secret ){
                    // if not, update them
                    $id = $stored_tokens['User']['id'];
                    $data = array('id' => $id,
                                  'token' => $accessToken->key,
                                  'token_secret' => $accessToken->secret,
                                  'token_updated' => time()
                                  );
                    $this->User->save($data);
                }

                // check if user has aleady imported his status on twitter
                $hasBeenInitialized = $this->User->findByTwitterId($verify_credentials->id_str,
                                                                   array('User.initialized_flag')
                                                                   );

                if( !$hasBeenInitialized['User']['initialized_flag'] ){

                    if( $this->Auth->login($user) ){
                        $this->redirect('/statuses/import');
                    }
                }else{

                    if( $this->Auth->login($user) ){
                        $this->redirect('/users/home');
                    }
                }
            }else{
                // register if user doesn't have his account

                // user's data to save
                $data_to_save = array(
                                      'twitter_id'=>$verify_credentials->id_str,
                                      'name'=>$verify_credentials->name,
                                      'screen_name'=>$verify_credentials->screen_name,
                                      'profile_image_url_https'=>$verify_credentials->profile_image_url_https,
                                      'time_zone'=>$verify_credentials->time_zone,
                                      'utc_offset'=>$verify_credentials->utc_offset,
                                      'created_at'=>(strtotime($verify_credentials->created_at)-SERVER_UTC_OFFSET),
                                      'lang'=>$verify_credentials->lang,
                                      'token'=>$accessToken->key,
                                      'token_secret'=>$accessToken->secret,
                                      'token_updated'=>0,
                                      'initialized_flag'=>0,
                                      'created'=>time()
                                      );
                $this->User->save($data_to_save);

                if( $this->Auth->login($user) ){
                    $this->redirect('/statuses/import');
                }
            }
        }
    }

    public function sent_tweets(){

        /*
         * show the teewts sent by logged-in user
         */

        // load user info 
        $user = $this->Auth->user();
        $twitter_id = $user['Twitter']['id'];
      
        // fetch user's latest 20 statuses
        $statuses = $this->Status->find(
                                        'all',array(
                                                    'conditions'=>array('Status.twitter_id'=>$twitter_id),
                                                    'limit'=>20,
                                                    'order'=>'Status.created_at ASC'
                                                    )
                                        );
        // fetch user's twitter account info
        $user_data = $this->User->find(
                                       'first',array(
                                                     'conditions'=>array('User.twitter_id'=>$twitter_id),
                                                     'fields'=>array(
                                                                     'User.twitter_id',
                                                                     'User.name',
                                                                     'User.screen_name',
                                                                     'User.profile_image_url_https',
                                                                     'User.utc_offset'
                                                                     )
                                                     )
                                       );

        // fetch list of all the statuses
        $status_date_list = $this->Status->find(
                                                'list',array(
                                                             'conditions'=>array(
                                                                                 'Status.twitter_id'=>$twitter_id
                                                                                 ),
                                                             'fields'=>array(
                                                                             'Status.created_at'
                                                                             ),
                                                             'order'=>'Status.created_at DESC'
                                                             )
                                                );
        // cllasify them by month
        $utc_offset = $user_data['User']['utc_offset'];
        $count = 0;
        foreach($status_date_list as $key=>$created_at){
            $created_at += $utc_offset;

            $year = date('Y',$created_at);
            $month = date('n',$created_at);
            $day = date('j',$created_at);

            $sum_by_year[$year] = isset($sum_by_year[$year]) ? $sum_by_year[$year]+1 : 1;
            $sum_by_month[$year][$month] = isset($sum_by_month[$year][$month]) ? $sum_by_month[$year][$month]+1 : 1;
            $sum_by_day[$year][$month][$day] = isset($sum_by_day[$year][$month][$day]) ? $sum_by_day[$year][$month][$day]+1 : 1;
 
        }
        //pr($sum_by_year); 
        //pr($sum_by_month);
        //pr($sum_by_day);
        
        //        pr($sum_by_day);
         $this->set('user_data',$user_data); 
         $this->set('statuses',$statuses);
      
         $this->set('sum_by_year',$sum_by_year);
         $this->set('sum_by_month',$sum_by_month);
         $this->set('sum_by_day',$sum_by_day);
       
    }
}