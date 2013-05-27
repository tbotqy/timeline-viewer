<?php

/**
 * Model/PublicDate
 * deal with 'created_at' value in tweet data
 */

class PublicDate extends AppModel{

    public $name ='PublicDate';
    
    public function convertTimeToDate($created_at){

        /** 
         * convert given $unixtime to YYYY/M/D 
         * @param int $unixtime
         * @return string converted unixtime in YYYY/M/D
         */

        return date('Y/n/j',$created_at);
    }
    
    public function dateExists($created_at){

        $date = $this->convertTimeToDate($created_at);
        
        $result = $this->find('count',array(
                'conditions'=>array(
                    'posted_date'=>$date
                )
            )
        );

        return $result > 0;
    }
    
    public function addRecord($created_at){
        if( !$this->dateExists($created_at) ){
            $this->create();
            $this->save(
                array(
                    'posted_date'=>$this->convertTimeToDate($created_at),
                    'posted_unixtime'=>$created_at
                )
            );
        }
    }

    public function getList($dest = 'posted_unixtime'){
        return $this->find('list',array(
                'fields'=>$dest,
                'order'=>'posted_unixtime DESC'
            )
        );
    }
    
}