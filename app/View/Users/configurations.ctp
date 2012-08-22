<!-- #wrap-configurations -->
<div id="wrap-configurations">
  
  <h1><i class="icon-hdd"></i><span>データ管理</span></h1>

  <!-- .wrap-profile -->
  <div class="wrap-profile">
        
    <div class="area-profile-image">
      <img class="profile-image-url-https" src="<?php echo str_replace('_normal','_reasonably_small',$loggingUser['Twitter']['profile_image_url_https']); ?>" />
    </div>
    
    <div class="area-center">
      <div class="area-names">

	<p class="name"><?php echo $loggingUser['Twitter']['name'];?></p>

	<p>@<span class="screen-name"><?php echo $loggingUser['Twitter']['screen_name'];?></span></p>

      </div>
    
      <div class="area-updated">
          <p>最終更新 : <span class="updated-date"><?php echo $profile_updated_time;?></span></p>
      </div>
    
      <div class="area-result">
	<span class="alert"></span>
      </div>

    </div>
    <!-- /.area-center -->
    
    <div class="area-button">
      <button id="update-profile" class="btn btn-success" data-loading-text="読み込み中"><i class="icon-refresh"></i>更新</button>

    </div>

  </div>
  <!-- /.wrap-profile -->

  <table class="table">
<!--  
    <thead>
      <tr>
	<th></th>
	<th>保存件数</th>
	<th>更新時刻</th>
	<th></th>
      </tr>
    </thead>
-->
    <tbody>

      <tr class="tweets">
	<td class="head">ツイート</td>
	<td class="count">
	  <span class="total-num"><?php echo $count_statuses;?></span>件
	  <span class="additional-num"></span>
	</td>
	<td class="last-update"><span class="date"><?php echo $status_updated_time;?></span></td>
	<td class="button">
	  <button id="update-statuses" class="btn btn-success" data-loading-text="読み込み中"><i class="icon-refresh icon-white"></i>更新</button>
	</td>
      </tr>
      
      <tr class="friends">
	<td class="head">フォローリスト</td>
	<td class="count">
	  <span class="total-num"><?php echo $count_friends;?></span>件
	  <span class="additional-num"></span>
	</td>
	<td class="last-update"><span class="date"><?php echo $friend_updated_time;?></span></td>
	<td class="button"><button id="update-friends" class="btn btn-success" data-loading-text="読み込み中">
	    <i class="icon-refresh icon-white"></i>更新</button>
	</td>
      </tr>

    </tbody>      

  </table>
  
  <div class="wrap-delete-button">
    <button class="btn btn-danger" href="#modal-delete-account" data-toggle="modal"><i class="icon-trash icon-white"></i>アカウントを削除する</button>
  </div>
  
</div>
<!-- /#wrap-configurations -->

<?php
    echo $this->element('modal-delete-account');
?>
