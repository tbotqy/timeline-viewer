<?php
  echo $this->Html->script('element.each-status.js',array('inline'=>false));
?>

<?php foreach($statuses as $status): ?>
<!-- .wrap-each-status -->
<div class="wrap-each-status">
  
  <!-- .profile-image -->
  <div class="profile-image">
    <div class="viewport">
      <a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>"><img src="<?php echo $user_data['User']['profile_image_url_https'];?>" alt="<?php echo $user_data['User']['screen_name']; ?>" /></a>
    </div>
  </div>
  <!-- /.profile-image -->
  
  <!-- .status-content -->
  <div class="status-content">
    <!-- .top -->      
    <span class="top">
      <span class="name">
          <a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>"><?php echo $user_data['User']['name'];?></a>
      </span>
      <span class="screen_name">
        <a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>">@<?php echo $user_data['User']['screen_name'];?></a>
      </span>
      <span class="date">
        <a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>/status/<?php echo $status['Status']['status_id_str'];?>">
	  <?php 
            echo date('Y',time()) > date('Y',$status['Status']['created_at']+$user_data['User']['utc_offset']) ?
            date('Y年n月j日',$status['Status']['created_at']+$user_data['User']['utc_offset']) : 
            date('n月j日',$status['Status']['created_at']+$user_data['User']['utc_offset']);
      ?>
	</a>
      </span>
    </span>
    <!-- /.top -->
    
    <span class="text">
      <?php 
          //echo $this->Text->autoLinkUrls($status['Status']['text']);
          echo $this->Link->addLinks($status['Status']['text'],$status['Entity']);
      ?>
    </span>
    
    <!-- .bottom -->
    <span class="bottom">
      <span class="specific-date">
	<?php echo date('Y年n月j日 - H:i',$status['Status']['created_at']+$user_data['User']['utc_offset']);?>
      </span>
      <span class="source">
	<?php echo $status['Status']['source'];?>から
      </span>
      <span class="link-official">
          <a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>/status/<?php echo $status['Status']['status_id_str'];?>">詳細</a>
      </span> 
    </span>
    <!-- /.bottom -->
  </div>
  <!-- /.status-content -->
</div>
<!-- /.wrap-each-status -->
<?php endforeach;?>
