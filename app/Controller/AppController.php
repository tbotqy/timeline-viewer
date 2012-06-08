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

    public $components = array('Auth','Session');
    public $helpers = array('Html','Form','Session');
    
    public function createClient(){
        return new OAuthClient(CONSUMER_KEY,SECRET_KEY);
    }

    public function getAnchoredStatuses($statuses){
        /* 
         * used to add anchor tags to each status body
         * returns array
         */ 
        
        // initialization of value to return 
        $anchored_statuses = array();
        
        foreach($statuses as $status){
            
            // fetch entity belonging to status
            $entities = $this->Entity->find(
                                            'all',
                                            array(
                                                  'conditions'=>array(
                                                                      'Entity.status_id_str'=>$status['Status']['status_id_str']
                                                                      )
                                                  )
                                            );
            
            // apply each entity's anchor tag
            foreach($entities as $entity){
                $type = $entity['Entity']['type'];
                // get entity's body
                $entity_body = "";
                
                switch($type){
                case 'urls':
                case 'media':
                    $entity_body = $entity['Entity']['url'];
                    break;
                case 'hashtags':
                    $entity_body = $entity['Entity']['hashtag'];
                    break;
                case 'user_mentions':
                    $entity_body = $entity['Entity']['mention_to_screen_name'];
                    break;
                }
                $status['Status']['text'] = $this->addAnchorLinks($status['Status']['text'],$entity_body,$type);
            }
            $anchored_statuses[] = $status;
        }
    
        return $anchored_statuses;
    }
 
    public function addAnchorLinks($tweet,$entity,$entity_type){
      
        // inserts anchor elements to given $tweet_body
        // returns string
        
        // determine href 
        switch($entity_type){
        case 'urls':
        case 'media':
            $href = $entity;
            break;
        case 'hashtags':
            $entity = "#".$entity;
            $href = "https://twitter.com/search?q=#".urlencode($entity);
            break;
        case 'user_mentions':
            $href = "https://twitter.com/".$entity;
            $entity = "@".$entity;
            break;
        default:
            echo $entity_type;echo "<br/>";
        }

        // insert <a href=...></a>
        
        $a_element = "<a href=\"".$href."\" target=\"_blank\">".$entity."</a>";
        $ret = str_replace($entity,$a_element,$tweet);
        
        return $ret;
    }
}