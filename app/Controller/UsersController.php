<?php
// Controller/UsersController.php

App::import('Vendor','OAuth/OAuthclient.php');
define('TWITTER_CONSUMER_KEY','A52ym5fFasTBlmkXlF18IA');
define('TWITTER_CONSUMER_SECRET','bDrO3hFyUlQwESnCW8LU2wRcI0LrkXjqUF6YRm4XaXA');
class UsersController extends AppController{
    
    public $helpers = array('Html','Form');
    public $components = array('Auth');
    public $layout = 'common';

    public function index(){
        //ログインしているか調べる
        if($this->Auth->loggedIn()){
            //ログインしている場合はhome()へリダイレクト
            $this->redirect('/users/home');
        }else{
            //ログインしていない場合はlogin画面を描写
            $this->redirect('/users/login');
        }
    }

    public function login(){
        //login画面を表示する
    }

    public function authorize(){
        //TwitterAPI連携の処理を行う。
        $client = $this->createClient();
        $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/users/callback');
  
        if($requestToken){
            //Twitter.comへリダイレクト
            $this->Session->write('twitter_request_token', $requestToken);
            $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
        }else{
            $this->Session->setFlash('failed in connecting to twitter. Please try again later.');
        }
    }

    public function callback(){
        //Twitter.comからリダイレクトされた時に呼ばれる
        //Twitterのidがデータベースにあるか調べる。ない場合、Statuses/import()へリダイレクト
        //ある場合、ツイートのインポートが済んでいるか調べる。済んでいる場合、home()へリダイレクト
        //済んでいない場合、Statuses/import()へリダイレクト
        $requestToken = $this->Session->read('twitter_request_token');
        $client = $this->createClient();
        $accessToken = $client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);
        
        if($accessToken){
            echo $accessToken->key;
            echo $accessToken->secret;
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
        return new OAuthClient('TWITTER_CONSUMER_KEY','TWITTER_CONSUMER_SECRET');
    }
}