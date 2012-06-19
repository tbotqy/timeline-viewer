<?php
  if($statuses){
      echo $this->element('each-status');
      echo $this->element('read-more');
  }else{
      echo $this->element('no-more-status');
  }
?>
