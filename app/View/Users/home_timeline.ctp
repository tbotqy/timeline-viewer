<?php
echo $this->Html->script('popstate.js',array('inline'=>false));
?>


<?php 
  if($date_list){
      echo $this->element('dashbord');
  }
?>

<!-- #wrap-main -->
  <?php if($statuses):?>
    <div id="wrap-main">
  <?php else:?>
    <div id="wrap-main" class="friend-not-found">
  <?php endif;?>

  <?php
      if($statuses){
          echo $this->element('timeline');
      }else{
          echo $this->element('friend-not-found');
      }
  ?>
  
</div>
<!-- /#wrap-main -->
