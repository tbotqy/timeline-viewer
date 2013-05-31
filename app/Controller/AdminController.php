<?php

/**
 * Controller/AdminController.php
 */

class AdminController extends AppController{

    public $layout = 'common';
    
    public $components = array('Parameter');

    public function beforeFilter(){

        parent::beforeFilter();

        $this->set('title_for_layout','Timedline | Admin');
        
        $this->Auth->allow('deny');

        $isAdmin = false;
             
        $user = $this->Auth->user();


        // load the list of admin user names
        $adminList = Configure::read('adminList');
        
        // check if current logging user is included in adminList
        foreach($adminList as $name){
            
            if($user['Twitter']['id'] == $name){
                $isAdmin = true;
                break;
            }
        }

        if(!$isAdmin){
            return $this->redirect('/users/logout');
        }
        
        if(Configure::read('underConstruction')){
            return $this->render('under-construction');
        }
        
    }

    public function index(){}
    
    public function statuses(){}
    
    public function accounts(){
        
        if(false){
        
            /**
             * code for maintenance
             * create the list data of tweets' posted time with public timeline
             */
    
            $all = $this->Status->find('list',array(
                    'conditions'=>array('pre_saved'=>false),
                    'fields'=>array('created_at'),
                    'order'=>array('created_at ASC'),
                    'group'=>array('created_at'),
                    'recursive'=>-1
                )
            );
            
            App::import('Model','PublicDate');
            $this->PublicDate = new PublicDate();
            foreach($all as $k=>$v){
                $this->PublicDate->addRecord($v);
            }
            echo "done";exit;
        }
        
        $activeUsers = $this->User->getActiveUsers();

        $goneUsers = $this->User->getGoneUsers();

        $this->set('gone_users',$goneUsers);
        $this->set('active_users',$activeUsers);
        $this->set('showFooter',true);

    }



}