<!-- #wrap-timeline -->
<div id="wrap-timeline">

     <?php 
     if($statuses){
         echo $this->element('each-status');
         if($hasNext){
             echo $this->element('read-more');
         }else{
             echo $this->element('no-more-status');
         }

     }else{
         echo $this->element('no-status');
     }
     ?>

</div>
<!-- /#wrap-timeline -->
