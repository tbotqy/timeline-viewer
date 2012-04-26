<?php
/*
 * Controller/UsersController.php
 */

class UsersController extends AppController{

    /* settings */
    public $helpers = array('Html','Form','Session');
    public $components = array('Auth','Session');
    public $layout = 'common';

    public function beforeFIlter(){
        $this->Auth->allow('index','login','authorize','callback','logout');
    }
    
    public function index(){
        /*
         * This action checks if user is logged in.
         */
        
        if( $this->Auth->loggedIn() ){
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

    public function authorize(){
        /*
         * This action takes Twitter OAuth process.
         */

        $client = $this->createClient();
        $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/users/callback');

        if( $requestToken ){
            //redirect to api.twitter.com
            $this->Session->write('twitter_request_token', $requestToken);
            $this->redirect('https://api.twitter.com/oauth/authenticate?oauth_token=' . $requestToken->key);
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
            //if failed in fetching access token
            //show the error message
            $this->Session->setFlash('Failed in connecting to api.twitter.com. Please try again later.');
        }else{
            //
            $verify_credentials = $client->get($accessToken->key,$accessToken->secret,'https://api.twitter.com/1/account/verify_credentials.json');
            $verify_credentials = json_decode($verify_credentials);
            $user = array();//contain the information about the user to get logged in.
            $user['Twitter']['id'] = $verify_credentials->id_str;
            $user['Twitter']['screen_name'] = $verify_credentials->screen_name;
            
            // check if user is already registored 
            $exist = $this->User->find('count',array('conditions'=>array('twitter_id'=>$verify_credentials->id_str)));
            
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
                
                //user's data to save
                $data_to_save = array(
                                      'twitter_id'=>$verify_credentials->id_str,
                                      'screen_name'=>$verify_credentials->screen_name,
                                      'token'=>$accessToken->key,
                                      'token_secret'=>$accessToken->secret,
                                      'created'=>time()
                                      );
                $this->User->save($data_to_save);
                
                if( $this->Auth->login($user) ){
                    $this->redirect('/users/import');
                }
            }
        }
    }

    public function home(){
        // [ToDo] show the loaded user's data in the view named home
    }

    public function logout(){
        // log the user out
        if($this->Auth->logout()){
            $this->redirect('/users/');
        }
    }
}