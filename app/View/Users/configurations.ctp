<?php
echo $this->Html->css('header-small',null,array('inline'=>false));
?>

<!-- #wrap-configurations -->
<div id="wrap-configurations">
  
  <h1><i class="icon-hdd"></i><span>データ管理</span></h1>
   
  <table class="table">
  
    <thead>
      <tr>
	<th></th>
	<th>保存件数</th>
	<th>更新時刻</th>
	<th></th>
      </tr>
    </thead>

    <tbody>

      <tr class="tweets">
	<td>ツイート</td>
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
	<td>フォローリスト</td>
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
