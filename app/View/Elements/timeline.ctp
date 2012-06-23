<!-- #wrap-timeline -->
<div id="wrap-timeline">

     <?php 
     if($statuses){
         foreach($statuses as $status){
             echo $this->element('each-status');
         }
     }
     ?>
  
     <?php
       if($hasNext){
           echo $this->element('read-more');
       }else{
           echo $this->element('no-more-status');
       }
     ?>

</div>
<!-- /#wrap-timeline -->
