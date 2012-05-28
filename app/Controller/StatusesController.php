<?php

/*
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    public $components = array('Auth','Session');
    public $helpers = array('Html','Form');
    public $layout = 'common';
    public $uses = array('User','Status','Entity');
    
    public function beforeFilter(){
        $this->Auth->deny('import');
        parent::beforeFilter();
    }

    public function import(){

        /*
         * show the import screen
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
         * This action calls twitter api to retrieve user's twitter statuses.
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

                $created_at = strtotime($val['created_at'])-32400;// based on UTC+0
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
                                $entity_to_save['screen_name'] = $content['screen_name'];
                                $entity_to_save['reply_to_user_id_str'] = $content['id_str'];
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
}