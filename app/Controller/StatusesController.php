<?php

/**
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    public $layout = 'common';
    
    public function beforeFilter(){

        parent::beforeFilter();
        $this->Auth->allow('sum');
        $this->Auth->deny('import');
    
    }
    
    public function import(){

        /**
         * show the screen for operating import method
         */

        $this->set('title_for_layout','Timedline | データの取り込み');
        
        $user =  $this->Auth->user();
        
        // redirect user if already has been initialized
        if($this->User->isInitialized($user['id'])){
            return $this->redirect('/your/tweets');
        }
        
        // get the total number of tweets user has on twitter        
        $param = array(
                       'user_id'=>$user['Twitter']['id'],
                       'include_entities'=>true
                       );

        $twitterUserInfo = $this->Twitter->get('users/show',$param);
        
        $statusesCount = $twitterUserInfo['statuses_count'];
        
        $this->set('statuses_count',$statusesCount);
        $this->set('screen_name',$user['Twitter']['screen_name']);
        $this->set('profile_image_url_https',$user['Twitter']['profile_image_url_https']);
        $this->set('showFooter',true);
        
    }
    
    public function sum(){
        $this->set('title_for_layout','共有ツイート数の推移 - Timedline');
        $this->set('showFooter',true);
    }

}
