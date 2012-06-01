<?php
$this->Html->script('statuses.import',array('inline'=>false));
?>
<h2 class="init">Retrieve your statuses.</h2>

<div id="areaInit">
  
  <ul id="userInfo" class="init">
     <li class="img"><img src="<?php echo $profile_image; ?>" title="it's you" /></li>
     <li class="sc_name">
       <?php
         echo $this->Html->link($screen_name,"https://twitter.com/#!/".$screen_name);
       ?>
     </li>
  </ul>

  <p>Retrieve your past tweets.This may take several seconds.</p>
  <button id="start" data-loading-text="取得中" data-complete-text="取得完了" class="btn btn-success">取得開始</button>

  <div id="status" style="display:none;">
    <span class="progress">Start</span>
    <p class="text"></p>
    <span class="date"></span>
  </div>
  
</div>
