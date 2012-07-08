<?php

/**
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    // chose the layout to render in
    public $layout = 'common';
    
    // set the models to be used
    public $uses = array('User','Status','Entity');
    
    // enable Text helper
    public $helpers = array('Text');

    public function beforeFilter(){
        $this->Auth->deny('import');
        parent::beforeFilter();
    }
    
    public function import(){

        /**
         * show the screen for operating import method
         */

        $user =  $this->Auth->user();

        // redirect user if already has been initialized
        if($this->User->isInitialized($user['id'])){
            return $this->redirect('/users/sent_tweets');
        }
        
        $param = array(
                       'user_id'=>$user['Twitter']['id'],
                       'include_entities'=>true
                       );
        
        $twitter_user_info = json_decode($this->Twitter->get('users/show',$param),true);
        $statuses_count = $twitter_user_info['statuses_count'];
        
        $this->set('statuses_count',$statuses_count);
        $this->set('screen_name',$user['Twitter']['screen_name']);
        $this->set('profile_image_url_https',$user['Twitter']['profile_image_url_https']);
    }
    
}