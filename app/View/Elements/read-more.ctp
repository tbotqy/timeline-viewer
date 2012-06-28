<!-- #wrap-read-more -->
<div id="wrap-read-more">

  <?php
     if($hasNext):
  ?>

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
