<?php
  /*
   * left area of each screen to show each kind of timeline.
   * contains navigation to switch timeline type, list of date with all the statuses user has.
   */
?>
<div id="wrap-dashbord">
  <!-- #timeline-type-list -->
  <nav id="timeline-type-list">
    <ul>
      <li>ツイート</li>
      <li>ホーム</li>
      <li>リスト</li>
    </ul>
  </nav>
  <!-- /#timeline-type-list -->
  
  <!-- #date-list -->
  <nav id="date-list">
    <!-- .wrap-years -->
    <ul class="wrap-years">
      <?php foreach($sum_by_day as $year => $months):?>
      <!-- each year -->
      <li class="wrap"><span class="toggle">▶</span>
	<a class="year" name="<?php echo $year;?>" href="/users/sent_tweets/<?php echo $year;?>"><?php echo $year."年";?></a>
	<!-- .wrap-months -->
	<ul class="wrap-months box-for-toggle">
	  <?php foreach($months as $month => $days):?>
	  <!-- each month -->
	  <li class="wrap"><span class="toggle">▶</span>
	    <a class="month" name="<?php echo $year.'-'.$month;?>" href="/users/sent_tweets/<?php echo $year;?>/<?php echo $month;?>"/><?php echo $month."月";?></a>
            <!-- .wrap-days -->
	    <ul class="wrap-days box-for-toggle">
	      <?php foreach($days as $day => $sum):?>
	      <!-- each day -->
	      <li class="link">
	        <a class="day" name="<?php echo $year.'-'.$month.'-'.$day; ?>" href="/users/sent_tweets/<?php echo $year;?>/<?php echo $month;?>/<?php echo $day;?>"><?php echo $day."日";?></a>
		<span class="status-sum"><?php echo $sum;?></span>
	      </li>
	      <!-- /each day -->
	      <?php endforeach;?>
	    </ul>
	    <!-- /.wrap-days /.box-for-toggle -->
          </li>
          <!-- /each month -->
          <?php endforeach;?>
        </ul>
        <!-- /.wrap-months /.box-for-toggle -->
      </li>
      <!-- /each year -->
      <?php endforeach;?>
    </ul>
    <!-- /.wrap-years -->
  </nav>
  <!-- /#date-list -->
</div>
<!-- /#wrap-dashbord -->
