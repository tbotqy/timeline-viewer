<!-- #wrap-import -->
<div id="wrap-import">
  
  <!-- .wrap-upper -->
  <div class="wrap-upper">
    <h2>タイムラインを取り込む</h2>

    <h4>
      <a href="https://twitter.com/<?php echo $screen_name;?>" target="_blank">
	<img width="30" src="/img/twitter-bird-light-bgs.png"/>
	@<?php echo $screen_name;?></a>のツイートを
      <button id="start-import" data-loading-text="保存中..." data-complete-text="取得完了" class="btn btn-success"><i class="icon-random icon-white"></i>取り込み開始</button><img class="loader" src="/img/ajax-loader.gif" />
    </h4>
  </div>

  <!-- .wrap-progress-bar -->
  <div class="wrap-progress-bar" style="display:none;">
    <input type="hidden" id="statuses-count" value="<?php echo $statuses_count;?>" />

    <div class="progress progress-striped progress-primary active">
      <div class="bar" style="width: 0%;height: 30px;"></div>
    </div>
    
    <p class="total"></p>

  </div>
  <!-- /.wrap-progress-bar -->
    

  <!-- /.wrap-upper -->
  
  <!-- .wrap-lower -->
  <div class="wrap-lower">
        
    <!-- .wrap-importing-status -->
    <div class="wrap-importing-status" style="display:none;">

      <!-- .wrap-tweet -->
      <div class="wrap-tweet">
	<div class="inner">
	  <p class="body"></p>
	  <p class="date"></p>	
	</div>
	<div class="triangle"></div>
      </div>
      <!-- /.wrap-tweet -->

      <!-- .wrap-profile-image -->
      <div class="wrap-profile-image">
	<div class="profile-image">
	  <div class="viewport">
            <a href="https://twitter.com/<?php echo $screen_name; ?>"><img src="<?php echo $profile_image_url_https;?>" alt="<?php echo $screen_name; ?>" /></a>
	  </div>
	</div>
      </div>
      <!-- /.wrap-profile-image -->   
            
    </div>
    <!-- /.wrap-importing-status -->

  </div>
  <!-- /.wrap-lower -->
  
</div>
<!-- /#wrap-import" -->
