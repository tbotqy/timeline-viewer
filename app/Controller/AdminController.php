<?php

/**
 * Controller/AdminController.php
 */

class AdminController extends AppController{

    public $layout = 'common';
    
    public $components = array('Parameter');

    public function beforeFilter(){

        parent::beforeFilter();
        
        $this->Auth->allow('deny');
        
        if(Configure::read('underConstruction')){
            return $this->render('under-construction');
        }
        
    }

    public function index(){

        $isAdmin = false;
             
        $user = $this->Auth->user();
       
        // load the list of admin user names
        $adminList = Configure::read('adminList');
        
        // check if current logging user is included in adminList
        foreach($adminList as $name){
            
            if($user['Twitter']['screen_name'] == $name){
                $isAdmin = true;
                break;
            }
        }

        if(!$isAdmin){
            return $this->redirect('/users/logout');
        }

        $gone_users = $this->User->getGoneUsers();

        $this->set('gone_users',$gone_users);
      
    }



}