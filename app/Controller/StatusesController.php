<?php

/*
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    public $components = array('Auth','Session');
    public $helpers = array('Time','Html','Form');
    public $layout = 'common';
    public $uses = array('User','Status','Entity');
    
    public function beforeFilter(){
        $this->Auth->deny('import');
        parent::beforeFilter();
    }

    public function import(){

        /*
         * show the screen to operate import method
         */

        $user =  $this->Auth->user();
        $client = $this->createClient();
        $token = $this->User->findByTwitterId(

                                              $user['Twitter']['id'],
                                              array('User.token','User.token_secret')
                                              );
        
        $verify_credentials = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/account/verify_credentials.json',array('screen_name'=>$user['Twitter']['screen_name']));
        $verify_credentials = json_decode($verify_credentials);
       
        $this->set('screen_name',$user['Twitter']['screen_name']);
        $this->set('profile_image',$verify_credentials->profile_image_url_https);
    }

    public function acquire_statuses(){

        /* 
         * calls twitter api to retrieve user's twitter statuses.
         * interacts with JavaScript with ajax
         * returns json string
         */
        
        if(!$this->request->is('Ajax')){
            // reject any request if not ajax
            echo "bad request";
            exit;
        }

        $this->autoRender = false;
        
        $user = $this->Auth->user();

        $client = $this->createClient();
        $token = $this->User->findByTwitterId(
                                              $user['Twitter']['id'],
                                              array('User.token','User.token_secret')
                                              );
            
        //                           //
        // acquire and save statuses //
        //                           //

        $api_params = array(
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'screen_name'=>$user['Twitter']['screen_name']
                            );     
        $max_id = $this->request->data('id_str_oldest');
                
        // configure parameters 
        if(!$max_id){
            // this is the case for first ajax request

            // [ToDo] turn initialized flag true in user model
            //$this->User->updateAll(array('initialized_flag'=>1),array('User.twitter_id'=>$user['Twitter']['id']));

            // acquire latest 100 statuses
            $api_params['count'] = 100;
        
            $result = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/statuses/user_timeline.json',$api_params);

            $result = json_decode($result['body'],true);
        }else{
            // acquire 101 statuses which are older than the status with max_id
            $api_params['count'] = 101;
            $api_params['max_id'] = $max_id;

            $result = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/statuses/user_timeline.json',$api_params);

            $result = json_decode($result['body'],true);
         
            // remove newest status from result because the status with max_id has been already saved 
            array_shift($result);
        }

        // save acquired data if there are
        if(count($result) > 0){
            
            // initialize data array 
            $status_to_save = array();
            $entity_to_save = array();

            foreach($result as $val){

                $created_at = strtotime($val['created_at']);
                $possibly_sensitive = isset($val['possibly_sensitive']) ? $val['possibly_sensitive'] : false;
                
                $status_to_save = array(
                                        'twitter_id'=>$user['Twitter']['id'],
                                        'status_id_str'=>$val['id_str'],
                                        'in_reply_to_status_id_str'=>$val['in_reply_to_status_id_str'],
                                        'in_reply_to_user_id_str'=>$val['in_reply_to_user_id_str'],
                                        'in_reply_to_screen_name'=>$val['in_reply_to_screen_name'],
                                        'place_full_name'=>$val['place']['full_name'],// optional value
                                        'retweet_count'=>$val['retweet_count'],// int
                                        'created_at'=>$created_at,
                                        'source'=>$val['source'],
                                        'text'=>$val['text'],
                                        'possibly_sensitive'=>$possibly_sensitive,// boolean
                                        'created'=>time()
                                        );

                // save this status
                $this->Status->create();
                $this->Status->save($status_to_save);
               
                // save entities belong to this status
                $entities = $val['entities'];
                
                foreach($entities as $type=>$contents){
                    if(count($contents)>0){

                        // save each of entities
                        foreach($contents as $content){
                            $this->Entity->create();
                            $entity_to_save = array(
                                                    'status_id_str'=>$val['id_str'],
                                                    'indice_f'=>$content['indices']['0'],
                                                    'indice_l'=>$content['indices']['1'],
                                                    'type'=>$type,
                                                    'created'=>time()
                                                    );
                            
                            switch($type){
                            case "hashtags":
                                $entity_to_save['hashtag'] = $content['text'];
                                break;
                            case "urls":
                            case "media":
                                $entity_to_save['url'] = $content['url'];
                            break;
                            case "user_mentions":
                                $entity_to_save['mention_to_screen_name'] = $content['screen_name'];
                                $entity_to_save['mention_to_user_id_str'] = $content['id_str'];
                                break;
                            default:
                                // new feature 
                            }
                            $this->Entity->create();
                            $this->Entity->save($entity_to_save);
                        }
                    }
                }
            }
        }
        
        //                                //
        // define the json data to return //
        //                                //
        
        // determine whether continue loop in ajax or not
        $continue = count($result) > 0 ? true : false;
        // number of statuses added to database
        $saved_count = count($result);
        // status currently fetching
        $last_status = end($result);
        
        $text = $last_status['text'];       
        $id_str_oldest = $last_status['id_str'];

        $utc_offset = $last_status['user']['utc_offset'];
        $created_at = strtotime($last_status['created_at']);// convert its format to unix time
        $created_at -= 32400;// fix server's timezone offset
        $created_at += $utc_offset;// timezone equal to the one configured in user's twitter profile
        $created_at = date("Y/m/d - H:i",$created_at);
      
        $ret = array(
                     'continue' => $continue,
                     'saved_count' => $saved_count,
                     'id_str_oldest' => $id_str_oldest,
                     'status' => array(
                                       'date'=>$created_at,
                                       'text'=>$text
                                       )
                     );
        
        // return json
        echo json_encode($ret);
    }

    public function read_more(){
        
        $this->layout = 'ajax';

        if(!$this->request->is('Ajax')){
            // reject that request
            echo 'bad request';
            exit;
        }

        // fetch more 10 statuses whose id is greater than last status id
        $last_status_id = $this->request->data('last_status_id');       
        $user = $this->Auth->user();
        $twitter_id = $user['Twitter']['id'];
        
        // fetch user's twitter account info
        $user_data = $this->User->find(
                                       'first',array(
                                                     'conditions'=>array('User.twitter_id'=>$twitter_id),
                                                     'fields'=>array(
                                                                     'User.twitter_id',
                                                                     'User.name',
                                                                     'User.screen_name',
                                                                     'User.profile_image_url_https',
                                                                     'User.utc_offset'
                                                                     )
                                                     )
                                       );

        $statuses = $this->Status->find(
                                        'all',
                                        array(
                                              'conditions'=>array(
                                                                  'Status.twitter_id'=>$user['Twitter']['id'],
                                                                  'Status.id >'=>$last_status_id
                                                                  ),
                                              'limit'=>10,
                                              'order'=>'Status.created ASC'
                                              )
                                        );

        // add anchor links to each entities on the status
        $statuses = $this->getAnchoredStatuses($statuses);

        $itr = count($statuses)-1;
        $last_status_id = $statuses[$itr]['Status']['id'];
        $this->set('last_status_id',$last_status_id);        
        $this->set('statuses',$statuses);
        $this->set('user_data',$user_data);
    }

    public function switch_term(){

        $this->layout = 'ajax';
        $user = $this->Auth->user();
        $twitter_id = $user['Twitter']['id'];
        $utc_offset = $user['Twitter']['utc_offset'];

        // fetch query string
        $date = $this->request->query['date'];
        $date_type = $this->request->query['date_type'];
       
        // calculate start/end of term to fetch 
        $term = $this->strToTerm($date,$date_type,$utc_offset);
        
        // fetch user's twitter account info
        $user_data = $this->User->find(
                                       'first',array(
                                                     'conditions'=>array('User.twitter_id'=>$twitter_id),
                                                     'fields'=>array(
                                                                     'User.twitter_id',
                                                                     'User.name',
                                                                     'User.screen_name',
                                                                     'User.profile_image_url_https',
                                                                     'User.utc_offset'
                                                                     )
                                                     )
                                       );

        // fetch statuses
        $statuses = $this->Status->find(
                                        'all',
                                        array(
                                              'conditions'=>array(
                                                                  'Status.twitter_id'=>$user['Twitter']['id'],
                                                                  'Status.created_at >='=>$term['begin'],
                                                                  'Status.created_at <='=>$term['end']
                                                                  ),
                                              'order'=>array('Status.created_at DESC')
                                              )
                                        );
        $itr = count($statuses) - 1;
        if($itr<0){
            pr($term);exit;
        }
        $last_status_id = $statuses[$itr]['Status']['id'];
        $this->set('statuses',$statuses);
        $this->set('last_status_id',$last_status_id);
        $this->set('user_data',$user_data);
        $this->render('switch_term');
    }


    private function strToTerm($date,$date_type,$utc_offset){
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

    private function strToYearTerm($strYear,$utc_offset){
        
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

    private function strToMonthTerm($strMonth,$utc_offset){
        
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
    
    private function strToDayTerm($strDay,$utc_offset){
        
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