<?php
  $dir = $isDebug ? "/js/unpacked/" : "";
  echo $this->Html->script($dir.'admin',array('inline'=>false));
?>
<!-- #wrap-main -->
<div id="wrap-admin">


  <?php if($active_users):?>
  <!-- .area-active-users -->
  <div class="area-active-users">
      <h1><a class="toggler" data-dest-content-type="active-users" href="#">アクティブユーザー一覧 (<?php echo count($active_users);?>人)</a></h1>
    <!-- .content -->
    <div class="content">
    <table class="table table-bordered table-condensed table-active-users">

      <thead>
	<tr>
	  <th class="centroid">id</th>
	  <th>screen name</th>
	  <th>created</th>
	  <th>updated</th>
	</tr>
      </thead>
      
      <tbody>

	<?php foreach($active_users as $user):?>
    
	<tr>
	  <td class="centroid"><?php echo $user['User']['id'];?></td>
          <td><?php echo $user['User']['screen_name'];?></td>
          <td><?php echo $this->Html->convertTimeToDate($user['User']['created'],32400);?></td>
          <td><?php echo $this->Html->convertTimeToDate($user['User']['updated'],32400);?></td>
	</tr>
	
	<?php endforeach;?>
      
      </tbody>
    
    </table>
    </div>
    <!-- .content -->
  </div>
  <!-- /.area-active-users -->
  <?php endif;?>

  <!-- .area-gone-users -->
  <div class="area-gone-users">
    <h1><a class="toggler" data-dest-content-type="gone-users" href="#">アカウントのクリーンアップ</a></h1>
    <?php if($gone_users):?>
    <!-- .content -->
    <div class="content">
    <table class="table table-bordered table-condensed table-gone-users">

      <thead>
	<tr>
	  <th></th>
	  <th class="centroid">id</th>
	  <th>screen name</th>
	  <th>updated</th>
	  <th></th>
	</tr>
      </thead>

      <tbody>
	
	<?php foreach($gone_users as $user):?>
	
	<tr data-dest-id="<?php echo $user['User']['id'];?>">
	  <td class="chk">
	    <button class="btn" data-toggle="button" data-dest-id="<?php echo $user['User']['id'];?>"><i class="icon-ok"></i></button>
	  </td>
          <td class="centroid"><?php echo $user['User']['id'];?></td>
          <td><?php echo $user['User']['screen_name'];?></td>
          <td><?php echo $this->Html->convertTimeToDate($user['User']['updated'],32400);?></td>
	  <td><button class="btn btn-danger delete-each" data-dest-id="<?php echo $user['User']['id'];?>"><i class="icon-trash"></i>削除</button> <img class="loader" src="/img/ajax-loader.gif" /></td>
	</tr>
	
	<?php endforeach;?>
      
      </tbody>
    
    </table>
  
    <div class="wrap-button">
      <button class="btn btn-danger" id="delete-selected"><i class="icon-trash"></i>Delete selected</button>
      <img class="loader" src="/img/ajax-loader.gif" />
    </div>
    </div>
    <!-- /.content -->
    <?php else:?>

    <div class="alert alert-info">
      クリーンアップするアカウントはありません。
    </div>
    <?php endif;?>
  </div>
  <!-- /.area-gone-users -->

</div>
<!-- /#wrap-admin -->
