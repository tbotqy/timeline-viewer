<?php
/*
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    public $components = array('Auth','Session');
    public $helpers = array('Html','Form');
    public $layout = 'common';
    public $uses = array('User');
    
    public function beforeFilter(){
        $this->Auth->deny('import');
        parent::beforeFilter();
    }

    public function import(){
        $user =  $this->Auth->user();
        $client = $this->createClient();
        $token = $this->User->findByTwitterId($user['Twitter']['id'],
                                              array('User.token','User.token_secret')
                                              );
        $verify_credentials = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/account/verify_credentials.json',array('screen_name'=>$user['Twitter']['screen_name']));
        $verify_credentials = json_decode($verify_credentials);
       
        $this->set('screen_name',$user['Twitter']['screen_name']);
        $this->set('profile_image',$verify_credentials->profile_image_url_https);
    }

    public function acquire_statuses(){
        /* This action calls twitter api to retrieve user's twitter statuses.
         * interacts with javascript with ajax
         * returns json string
         */

        /* TEST CODE */
        echo $this->request->data('test');
        $user = $this->Auth->user();
        $token = $this->User->findByTwitterId($user['Twitter']['id'],
                                              array('User.token','User.token_secret')
                                              );
        $client = $this->createClient();
        $statuses = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/statuses/user_timeline.json',array('count'=>'200'));
        //pr(json_decode($statuses->body));
    }

    public function ajax_test(){
        $this->autoRender = false;
        $data = $this->request->data('test');
        $ret = array(
                     'data'=>$data
                     );
        $ret = json_encode($ret);
        echo $ret;
        //echo $this->request->data('test');
    }
}