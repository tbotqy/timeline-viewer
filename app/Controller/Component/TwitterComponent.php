<?php

/**
 * /Controller/Component/TwitterComponent.php
 * component to controll twitter api
 */

class TwitterComponent extends Component{

    public $components = array('Auth');

    public function createClient(){
        return new OAuthClient( Configure::read('twitter_consumer_key'), Configure::read('twitter_consumer_secret'));
    }

    public function initialize($controller){
        $this->controller = $controller;
    }

    public function get($method,$options = array(),$tokens = null){
 
        /* *
         * @param string $method : something like... statuses/user_timeline
         * @param array $options : an array containing some params to send to Twitter API       
         * @param array $tokens : should be an array containing both access token and access token secret
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