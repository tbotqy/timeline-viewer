<?php

class Entity extends AppModel{
    public $name = 'Entity';
    public $belongsTo = array(
                              'className'=>'Status',
                              'foreignKey'=>'status_id_str'
                              );

}