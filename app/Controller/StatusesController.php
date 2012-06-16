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

    public function acquire_statuses(){

        /** 
         * calls twitter api to retrieve user's twitter statuses
         * interacts with JavaScript with ajax
         * returns json string
         */
        
        if(!( $this->request->is('ajax') && $this->request->is('post'))){
            echo "bad request";
            exit;
        }

        $this->autoRender = false;
        $this->Twitter->initialize($this);

        $user = $this->Auth->user();
        $token = $this->User->getTokens($user['id']);
            
        //                           
        // acquire and save statuses 
        //                           

        // set params for api call
        $api_params = array(
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'screen_name'=>$user['Twitter']['screen_name']
                            );

        // this is the oldest tweet's id among those which have imported so far
        $max_id = $this->request->data('id_str_oldest');
                
        // configure parameters 
        if(!$max_id){
            // this is the case for first ajax request

            // turn initialized flag true in user model
            $this->User->updateAll(
                                   array('initialized_flag'=>true),
                                   array('User.id'=>$user['id'])
                                   );

            // set 100 as the number of statuses to acquire
            $api_params['count'] = 100;
            
            // acquire latest 100 statuses
            $statuses = $this->Twitter->get('statuses/user_timeline',$api_params);
            $statuses = json_decode($statuses['body'],true);

        }else{
            
            // acquire 101 statuses which are older than the status with max_id
            $api_params['count'] = 101;
            $api_params['max_id'] = $max_id;
            
            // acquire 101 statuses older than max_id
            $statuses = $this->Twitter->get('statuses/user_timeline',$api_params);
            $statuses = json_decode($statuses['body'],true);
         
            // remove the newest status from result because it has been already saved in previous loop
            array_shift($statuses);
        }

        // save acquired data if any
        if($statuses){
            
            // initialize data array 
            $status_to_save = array();
            $entity_to_save = array();

            foreach($statuses as $status){

                // convert date format 
                $created_at = strtotime($status['created_at']);
                
                // this value doesn't always exist in returned data.
                $possibly_sensitive = isset($status['possibly_sensitive']) ? $status['possibly_sensitive'] : false;
                
                // create an array to pass to model
                $status_to_save = array(
                                        'twitter_id'=>$user['Twitter']['id'],
                                        'status_id_str'=>$status['id_str'],
                                        'in_reply_to_status_id_str'=>$status['in_reply_to_status_id_str'],
                                        'in_reply_to_user_id_str'=>$status['in_reply_to_user_id_str'],
                                        'in_reply_to_screen_name'=>$status['in_reply_to_screen_name'],
                                        'place_full_name'=>$status['place']['full_name'],// optional value
                                        'retweet_count'=>$status['retweet_count'],// int
                                        'created_at'=>$created_at,
                                        'source'=>$status['source'],
                                        'text'=>$status['text'],
                                        'possibly_sensitive'=>$possibly_sensitive,// boolean
                                        'created'=>time()
                                        );

                // primary key ++
                $this->Status->create();
                // save the status
                $this->Status->save($status_to_save);
               
                //
                // save entities belong to this status
                //
                
                $entities = $status['entities'];
                
                // First,check if specifying type has its node(type means hashtags,urls,ect..)
                foreach($entities as $type=>$contents){
                    if(count($contents)>0){
                        
                        // if the type has, save nodes belonging to the type
                        foreach($contents as $content){
 
                            $entity_to_save = array(
                                                    'status_id_str'=>$status['id_str'],
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
        
        //                                
        // define the json data to return 
        //                                
        
        // determine whether continue the loop in ajax or not
        $continue = count($statuses) > 0 ? true : false;
    
        // number of statuses added to database
        $saved_count = count($statuses);
        
        // the status to show as one which is currently fetching
        $last_status = end($statuses);
        $text = $last_status['text'];       
        $id_str_oldest = $last_status['id_str'];

        $utc_offset = $last_status['user']['utc_offset'];
        $created_at = strtotime($last_status['created_at']);// convert its format to unix time
        $created_at -= 32400;// [ToDo] check if this process is necessary or not -> fix server's timezone offset
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

        if(!$this->request->is('ajax')){
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

        $itr = count($statuses)-1;
        $last_status_id = $statuses[$itr]['Status']['id'];
        $this->set('last_status_id',$last_status_id);        
        $this->set('statuses',$statuses);
        $this->set('user_data',$user_data);
    }

    public function switch_term(){

        /*
         * change which term of statuses to show
         * returns html responce
         */

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
        $statuses = $this->getAnchoredStatuses($statuses);
        $itr = count($statuses) - 1;
        
        $last_status_id = $statuses[$itr]['Status']['id'];
        $this->set('statuses',$statuses);
        $this->set('last_status_id',$last_status_id);
        $this->set('user_data',$user_data);
        $this->render('switch_term');
    }

    private function convertEntityArrayToSave(array $entities,$parent_status_id){        
       
        /**
         * format given entity array for saving
         * returns array
         * returned array can be passed to Entity->save()
         */
        
        // initialize array to return 
        $entity_to_save = array();
        
        // dive into each entity on array
        foreach($entities as $type=>$contents){
            
            if(count($contents)>0){// if seeing entity array has any content inside

                // save each of entities
                foreach($contents as $content){
                    // set common values 
                    $entity_to_save = array(
                                            'status_id_str'=>$parent_status_id,
                                            'indice_f'=>$content['indices']['0'],
                                            'indice_l'=>$content['indices']['1'],
                                            'type'=>$type,
                                            'created'=>time()
                                            );
                    
                    // add some values varying in types
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
                }
            }
        }

        return $entity_to_save;
    }

    public function getEntity(){
 
        $this->Twitter->initialize($this);
        $user = $this->Auth->user();
        $param = array('include_entities'=>true,'max_id'=>'18131468815437824');
        $result = $this->Twitter->get('statuses/user_timeline',$param);
        pr($result = json_decode($result,true));exit;
        $this->set('text',$result['0']['text']);
    }
}