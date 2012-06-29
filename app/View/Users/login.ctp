<!-- #wrap-login -->
<div id="wrap-login">
  
  <!-- .wrap-explain -->
  <div class="wrap-explain">

    <h1 class="brand">TimedLine</h1>
    <p>is a simple view system for those who want to have the access to their past tweets.</p>
    
    <div class="wrap-btn-auth">
      <?php
        echo $this->Html->link('Sign in with Twitter',array('controller'=>'users','action'=>'authorize'),array('class'=>'btn-auth'));
      ?>
    </div>

  </div>
  <!-- /.wrap-explain -->

</div>
<!-- /#wrap-login -->
