<!-- #wrap-login -->
<div id="wrap-login">

  <header>
    <h1 class="brand">Timedline</h1><span class="version">beta</span>
    <p class="catch">あの日のタイムラインを眺められるちょっとしたアプリケーション</p>
  </header>

  <div class="remark">
    Timedlineは<a href="#modal-how-data-are-treated" data-toggle="modal">あなたのTwitterアカウントと連携</a>します
  </div>
      
  <div class="wrap-btn-auth">
    <?php
      echo $this->Html->link('Sign in with Twitter',array('controller'=>'users','action'=>'authorize'),array('class'=>'btn-auth'));
	?>
  </div>

</div>
<!-- /#wrap-login -->

<?php
    echo $this->element('modal-how-data-are-treated');
?>
