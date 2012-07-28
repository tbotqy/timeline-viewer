<?php
  echo $this->Html->css('header-small',null,array('inline'=>false));
  echo $this->Html->script('admin',array('inline'=>false));
?>
<!-- #wrap-main -->
<div id="wrap-admin">
  <h1>アカウントのクリーンアップ</h1>
  <?php if($gone_users):?>
  <table class="table table-bordered table-condensed">

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
  <?php else:?>

  <div class="alert alert-info">
    クリーンアップするアカウントはありません。
  </div>
  <?php endif;?>

</div>
<!-- /#wrap-admin -->
