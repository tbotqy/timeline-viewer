<?php

  /**
   * the params required to present this element are...
   *  the data belongs to User and Status model
   *  User musts be contained in value $user_data,
   *  Status musts be contained in value $status
   */
?>
<div class="wrap-one-result">

<?php
     $loopCnt = 0;
?>

<?php foreach($statuses as $status):?>

<?php
  if($status['Status']['is_retweet']){
      $retweeterName = $status['User']['name']; 
      $status['User']['name'] = $status['Status']['rt_name'];
      $status['User']['screen_name'] = $status['Status']['rt_screen_name'];
      $status['User']['profile_image_url_https'] = $status['Status']['rt_profile_image_url_https'];
      $status['Status']['text'] = $status['Status']['rt_text'];
      $status['Status']['cource'] = $status['Status']['rt_source'];
      $status['Status']['created_at'] = $status['Status']['rt_created_at'];
  }
   ?>

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
      <span class="logo">
	<img src="/img/twitter-bird-light-bgs.png" alt="Twitterブランド" />
      </span>      
      <span class="name">
          <a href="https://twitter.com/<?php echo $status['User']['screen_name'];?>" target="_blank"><?php echo $status['User']['name'];?></a>
      </span>
      <span class="screen-name">
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
                echo $this->Link->addLinks($status['Status']['text'],$status['Entity']);
        // echo $this->Link->addLinks($status['Status']['text']);
        
      ?>
    </span>
    <?php if($status['Status']['is_retweet']):?>
    <div class="retweet-info">
      <p><span class="icon">&nbsp;</span><?php echo $retweeterName;?>さんがリツイート</p>
    </div>
    <?php endif;?>
    <!-- .bottom -->
    <span class="bottom">
      <!-- .pull-left -->
      <div class="pull-left">
	<span class="specific-date">
	  <?php echo date('Y年n月j日 - H:i',$status['Status']['created_at']+$status['User']['utc_offset']);?>
	</span>
	<span class="source">
	  <?php echo $status['Status']['source'];?>から
	</span>
	<span class="link-official">
          <a href="https://twitter.com/<?php echo $status['User']['screen_name'];?>/status/<?php echo $status['Status']['status_id_str'];?>" target="_blank">詳細</a>
	</span>
	<!-- .area-intents -->
	<div class="area-intents">
	  <a class="intent intent-reply" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $status['Status']['status_id_str'];?>">&nbsp;</a>
	  <a class="intent intent-retweet" href="https://twitter.com/intent/retweet?tweet_id=<?php echo $status['Status']['status_id_str'];?>">&nbsp;</a>
	  <a class="intent intent-favorite" href="https://twitter.com/intent/favorite?tweet_id=<?php echo $status['Status']['status_id_str'];?>">&nbsp;</a>
	</div>
	<!-- /.area-intents -->
      </div>
      <!-- /.pull-left -->
      
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
<?php
    if( $isInitialRequest && !isset($statuses[$loopCnt+1]) ){
        echo $this->element('adsense468-link-unit');
    }
  $loopCnt++;
?>
<?php endforeach;?>
<?php if(isset($oldest_timestamp)):?>
<input type="hidden" class="oldest-timestamp" value="<?php echo $oldest_timestamp;?>"/>
<?php endif;?>
</div>
