<?php

/*
 * component to controll twitter api
 */

class TwitterComponent extends Component{

    public $components = array('Auth');

    private function createClient(){
        return new OAuthClient(CONSUMER_KEY,SECRET_KEY);
    }

    public function initialize($controller){
        $this->controller = $controller;
    }

    public function get($method,$options = array(),$tokens = null){
 
        /* 
         * $method : something like... statuses/user_timeline
         * $tokens : should be an array containing both access token and access token secret
         * $options : an array containing some params to send to Twitter API
         */
        
        $client = $this->createClient();

        $url = "https://api.twitter.com/1/".$method.".json";
        if(!$tokens){
            // instantiate User model
            $modelUser = $this->controller->User;
            $authUser= $this->Auth->user();
            $tokens = $modelUser->find(
                                       'first',
                                       array(
                                             'conditions'=>array('User.twitter_id'=>$authUser['Twitter']['id']),
                                             'fields'=>array('User.token','User.token_secret')
                                             )
                                       );
            $token = $tokens['User']['token'];
            $token_secret = $tokens['User']['token_secret'];
        }else{
            $token = $tokens['token'];
            $token_secret = $tokens['token_secret'];
        }
        
        return $client->get($token,$token_secret,$url,$options);
    }

        
}