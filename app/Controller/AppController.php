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

    public $uses = array('User','Status','Friend','Entity');
    public $components = array('Auth','Session','Twitter');
    public $helpers = array('Html','Session','Text','Link');
    
    public $userIsInitialized = false;
    public $browserOk = false;
    public $isDebug = false;
    public $underConstruction = false;

    public function beforeFilter(){
        
        parent::beforeFilter();
        
        $this->isDebug = Configure::read('debug') > 0;
        $this->underConstruction = Configure::read('underConstruction');
        $this->set('title_for_layout','Timedline - あの日のタイムラインを眺められる、ちょっとしたアプリケーション');
        $this->set('isDebug',$this->isDebug);
        $this->set('isInitialRequest',false);
        $this->set('underConstruction',$this->underConstruction);

        $actionType = $this->request->params['action'];
  
        // check if user agent is compatible
        if($this->isCompatibleUA()){

            // check if the site is under construction
            if($this->underConstruction){
                
                if($this->isRequestedByFacebookPlugin()){
                    // do nothing
                }else{

                    // check if already showing under_construction page
                    if($actionType == "under_construction"){
                        // do nothing
                    }else{
                        return $this->redirect('/under_construction');
                    }
                }

            }else{
                // do nothing
            }

        }else{

            // check if already showing browser warning page
            if($actionType == "browser"){
                // do nothing
            }else{
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
            
            $userIsInitialized = $this->User->isInitialized($this->Auth->user('id'));        

            $tokens = $this->User->getTokens($id);
            $this->Twitter->setAccessToken( $tokens['User'] );
            $this->set('loggingUser',$loggingUser);

        }

        $this->set(compact('userIsInitialized','loggedIn','actionType'));
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

    
    public function isCompatibleUA(){
        
        $ua = $this->getUserAgent();
        
        // the list of user agents not to be accepted
        $uaBlackList = array(
                             'opera',
                             'msie'
                             );  
        
        foreach($uaBlackList as $uaName){

            if($ua === $uaName){

                if($ua === "msie"){
                    $version = (int)$this->getIEVersion();
                    if($version >= 9){
                        return true;
                    }
                }
                
                return false;

            }

        }

        return true;
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

    public function getUserAgent($plane=false){
        
        if(!env('HTTP_USER_AGENT')){
            return false;
        }
        
        $httpUA = env('HTTP_USER_AGENT');

        if($plane) return $httpUA;

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

    /*
    public function getIEVersion(){
    */
        /**
         * check if client browser type is IE and returns its version
         * @return int : version of IE if browser is IE, else false
         */
    /*  
        $httpUA = env('HTTP_USER_AGENT');
        $pos = stripos($httpUA,'msie');

        if($pos === FALSE) return false;

        $ver = substr($httpUA, $pos+5, 2);
        if(strpos($ver,'.')){
            $ver = str_replace(".","",$ver);
        }

        return (int)$ver;
    }
    */
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
            $itrLast = count($array) - 1;
        }else{
            $itrLast = 0;
        }

        return $array[$itrLast];
    }

    public function convertTimeToDate($time,$utcOffset,$format = 'Y/m/d - H:i:s'){

        $userTime = $time + $utcOffset;
        return date($format,$userTime);

    }

}
