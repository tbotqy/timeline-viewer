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
        $token = $this->User->findByTwitterId($user['Twitter']['id'],
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
        
        //                           //
        // acquire and save statuses //
        //                           //

        $max_id = $this->request->data('id_str_oldest');
        if(!$max_id){
            // [ToDo]
            // acquire latest 100 statuses
            // save those to database
        }else{
            // [ToDo]
            // acquire statuses which are older than the status with max_id
            // save those to database
        }
        
        //                                //
        // define the json data to return //
        //                                //
        
        // determine whether continue loop or not
        $continue = count($fetched_statuses) < 100 ? false : true;
        // how many of new statuses added to database
        $saved_count = ;
        // show user the status currently fetching
        $current_status = ;

        $ret = array(
                     'continue' => $continue,
                     'saved_count' => $saved_count,
                     'current_status' => array(
                                               'date'=>'',
                                               'text'=>''
                                               )
                     );
        echo json_encode($ret);
    }


    
    public function ajax_test(){
        $this->autoRender = false;

        $received_num = json_decode($this->request->data('num'));

        $num = $received_num;
        $num ++;

        $ret = array('num'=>$num);
        echo json_encode($ret);
        return;

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