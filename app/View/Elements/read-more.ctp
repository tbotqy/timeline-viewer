<?php
  echo $this->Html->css('element.read-more',null,array('inline'=>false));
?>

<!-- #wrap-read-more -->
<div id="wrap-read-more">

  <?php
     if($hasNext):
  ?>
  <input type="hidden" id="action-type" value="<?php echo $actionType;?>"/>
  <button id="read-more" data-loading-text="loading" data-complete-text="続きを読み込む" class="btn">続きを読み込む</button>
  <?php
      else:
     ?>    
  <div class="alert alert-info">
    <p>最後のつぶやきです</p>
  </div>
  <?php
       endif;
   ?>
</div>
<!-- /#wrap-read-more -->
