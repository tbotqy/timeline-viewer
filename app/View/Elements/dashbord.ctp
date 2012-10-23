<?php
  /*
   * left area of each screen to show each kind of timeline.
   * contains navigation to switch timeline type, list of date with all the statuses user has.
   */
?>

<!-- #wrap-dashbord -->
<div id="wrap-dashbord" data-type="<?php echo $actionType;?>">
  <!-- .inner -->
  <div class="inner">

    <div class="space"></div>

    <!-- #wrap-term-selectors -->
    <div id="wrap-term-selectors">
    
      <!-- #wrap-list-years -->
      <div id="wrap-list-years">
	<ul class="list-years my-btn-group">
	  <?php
            $max = count($date_list);
            $count = 0;
	     ?>
	  <?php foreach($date_list as $year=>$val):?>
	  <li data-date="date-<?php echo $year;?>"><a class="btn <?php if($max==1){echo 'first last';}elseif($count++ == 0){echo 'first';}elseif($count == $max){echo 'last';}else{ echo 'mid';}?>" data-date-type="year" href="<?php echo $this->Link->removeNumParam($this->Html->url(null,false));?>/<?php echo $year;?>" data-date="<?php echo $year;?>" data-complete-text="<?php echo $year;?>"> <?php echo $year;?></a></li>
	    <?php endforeach;?>
	</ul>
      </div>
      <!-- /#wrap-list-years -->
      
      <!-- #wrap-list-months -->
      <div id="wrap-list-months">
	<?php foreach($date_list as $year=>$months):?>
	<ul class="list-months date-<?php echo $year;?> my-btn-group">
	  <?php
                $max = count($months);
            $count = 0;
      ?>
	  <?php foreach($months as $month=>$val):?>
	  <li data-date="date-<?php echo $year.'-'.$month;?>"><a data-date-type="month" href="<?php echo $this->Link->removeNumParam($this->Html->url(null,false));?>/<?php echo $year.'-'.$month;?>" data-date="<?php echo $year.'-'.$month;?>" class="btn <?php if($max==1){echo 'first last';}elseif($count++ == 0){echo 'first';}elseif($count == $max){echo 'last';}else{ echo 'mid';}?>" data-complete-text="<?php echo $month;?>"><?php echo $month;?></a></li>
	  <?php endforeach;?>
	</ul>
	<?php endforeach;?>
      </div>
      <!-- /#wrap-list-months -->

      <!-- #wrap-list-days -->
      <div id="wrap-list-days">
	<?php foreach($date_list as $year=>$months):?>
	<?php foreach($months as $month=>$days):?>
	<ul class="list-days date-<?php echo $year.'-'.$month;?> my-btn-group">
	  <?php
                $max = count($days);
            $count = 0;
	     ?>

	  <?php foreach($days as $day=>$sum):?>

	  <li data-date="date-<?php echo $year.'-'.$month.'-'.$day;?>"><a data-date-type="day" href="<?php echo $this->Link->removeNumParam($this->Html->url(null,false));?>/<?php echo $year.'-'.$month.'-'.$day;?>" data-date="<?php echo $year.'-'.$month.'-'.$day;?>" class="btn <?php if($max==1){echo 'first last';}elseif($count++ == 0){echo 'first';}elseif($count == $max){echo 'last';}else{ echo 'mid';}?>" data-complete-text="<?php echo $day;?>"><?php echo $day;?></a></li>
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
  
