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
    public $browserOk = false;

    public function beforeFilter(){
        
        parent::beforeFilter();
     
        $this->set('title_for_layout','Timedline');

        $actionType = $this->request->params['action'];
        
        // check if current http request is created by facebook plugin
        // because facebook like plugin doesn't work if set url to like fires any redirect
        if(!$this->isRequestedByFacebookPlugin()){
            
            if(Configure::read('underConstruction')){
                
                if($actionType !== 'under_construction'){
                    return $this->redirect('/under_construction');
                }elseif($actionType === 'under_construction'){
                    return;
                }
                
            }
               
            if(!$this->isCompatibleBrowser() && $actionType != 'browser'){
                return $this->redirect('/browser');
            }

        }

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
        $this->set('actionType',$actionType);
        $this->set('isAjax',$this->request->isAjax());
        $this->set('showFooter',false);
    
    }

    public function beforeRender(){
        
        if($this->name == "CakeError"){
            $this->layout = "common";
            $this->set('title_for_layout','Timedline | error');
            $this->set('showFooter',true);
        }

    }
    
    public function isCompatibleBrowser(){
        
        $ret = false;
        $ua = $this->getUserAgent();

        // the list of user agents to be accepted
        $uaWhiteList = array(
                             'msie',
                             'chrome',
                             'firefox',
                             'safari',
                             );
        
        foreach($uaWhiteList as $uaName){

            if($ua === $uaName){

                if($uaName === 'msie'){
                    // if IE,only 9+ is accepted
                    $version = (int)$this->getIEVersion();
                    if($version >= 9){
                        $ret = true;
                    }
                }else{
                    $ret = true;
                }
            }

        }

        if($ret){
            $this->browserOk = true;
        }

        return $ret;

    }
    
    public function isRequestedByFacebookPlugin(){

        // check if current http request is made by facebook plugin
        // by checking if HTTP_USER_AGENT contains string 'facebook
        
        //$destinationString = "facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)"
        $ua = env('HTTP_USER_AGENT');

        // check ua
        if(strpos($ua,'facebook') !== false){
            return true;
        }else{
            return false;
        }

    }

    public function getUserAgent(){

        if(!env('HTTP_USER_AGENT')){
            return false;
        }
        
        $httpUA = env('HTTP_USER_AGENT');

        $uaStrList = array(
                           'msie',
                           'chrome',
                           'safari',
                           'firefox',
                           'opera'
                           );

        foreach($uaStrList as $uaName){
        
            if(stripos($httpUA,$uaName) !== false){
                return $uaName;
            }
       
        }

        return "unkown agent";
        
    }

    public function getIEVersion(){

        if(!env('HTTP_USER_AGENT')){
            return false;
        }
        
        $httpUA = env('HTTP_USER_AGENT');
        $pos = stripos($httpUA,'msie');
        $ver = substr($httpUA, $pos+5, 1);
        
        return $ver;
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

        if(!is_array($array)){
            return false;
        }
        
        if(count($array) > 0){
            $itr_last = count($array) - 1;
        }else{
            $itr_last = 0;
        }

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