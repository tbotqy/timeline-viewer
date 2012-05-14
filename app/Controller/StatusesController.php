<?php
/*
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    public $components = array('Auth','Session');
    public $helpers = array('Html','Form');
    public $layout = 'common';
    public $uses = array('Status','User');
    
    public function beforeFilter(){
        $this->Auth->deny('import');
        parent::beforeFilter();
    }

    public function import(){
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
        /* This action calls twitter api to retrieve user's twitter statuses.
         * interacts with JavaScript with ajax
         * returns json string
         */
        
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

        // [ToDo]save acquired data if there are
        if( count($result) > 0){
            foreach($result as $val){
                // [debug code]
                $text = $val['text'];
                $id_str = $val['id_str'];
                $created_at = strtotime($val['created_at'])-32400;// based on GMT+0
                $created = time();// based on server's timezone 

                $data_to_save = array(
                                      'twitter_id'=>$user['Twitter']['id'],
                                      'id_str'=>$id_str,
                                      'created_at'=>$created_at,
                                      'text'=>$text
                                      );
                // [debug code]
                //$data_to_save['id'] = (int)$text;

                $this->Status->create();
                $this->Status->save($data_to_save);
                // [ToDo] consider which entity to store from returned status
                // [ToDo] turn initialized flag true in user model
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
    
    public function debug(){
        date_default_timezone_set('Asia/Tokyo');
        $user = $this->Auth->user();
        $client = $this->createClient();
        
        $api_params = array(
                            'include_rts'=>'true',
                            'include_entities'=>true,
                            'screen_name'=>$user['Twitter']['screen_name'],
                            'count'=>100,
                            'max_id'=>'196267504706916352'
                            );     
        $token = $this->User->findByTwitterId($user['Twitter']['id'],
                                              array(
                                                    'User.token',
                                                    'User.token_secret'
                                                    )
                                              );
        
        $result = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/statuses/user_timeline.json',$api_params);

        echo "<meta charset='utf-8' />";
        pr(json_decode($result,true));
    }

    public function ajax_test(){
        $this->autoRender = false;
        
        $nullData = $this->request->data('nullData');
        $nullData = null;
        echo gettype($nullData);
    }    

    public function checkDb(){
        $lack_list = array();
        for($num = 3200;$num>0;$num--){
            $text = $num;
            $result = $this->Status->find('count',
                                          array('conditions'=>array('Status.text'=>$text))
                                          );
        
            if($result == 0){
                $lack_list[] = $num;
            }
        }
        pr($lack_list);
    }

    public function analyze(){
        echo "<meta charset='utf-8' />";
        // sum statuses in each month 

        $user = $this->Auth->user();

        $statuses = $this->Status->find(
                                        'all'
                                        );

        foreach($statuses as $status){
            $status = $status['Status'];

            $created_at = $status['created_at'] + 32400;
            $month = date('n',$created_at);
            $year = date('Y',$created_at);
            $result[] = array($year=>$month);
            
        }

        foreach($result as $key=>$val){
            $num[$key] ++;
        }

        pr($num);

    }

}