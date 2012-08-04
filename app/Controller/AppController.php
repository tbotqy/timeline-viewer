<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */

class AppController extends Controller {

    public $uses = array('User','Status','Entity','Friend');
    public $components = array('Auth','Session','Twitter');
    public $helpers = array('Html','Session','Text','Link');
    
    public $userIsInitialized = false;
    
    public function beforeFilter(){
        
        parent::beforeFilter();

        $this->disableCache();

        $this->set('title_for_layout','Timedline');
        
        // check if user is logged in
        $loggedIn = $this->Auth->loggedIn();

        if($loggedIn){
            $loggingUser = $this->Auth->user();
            
            // check if user is initialized 
            $this->checkInitialized();
            
            // check if user id in Auth info exists in database
            $id = $loggingUser['id'];
            $twitter_id = $loggingUser['Twitter']['id'];
            
            if(!$this->User->userExists($id)){
                $this->Auth->logout();
                $this->redirect('/');
                return ;
            }
            
            $this->set('loggingUser',$loggingUser);
        }
            
        $userIsInitialized = $this->User->isInitialized($this->Auth->user('id'));
        
        $this->set('userIsInitialized',$userIsInitialized);
        $this->set('loggedIn',$loggedIn);
        $this->set('actionType',$this->request->params['action']);
        $this->set('isAjax',$this->request->isAjax());
        $this->set('showFooter',false);

        if(Configure::read('underConstruction')){
            return $this->render('under-construction');
        }
        
    }
    
    public function checkInitialized(){
     
        $this->userIsInitialized = $this->User->isInitialized($this->Auth->user('id'));
    
    }

    public function getLastLine($array){
    
        /**
         * returns the last element of given $array
         * @param array $array
         * @return array
         */
        
        $itr_last = count($array) - 1;
        return $array[$itr_last];
    }


    public function createClient(){
        
        return new OAuthClient( Configure::read('twitter_consumer_key'), Configure::read('twitter_consumer_secret'));
    
    }

    public function convertTimeToDate($time,$utc_offset,$format = 'Y/m/d - H:i:s'){

        $user_time = $time + $utc_offset;
        return date($format,$user_time);

    }

}