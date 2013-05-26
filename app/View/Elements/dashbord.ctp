<?php
  if($isDebug){
      $now = time();
      echo $this->Html->script('/js/unpacked/popstate.js?t='.$now,array('inline'=>false));
      echo $this->Html->script('/js/unpacked/get_dashbord.js?t='.$now,array('inline'=>false));
  }else{
      echo $this->Html->script('popstate.js?v=1',array('inline'=>false));
      echo $this->Html->script('get_dashbord.js?v=1369568580',array('inline'=>false));
  }
?>
<!-- #wrap-dashbord -->
<div id="wrap-dashbord" data-type="<?php echo $actionType;?>">
</div>
<!-- /#wrap-dashbord -->

