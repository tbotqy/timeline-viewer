<?php

/**
 * Model/Entity
 */

class Entity extends AppModel{
    
    public $name = 'Entity';
    
    public function saveEntities($status_id,$status){
      
        /**
         * save given enitities with its status_id linked
         * @return bool : succeed or failed
         */

        $entities = $status['entities'];

        // First,check if specifying type has its node(type means hashtags,urls,ect..)
        foreach($entities as $type=>$contents){
           
            if(count($contents)>0){
                        
                // if the type has, save nodes belonging to the type
                foreach($contents as $content){
                    
                    $entity_to_save = $this->createArrayToSave($status_id,$status,$content,$type);
     
                    $this->create();
                    $this->save($entity_to_save);
                }
                
            }
        }
    }

    public function createArrayToSave($status_id,$status,$entity,$entity_type){

        /**
         * create an array to save
         * returns array
         */
        
        $ret = array(
                     'status_id'=>$status_id,
                     'status_id_str'=>$status['id_str'],
                     'indice_f'=>$entity['indices']['0'],
                     'indice_l'=>$entity['indices']['1'],
                     'type'=>$entity_type,
                     'created'=>time()
                     );
       
        switch($entity_type){
        case 'hashtags':
            $ret['hashtag'] = $entity['text'];
            break;
        case 'urls':
            $ret['url'] = $entity['url'];
            $ret['display_url'] = $entity['display_url'];
            break;
        case 'media':
            $ret['url'] = $entity['url'];
            $ret['display_url'] = $entity['display_url'];
            break;
        case 'user_mentions':
            $ret['mention_to_screen_name'] = $entity['screen_name'];
            $ret['mention_to_user_id_str'] = $entity['id_str'];
            break;
        default:
            // new feature 
        }
        return $ret;
    }

}