<?php
  /*
   * left area of each screen to show each kind of timeline.
   * contains navigation to switch timeline type, list of date with all the statuses user has.
   */
?>
<!-- #wrap-dashbord -->
<div id="wrap-dashbord">
  <!-- .inner -->
  <div class="inner">
    <div class="toggle">
      <a href="#"><i class="icon-eject"></i></a>
    </div>
    <!-- #wrap-mode-nav" -->
    <div id="wrap-timeline-mode-nav">
      <nav>
	<ul class="nav nav-pills">
	  <li class="active"><a>You</a></li>
	  <li><a>Following</a></li>
	</ul>
      </nav>
    </div>
    <!-- /#wrap-timeline-mode-nav -->
    <!-- #wrap-term-selectors -->
    <div id="wrap-term-selectors">
    
      <!-- "wrap-list-years -->
      <div id="wrap-list-years">
	<ul class="list-years">
	  <?php foreach($date_list as $year=>$val):?>
            <li data-date="date-<?php echo $year;?>"><a data-date-type="year" href="/users/sent_tweets/<?php echo $year;?>" data-date="<?php echo $year;?>" class="btn" data-complete-text="<?php echo $year;?>"> <?php echo $year;?></a></li>
	    <?php endforeach;?>
	</ul>
      </div>
      <!-- /#wrap-list-years -->
      
      <!-- #wrap-list-months -->
      <div id="wrap-list-months">
	<?php foreach($date_list as $year=>$months):?>
	<ul class="list-months date-<?php echo $year;?>">
	  <?php foreach($months as $month=>$val):?>
	  <li data-date="date-<?php echo $year.'-'.$month;?>"><a data-date-type="month" href="/users/sent_tweets/<?php echo $year.'-'.$month;?>" data-date="<?php echo $year.'-'.$month;?>" class="btn" data-complete-text="<?php echo $month;?>"><?php echo $month;?></a></li>
	  <?php endforeach;?>
	</ul>
	<?php endforeach;?>
      </div>
      <!-- /#wrap-list-months -->

      <!-- #wrap-list-days -->
      <div id="wrap-list-days">
	<?php foreach($date_list as $year=>$months):?>
	<?php foreach($months as $month=>$days):?>
	<ul class="list-days date-<?php echo $year.'-'.$month;?>">
	  <?php foreach($days as $day=>$sum):?>
	  <li data-date="date-<?php echo $year.'-'.$month.'-'.$day;?>"><a data-date-type="day" href="/users/sent_tweets/<?php echo $year.'-'.$month.'-'.$day;?>" data-date="<?php echo $year.'-'.$month.'-'.$day;?>" class="btn" data-complete-text="<?php echo $day;?>"><?php echo $day;?></a></li>
	  <?php endforeach;?>
	</ul>
	<?php endforeach;?>
	<?php endforeach;?>
      </div>
      <!-- /#wrap-list-days -->
      
    </div>
    <!-- /#wrap-term-selectors -->
    
  </div>
  <!-- /.inner -->
</div>
<!-- /#wrap-dashbord -->
  
