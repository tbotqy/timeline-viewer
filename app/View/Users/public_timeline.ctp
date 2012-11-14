<?php 
  if($date_list){
      echo $this->element('dashbord');
  }
?>

<!-- #wrap-timeline-lower -->
<div id="wrap-timeline-lower">

  <!-- #wrap-main -->
  <div id="wrap-main">
    
    <?php
        echo $this->element('timeline');
    ?>
    
  </div>
  <!-- /#wrap-main -->
  
  <?php
    echo $this->element('ads-timeline');
  ?>

</div>
<!-- /#wrap-timeline-lower -->
