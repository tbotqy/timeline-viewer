<?php

class PublicDateFixture extends CakeTestFixture{
    
    public $fields = array(
        'id'=>array('type'=>'integer','key'=>'primary'),
        'posted_date'=>array('type'=>'string','length'=>10,'null'=>false)
    );

    public $records = array(
        array('id'=>1,'posted_date'=>'2013/5/10'),
        array('id'=>2,'posted_date'=>'2012/12/31'),
        array('id'=>3,'posted_date'=>'2011/10/10')
    );
    
}