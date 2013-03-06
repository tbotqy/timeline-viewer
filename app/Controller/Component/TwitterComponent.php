<?php

/**
 * component for Twitter OAuth
 */

App::uses('Component','Controller');
App::import('Vendor','OAuth/OAuthClient');

define('TWITTER_API_VERSION',1.1);

class TwitterComponent extends Component{
    
    private $consumerKey = null;
    private $consumerSecretKey = null;
    private $accessToken = null;
    private $accessTokenSecret = null;
    private $client = null;
    private $callBackUrl = null;
    private $apiVersion = null;

    public function __construct(){
        $this->consumerKey = Configure::read('CONSUMER_KEY');
        $this->consumerSecretKey = Configure::read('CONSUMER_SECRET_KEY');
        $this->callBackUrl = Configure::read('CALL_BACK');
        $this->apiVersion = TWITTER_API_VERSION;
        $this->client = new OAuthClient( $this->getConsumerKey(),$this->getConsumerSecretKey() );
    }

    public function getUserTweets($twitterId,$sinceId = null,$maxId = null,$count = 100){
        $ret = "";
        // set options
        $options = array('user_id'=>$twitterId,'count'=>$count,'include_rts'=>true);
        
        if($maxId) $options['max_id'] = $maxId;
        if($sinceId) $options['since_id'] = $sinceId;

        $ret = $this->get('statuses/user_timeline',$options);
        
        return $ret;
    }

    public function getFollowing($twitterId){

        /**
         * @param mixed $id
         * @return array or false if failed in
         */

        $ret = $this->get('friends/ids',array('user_id'=>$twitterId));

        return $ret ? $ret['ids'] : false;
    }

    public function getFollowers($twitterId){
        
        /**
         * @param mixed $id
         * @return array or false if failed in
         */

        $ret = $this->get('followers/ids',array('user_id'=>$twitterId));
        return $ret ? $ret['ids'] : false;
    }

    public function getIdByScreenName($screenName){
        /**
         * convert given screen name to twitter id
         * @param mixed $id
         * @return string : the screen name found by given $id
         */

        $userInfo = $this->get('users/show',array('screen_name'=>$screenName));
        
        return $userInfo ? $userInfo['id_str'] : false;

    }
    
    public function getAuthorizeUrl(Object $requestToken){

        /**
         * @param object $requestToken, should countain both 'key' and 'secret'
         * @return string : url to authorize user if success, else false
         */
        
        if($requestToken){
            return 'https://api.twitter.com/oauth/authorize?oauth_token='.$requestToken->key;
        }else{
            return false;
        }
        
    }

    public function getRequestToken(){
        $requestToken = $this->client->getRequestToken('https://api.twitter.com/oauth/request_token',$this->callBackUrl);
        return $requestToken ? $requestToken : false;
    }

    public function getAccessToken(OAuthToken $requestToken){
        $accessToken = $this->client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);
        return $accessToken ? $accessToken : false;
    }

    public function getHttpResponse(){
        return $this->client->getFullResponse();
    }
   
    private function getConsumerKey(){
        return $this->consumerKey;
    }

    private function getConsumerSecretkey(){
        return $this->consumerSecretKey;
    }
    
    public function setAccessToken(Array $accessToken){
        /**
         * @param Array $accessToken
         * @return void
         */
        $this->accessToken = array_key_exists('key',$accessToken) ? $accessToken['key'] : $accessToken['token'];
        $this->accessTokenSecret = array_key_exists('secret',$accessToken) ? $accessToken['secret'] : $accessToken['token_secret'];
    }

    public function getVerifyCredentials(){
        $ret = $this->get('account/verify_credentials',array(),false);
        return $ret ? $ret : false;
    }

    public function deleteTweet($statusId){
        
        /**
         * delete the status with given $statusId
         * @return void
         */

        $this->post('statuses/destroy',array('id'=>$statusId));
    }

    public function follow($id){
        /**
         * follow the user whose id = $id
         * @param mixed $id
         * @return void
         */
        
        $this->post('friendships/create',array('user_id'=>$id));
        
    }

    public function unfollow($id){
        /**
         * unfollow the user whose id = $id
         * @param mixed $id
         * @return void
         */

        //$this->post('friendships/destroy',array('user_id'=>$id));
        $this->post('friendships/destroy',array('screen_name'=>$id));

    }

    public function get($method,$options = array(),$returnAsArray = true){
        
        $ret = $this->client->get($this->accessToken,$this->accessTokenSecret,'https://api.twitter.com/'.$this->apiVersion.'/'.$method.'.json',$options);
        
        if($returnAsArray){
            $ret = json_decode($ret,true);

            if( array_key_exists('error',$ret) ){
                return false;
            }
        }else{
            if( property_exists($ret,'error') ){
                    return false;
                }
        }
        
        return $ret ? $ret : false;
    }

    public function post($method,$options = array()){
        $ret = $this->client->post($this->accessToken,$this->accessTokenSecret,'https://api.twitter.com/'.$this->apiVersion.'/'.$method.'.json',$options);
        return $ret ? $ret : false;
    }


}
