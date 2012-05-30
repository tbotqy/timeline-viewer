<?php
$this->Html->css('users.sent_tweets',null,array('inline'=>false));
?>

<!-- #date-list -->
<nav id="date-list">
  <!-- wrap-years -->
  <?php foreach($sum_by_day as $year => $months):?> 
  <ul class="wrap-years">
    <span class="toggle">▼</span><li><?php echo $year."年";?></li>
    <!-- wrap-months -->
    <?php foreach($months as $month => $days):?>
    <ul class="wrap-months">
      <li><?php echo $month."月";?></li>
      <?php foreach($days as $day=>$status_sum):?>
      <ul class="wrap-days">
        <li><?php echo $day."日";?><span class="status-sum"><?php echo $status_sum;?></span></li>
      </ul><!-- /.wrap-days -->
      <?php endforeach;?>  
    </ul><!-- /.wrap-months -->
    <?php endforeach;?>  
  </ul><!-- /.wrap-years -->
  <?php endforeach;?>
</nav><!-- /#date-list -->

<?php foreach($statuses as $status): ?>
<!-- #wrap-each-status -->
<div id="wrap-each-status">
  
  <div class="profile-image">
    <div class="viewport">
      <a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>"><img src="<?php echo $user_data['User']['profile_image_url_https'];?>" alt="<?php echo $user_data['User']['screen_name']; ?>" /></a>
    </div>
  </div>
  <!-- .status-content -->
  <div class="status-content">
    <span class="top"><!-- top -->
      <span class="name">
	<a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>"><?php echo $user_data['User']['name'];?></a>
      </span>
      <span class="screen_name">
	<a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>">@<?php echo $user_data['User']['screen_name'];?></a>
      </span>
      <span class="date">
        <a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>/status/<?php echo $status['Status']['status_id_str'];?>"><?php 
            echo date('Y',time()) > date('Y',$status['Status']['created_at']+$user_data['User']['utc_offset']) ?
            date('Y年n月j日',$status['Status']['created_at']+$user_data['User']['utc_offset']) : 
            date('n月j日',$status['Status']['created_at']+$user_data['User']['utc_offset']);
    ?>
	</a>
      </span>
    </span><!-- /.top -->
    <span class="text">
     <?php echo $status['Status']['text'];?>
    </span>
    <!-- .bottom -->
    <span class="bottom">
      <span class="specific-date">
	<?php echo date('Y年n月j日 - h:m',$status['Status']['created_at']+$user_data['User']['utc_offset']);?>
      </span>
      <span class="source">
	<?php echo $status['Status']['source'];?>から
      </span>
      <span class="link-official">
	<a href="https://twitter.com/<?php echo $user_data['User']['screen_name'];?>/status/<?php echo $status['Status']['status_id_str'];?>">詳細</a>
      </span> 
    </span><!-- /.bottom -->
  </div><!-- /.status-content -->
  
</div><!-- /#wrap-each-status -->
<?php endforeach; ?>
