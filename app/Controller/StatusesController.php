<?php

/**
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    // chose the layout to render
    public $layout = 'common';
    
    // set the models to be used
    public $uses = array('User','Status','Entity');
    
    // this stuff helps me !
    public $helpers = array('Text');

    public function beforeFilter(){
        $this->Auth->deny('import');
        parent::beforeFilter();
    }
    
    public function import(){

        /*
         * show the screen for operating import method
         */
        
        $user =  $this->Auth->user();
        $this->set('screen_name',$user['Twitter']['screen_name']);
        $this->set('profile_image',$user['Twitter']['profile_image_url_https']);
    }
}