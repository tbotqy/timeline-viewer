<!-- "wrap-list-years -->
<div id="wrap-list-years">
  <ul class="list-years">
    <?php foreach($date_list as $year=>$val):?>
    <li data-date="date-<?php echo $year;?>"><a data-date-type="year" href="/users/<?php echo $actionType;?>/<?php echo $year;?>" data-date="<?php echo $year;?>" class="btn" data-complete-text="<?php echo $year;?>"> <?php echo $year;?></a></li>
      <?php endforeach;?>
  </ul>
</div>
<!-- /#wrap-list-years -->
  
<!-- #wrap-list-months -->
<div id="wrap-list-months">
  <?php foreach($date_list as $year=>$months):?>
  <ul class="list-months date-<?php echo $year;?>">
    <?php foreach($months as $month=>$val):?>
    <li data-date="date-<?php echo $year.'-'.$month;?>"><a data-date-type="month" href="/users/<?php echo $actionType;?>/<?php echo $year.'-'.$month;?>" data-date="<?php echo $year.'-'.$month;?>" class="btn" data-complete-text="<?php echo $month;?>"><?php echo $month;?></a></li>
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
    <li data-date="date-<?php echo $year.'-'.$month.'-'.$day;?>"><a data-date-type="day" href="/users/<?php echo $actionType;?>/<?php echo $year.'-'.$month.'-'.$day;?>" data-date="<?php echo $year.'-'.$month.'-'.$day;?>" class="btn" data-complete-text="<?php echo $day;?>"><?php echo $day;?></a></li>
    <?php endforeach;?>
  </ul>
  <?php endforeach;?>
  <?php endforeach;?>
</div>
<!-- /#wrap-list-days -->
