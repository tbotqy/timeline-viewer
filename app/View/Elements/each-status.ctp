<?php

  /**
   * the params required to present this element are...
   *  the data belongs to User and Status model
   *  User musts be contained in value $user_data,
   *  Status musts be contained in value $status
   */
 
?>

<?php foreach($statuses as $status):?>
<!-- .wrap-each-status -->
<div class="wrap-each-status" 
     <?php
          if($loggedIn && $status['User']['twitter_id'] === $loggingUser['Twitter']['id']):
     ?>
     data-status-id="<?php echo $status['Status']['id'];?>"
  <?php endif;?>
  >
  
  <!-- .profile-image -->
  <div class="profile-image">
    <div class="viewport">
      <a href="https://twitter.com/<?php echo $status['User']['screen_name'];?>" target="_blank"><img src="<?php echo $status['User']['profile_image_url_https'];?>" alt="<?php echo $status['User']['screen_name']; ?>" width="48" height="48"/></a>
    </div>
  </div>
  <!-- /.profile-image -->
  
  <!-- .status-content -->
  <div class="status-content">
    <!-- .top -->      
    <span class="top">
      <span class="name">
          <a href="https://twitter.com/<?php echo $status['User']['screen_name'];?>" target="_blank"><?php echo $status['User']['name'];?></a>
      </span>
      <span class="screen_name">
        <a href="https://twitter.com/<?php echo $status['User']['screen_name'];?>" target="_blank">@<?php echo $status['User']['screen_name'];?></a>
      </span>
      <span class="date">
        <a href="https://twitter.com/<?php echo $status['User']['screen_name'];?>/status/<?php echo $status['Status']['status_id_str'];?>" target="_blank">
	  <?php 
            echo date('Y',time()) > date('Y',$status['Status']['created_at']+$status['User']['utc_offset']) ?
            date('Y年n月j日',$status['Status']['created_at']+$status['User']['utc_offset']) : 
            date('n月j日',$status['Status']['created_at']+$status['User']['utc_offset']);
      ?>
	</a>
      </span>
    </span>
    <!-- /.top -->
    
    <span class="text">
      <?php 
          //      echo $this->Link->addLinks($status['Status']['text'],$status['Entity']);
          echo $this->Link->addLinks($status['Status']['text']);
      ?>
    </span>
    
    <!-- .bottom -->
    <span class="bottom">
      <span class="specific-date">
	<?php echo date('Y年n月j日 - H:i',$status['Status']['created_at']+$status['User']['utc_offset']);?>
      </span>
      <span class="source">
	<?php echo $status['Status']['source'];?>から
      </span>
      <span class="link-official">
          <a href="https://twitter.com/<?php echo $status['User']['screen_name'];?>/status/<?php echo $status['Status']['status_id_str'];?>" target="_blank">詳細</a>
      </span>
      <?php
          if($loggedIn && $status['User']['twitter_id'] === $loggingUser['Twitter']['id']):
      ?>
      <span class="link-delete" data-status-id="<?php echo $status['Status']['id'];?>">
	<a href="#"> <i class="icon-trash"></i> </a>
      </span>
      <?php
           endif;
         ?>
    </span>
    <!-- /.bottom -->
  </div>
  <!-- /.status-content -->
</div><!-- /.wrap-each-status -->
<?php endforeach;?>
<?php if(isset($oldest_timestamp)):?>
<input type="hidden" id="oldest-timestamp" value="<?php echo $oldest_timestamp;?>"/>
<?php endif;?>
