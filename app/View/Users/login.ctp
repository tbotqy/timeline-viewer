<div id="wrapperLogin">
  <div class="welcome">
    <span> (some text comes here) </span>
    <div class="wrapper_btn_auth">
      <?php
        echo $this->Html->link('Sign in with Twitter',array('controller'=>'users','action'=>'authorize'),array('class'=>'btn_auth'));
      ?>
    </div>
  </div>
</div>
