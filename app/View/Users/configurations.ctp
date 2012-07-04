<?php
echo $this->Html->css('header-small',null,array('inline'=>false));
?>

<!-- #wrap-configurations -->
<div id="wrap-configurations">
  
  <h1><i class="icon-cogs"></i><span>データ管理</span></h1>
  
  <table class="table">
  
    <thead>
      <tr>
	<th></th>
	<th>保存してある件数</th>
	<th>最後に更新した日時</th>
	<th></th>
      </tr>
    </thead>

    <tbody>
      <tr class="tweets">
	<td>ツイート</td>
	<td class="count">1203件</td>
	<td class="last-update">2012-5-10 10:23</td>
	<td>
	  <button id="update-statuses" class="btn btn-success" data-loading-text="読み込み中"><i class="icon-refresh icon-white"></i>更新</button>
	</td>
      </tr>
      
      <tr class="friends">
	<td>フォローリスト</td>
	<td class="count">1203件</td>
	<td class="last-update">2012-5-10 10:23</td>
	<td><button id="update-friends" class="btn btn-success" data-loading-text="読み込み中">
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
