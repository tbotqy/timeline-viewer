<?php
/*
 * Controller/StatusesController.php
 */

class StatusesController extends AppController{
    
    public $components = array('Auth','Session');
    public $helpers = array('Html','Form');
    public $layout = 'common';
    public $uses = array('User');
    
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

        //                           //
        // acquire and save statuses //
        //                           //

        //$api_method = "statuses/user_timeline.json";
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
        
            $token = $this->User->findByTwitterId(
                                                  $user['Twitter']['id'],
                                                  array('User.token','User.token_secret')
                                                  );
            $result = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/statuses/user_timeline.json',$api_params);

            $result = json_decode(file_get_contents($api_url),true);
        }else{
            // acquire 101 statuses which are older than the status with max_id
            $api_params['count'] = 101;
            $api_params['max_id'] = $max_id;

            $result = $client->get($token['User']['token'],$token['User']['token_secret'],'https://api.twitter.com/1/statuses/user_timeline.json',$api_params);

            // remove newest status from result because the status with max_id has been already saved 
            array_shift($result);
        }

        // [ToDo]save acquired data
        
        //                                //
        // define the json data to return //
        //                                //
        
        // determine whether continue loop in ajax or not
        $continue = count($result) < 100 ? false : true;
        // number of statuses added to database
        $saved_count = count($result);
        // status currently fetching
        $last_status = end($result);
        
        $text = $last_status['text'];       
        $id_str_oldest = $last_status['id_str'];

        $utc_offset = $last_status['user']['utc_offset'];
        $created_at = strtotime($last_status['created_at']);// convert its format to unix time
        $created_at -= 32400;// fix server's timezone
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
                            'include_rts'=>true,
                            'include_entities'=>true,
                            'screen_name'=>$user['Twitter']['screen_name'],
                            'count'=>100
                            );     
        
        $result = $client->get(
        
        echo "<meta charset='utf-8' />";
        print_r($result);
        //echo count($result);
    }

    public function ajax_test(){
        $this->autoRender = false;
        
        $nullData = $this->request->data('nullData');
        $nullData = null;
        echo gettype($nullData);
    }

    private function createApiUrl($method,$params = null){
        $url = 'http://twitter.com/'.$method;
        $ret = '';
        if($params){
            $params_on_url= '?';
            $loop_count = 0;
            foreach($params as $key=>$val){
                $loop_count++;
                $params_on_url .= $key .'='.$val;
                if($loop_count > 0 && $loop_count < count($params)){
                    $params_on_url .= '&';
                }
            }
            $ret = $url.$params_on_url;
        }else{
            $ret = $url;
        }

        return $ret;
    }
}