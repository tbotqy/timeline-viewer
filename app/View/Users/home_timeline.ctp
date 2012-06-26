<?php
  echo $this->Html->css('users.sent_tweets',null,array('inline'=>false));
  echo $this->Html->script('users.sent_tweets',array('inline'=>false));
?>

<?php echo $this->element('dashbord');?>

<!-- #wrap-main -->
<div id="wrap-main">

  <?php
      if($statuses){
          echo $this->element('timeline');
      }else{
          echo $this->element('no-status');
      }
  ?>
  
</div>
<!-- /#wrap-main -->
