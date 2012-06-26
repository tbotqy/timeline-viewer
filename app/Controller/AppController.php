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

    public $components = array('Auth','Session','Twitter');
    public $helpers = array('Html','Form','Session','Text','Link');
    public $uses = array('User');

    public function beforeFilter(){
        parent::beforeFilter();
        
        // check if user is logged in
        $loggedIn = $this->Auth->loggedIn();
        // pass it to view
        $this->set('loggedIn',$loggedIn);
        
        if($loggedIn){
            $loggingUser = $this->Auth->user();
            $this->set('loggingUser',$loggingUser);
            
            $userIsInitialized = $this->User->isInitialized($this->Auth->user('id'));
            $this->set('userIsInitialized',$userIsInitialized);
        }
        
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
        return new OAuthClient(CONSUMER_KEY,SECRET_KEY);
    }

    public function termToTime($date,$date_type,$utc_offset){
        $ret = "";

        switch($date_type){
        case 'year':
            $ret = $this->strToYearTerm($date,$utc_offset);
            break;
        case 'month':
            $ret = $this->strToMonthTerm($date,$utc_offset);
            break;
        case 'day':
            $ret = $this->strToDayTerm($date,$utc_offset);
            break;
        }
        
        return $ret;
    }

    public function strToYearTerm($strYear,$utc_offset){
        
        /*
         * given value is exected to be year format like 2012
         * convert given value to unixtime 
         * returning array contains begin/end unixtime of given year
         */
        
        // create string representing the first day of year like 2012-1-1 00:00:00
        $strBegin = $strYear.'-1-1 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utc_offset;
        
        // create string representing the last moment of year like 2012-12-31 23:59:59
        $strEnd = $strYear.'-12-31 23:59:59';
        $timeEnd = strtotime($strEnd) - $utc_offset;

        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }

    public function strToMonthTerm($strMonth,$utc_offset){
        
        /*
         * given value is exected to be ike 2012-5
         * convert given value to unixtime 
         * returning array contains begin/end unixtime of given month
         */
        
        // create string representing the first day of month like 2012-2-1 00:00:00
        $strBegin = $strMonth.'-1 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utc_offset;
        
        // create string representing the last moment of month like 2012-2-29 23:59:59
        $last_day_of_month = date('t',strtotime($strMonth));
        $strMonth .= '-'.$last_day_of_month;
        $strEnd = $strMonth.' 23:59:59';
        $timeEnd = strtotime($strEnd) - $utc_offset;

        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }
    
    public function strToDayTerm($strDay,$utc_offset){
        
        /*
         * given value is exected to be ike 2012-5-1
         * convert given value to unixtime 
         * returning array contains begin/end unixtime of given day
         */
        
        // create string representing the first monet of day like 2012-5-1 00:00:00
        $strBegin = $strDay.' 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utc_offset;
        
        // create string representing the last moment of day like 2012-5-1 23:59:59
        $strEnd = $strDay.' 23:59:59'; 
        $timeEnd = strtotime($strEnd) - $utc_offset;
        
        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }


}