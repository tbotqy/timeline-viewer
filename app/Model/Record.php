<?php

/**
 * Model/Record.php
 */

class Record extends AppModel{
    
    public function addRecords(array $statusArray){
        $count = 0;
        foreach($statusArray as $s){
  
            $data = array('status_id'=>$s['Status']['id'],
                          'status_text'=>$s['Status']['text'],
                          'done'=>false,
                          );
            $this->create();
            $this->save($data);
            $count++;
        }

        return $count;

    }

    public function getUndoneRecords($limit = null){

        if($limit === 0) return false;

        $ret = $this->find('all',
                           array(
                                 'conditions'=>array(
                                                     'done'=>false
                                                     ),
                                 'limit'=>$limit
                                 )
                           );
        
        return count($ret) > 0 ? $ret : false;
    }

    public function markAsDone($id){
        
        $this->id = $id;
        $this->saveField('done',true);
        $this->saveField('done_at',time());
        
    }
    
    public function getProgress(){

        $countDone = $this->find('count',
                                 array(
                                       'conditions'=>array(
                                                           'done'=>true
                                                           )
                                       )
                                 );
        
        $countAll = $this->find('count');
        
        $ret = ($countDone/$countAll) * 100;
        
        return (int)$ret;
    }
}