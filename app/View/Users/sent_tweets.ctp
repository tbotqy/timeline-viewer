<?php
$this->Html->css('users.sent_tweets',null,array('inline'=>false));
?>

<!-- #date-list -->
<nav id="date-list">
  
  <ul class="wrap-years">

    <?php foreach($sum_by_day as $year => $months):?>
    
    <!-- each year -->

    <li class="wrap"><span class="toggle">▶</span><?php echo $year."年";?>
      
      <!-- .wrap-months -->
      <ul class="wrap-months box-for-toggle">
	
	<?php foreach($months as $month => $days):?>
	<!-- each month -->
	<li class="wrap"><span class="toggle">▶</span><?php echo $month."月";?>

	  <!-- .wrap-days -->
	  <ul class="wrap-days box-for-toggle">

	    <?php foreach($days as $day => $sum):?>

	    <!-- each day -->
	    <li class="no-wrap ajax">
	      <a href="#">
		<?php echo $day."日";?>
	      </a>
	      <span class="status-sum"><?php echo $sum;?></span>
	    </li>
	    <!-- /each day -->
	    
	    <?php endforeach;?>
	  
	  </ul>
	  <!-- /.wrap-days /.box-for-toggle -->
	  
	</li>
	<!-- each month -->
	<?php endforeach;?>
      </ul>
      <!-- /.wrap-months /.box-for-toggle -->
   
    </li>
    <!-- /each year -->
    <?php endforeach;?>
  </ul><!-- /.wrap-years -->
  
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
