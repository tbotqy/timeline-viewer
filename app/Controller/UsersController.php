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
         * ユーザーがログインしているかチェックするアクション
         */
        
        if($this->Auth->loggedIn()){
            //ログインしている場合、ホームへリダイレクト
            $this->redirect('/users/home');
        }else{
            //ログインしていない場合、ログイン画面へリダイレクト
            $this->redirect('/users/login');
        }
    }

    public function login(){
        /*
         * ログイン画面を表示するアクション
         */
    }

    public function authorize(){
        /*
         * ツイッターのOAuth認証をするアクション
         */
        
        $client = $this->createClient();
        $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/users/callback');
        if($requestToken){
            //redirect to api.twitter.com
            $this->Session->write('twitter_request_token', $requestToken);
            $this->redirect('https://api.twitter.com/oauth/authenticate?oauth_token=' . $requestToken->key);
        }else{
            $this->Session->setFlash('failed in connecting to twitter. Please try again later.');
        }
    }

    public function callback(){
        /*
         * ツイッターOAuth認証のコールバックアクション
         */

        //リクエストトークンを使ってアクセストークンを取得する
        $requestToken = $this->Session->read('twitter_request_token');
        $client = $this->createClient();
        $accessToken = $client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);
  
        if($accessToken){
            //ユーザーのアカウント情報を取得
            $user_information = $client->get($accessToken->key,$accessToken->secret,'https://api.twitter.com/1/account/verify_credentials.json');
            $user_information = json_decode($user_information);
            $user = array();//ログインさせるユーザーのアカウント情報を格納する配列
            $user['Twitter']['id'] = $user_information->id_str;
            $user['Twitter']['screen_name'] = $user_information->screen_name;
            
            //Twitterのidがデータベースにあるか調べる。ない場合、Statuses/importへリダイレクト
            $exist = $this->User->find('count',array('conditions'=>array('twitter_id'=>$user_information->id_str)));
            if($exist){
                //データベースに保存してあるアクセストークンが最新か調べる。
                $this->User->findByTwitterId($user);
                //ツイートのインポートが済んでいるか調べる。
                $hasBeenInitialized = $this->User->find('first',array('conditions'=>array('initialized_flag')));
                
                if(!$hasBeenInitialized){
                    //済んでいない場合、ログイン処理後、Statuses/importへリダイレクト
                    if($this->Auth->login($user)){
                        $this->redirect('/statuses/import');
                    }
                }else{
                    //済んでいる場合、ログイン処理後homeへリダイレクト
                    if($this->Auth->login($user)){
                        $this->redirect('/users/home');
                    }
                }
            }else{
                //Twiiterのidがデータベースにない場合、登録した後に/statuses/importへリダイレクト
                //登録するデータセット
                $data_to_save = array(
                                      'twitter_id'=>$user_information->id_str,
                                      'screen_name'=>$user_information->screen_name,
                                      'token'=>$accessToken->key,
                                      'token_secret'=>$accessToken->secret,
                                      'created'=>time()
                                      );
                $this->User->save($data_to_save);
                
                if($this->Auth->login($user)){
                    $this->redirect('/users/import');
                }
            }
        }
    }
    
    public function home(){
        //ログインしているユーザーのデータを読み込んで表示する。

    }

    public function logout(){
        //ログアウト処理をする
        if($this->Auth->logout()){
            $this->redirect('/users/');
        }
    }

    private function createClient(){
        return new OAuthClient(CONSUMER_KEY,SECRET_KEY);
    }

}