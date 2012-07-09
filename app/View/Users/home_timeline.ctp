<?php
echo $this->Html->script('popstate.js',array('inline'=>false));
?>


<?php 
  if($date_list){
      echo $this->element('dashbord');
  }
?>

<!-- #wrap-main -->
<div id="wrap-main" class="home-timeline">

  <?php
      if($statuses){
          echo $this->element('timeline');
      }else{
          echo $this->element('invite-friends');
      }
  ?>
  
</div>
<!-- /#wrap-main -->
