<?php

App::uses('Entity','Model');

class EntityTest extends CakeTestCase{
    
    public $fixtures = array('app.entity');

    public function setUp(){
        parent::setUp();
        $this->Entity = ClassRegistry::init('Entity');
    }

    public function testCreateArrayToSave(){
        
        // 
        // define fixtures to give
        // 
        $status_id = 1;
        $status = array(
                        'id_str'=>10
                        );
        $entity = array(
                        'indices'=>array(1,2),
                        'text'=>"#hashtag",
                        'url'=>"http://test.com",
                        'screen_name'=>'tester',
                        'id_str'=>'1234'
                        );
        
        // 
        // define expected results
        // 
        $entity_type = "hashtags";
        $expected[$entity_type] = array(
                                        'status_id'=>$status_id,
                                        'status_id_str'=>$status['id_str'],
                                        'indice_f'=>$entity['indices']['0'],
                                        'indice_l'=>$entity['indices']['1'],
                                        'type'=>$entity_type,
                                        'created'=>time(),
                                        'hashtag'=>$entity['text'],
                                        );
        
        $entity_type = "urls";
        $expected[$entity_type] = array(
                                        'status_id'=>$status_id,
                                        'status_id_str'=>$status['id_str'],
                                        'indice_f'=>$entity['indices']['0'],
                                        'indice_l'=>$entity['indices']['1'],
                                        'type'=>$entity_type,
                                        'created'=>time(),
                                        'url'=>$entity['url'],
                                        );
        
        $entity_type = "media";
        $expected[$entity_type] = array(
                                        'status_id'=>$status_id,
                                        'status_id_str'=>$status['id_str'],
                                        'indice_f'=>$entity['indices']['0'],
                                        'indice_l'=>$entity['indices']['1'],
                                        'type'=>$entity_type,
                                        'created'=>time(),
                                        'url'=>$entity['url'],
                                        );


        $entity_type = "user_mentions";
        $expected[$entity_type] = array(
                                        'status_id'=>$status_id,
                                        'status_id_str'=>$status['id_str'],
                                        'indice_f'=>$entity['indices']['0'],
                                        'indice_l'=>$entity['indices']['1'],
                                        'type'=>$entity_type,
                                        'created'=>time(),
                                        'mention_to_screen_name'=>$entity['screen_name'],
                                        'mention_to_user_id_str'=>$entity['id_str']
                                        );
        

        // 
        // define cases
        // 
        $cases = array('hashtags','urls','media','user_mentions');

        //
        // assert
        // 

        foreach($cases as $case){

            $result = $this->Entity->createArrayToSave($status_id,$status,$entity,$case);
            
            foreach($result as $key=>$val){
                $this->assertEquals($val,$expected[$case][$key],$case.":".$key.":".$val."<-->".$expected[$case][$key]);
            }
        }
    }


    
}
