<?php

class StatusesController extends AppController{
    
    public $components = array('Auth','Session');
    public $helpers = array('Html','Form');
    public $layout = 'common';
    public $uses = array('User');
    public function import(){
        //ツイートの取り込みを行う画面を表示する。
       $user =  $this->Auth->user();

       $client = $this->createClient();
       $token = $this->User->findByTwitterId($user['Twitter']['id'],
                                             $fields = array('User.token','User.token_secret')
                                             );
       $verify_credentials = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/account/verify_credentials.json',array('screen_name'=>$user['Twitter']['screen_name']));
       $verify_credentials = json_decode($verify_credentials);
       $this->set('screen_name',$user['Twitter']['screen_name']);
       $this->set('profile_image',$verify_credentials->profile_image_url_https);
    }

    public function acquire_statuses(){
        //APIを呼んでデータを取得
        //データベースに保存
    }
}