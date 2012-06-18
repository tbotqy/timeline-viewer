<?php
  echo $this->Html->css('users.sent_tweets',null,array('inline'=>false));
  echo $this->Html->script('users.sent_tweets',array('inline'=>false));
?>

<?php echo $this->element('dashbord');?>

<!-- #wrap-main -->
<div id="wrap-main">
  <!-- #wrap-timeline -->
  <div id="wrap-timeline">

    <?php echo $this->element('each-status'); ?>
      <input type="hidden" id="last-status-id" value="<?php echo $last_status_id;?>" />
      <div class="cover"><span>Loading</span></div>
      <div class="land-mark"></div>
  </div>
  <!-- /#wrap-timeline -->
  <?php echo $this->element('read-more');?>

</div>
<!-- /#wrap-main -->
