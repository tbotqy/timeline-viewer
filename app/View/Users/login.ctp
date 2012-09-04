<!-- #wrap-login -->
<div id="wrap-login">

  <!-- .upper -->
  <header class="upper">
    <div class="brand">
      <h1 class="name">Timedline</h1>
      <span class="version">beta</span>
    </div>
    <p class="catch">あの日のタイムラインを眺められるちょっとしたアプリケーション</p>
  </header>
  <!-- /.upper -->
  
  <div class="middle">
    <div class="explanation-box caramel">
      Great explanation comes here..
    </div>
  </div>

  <!-- .lower -->
  <div class="lower">
    
    <div class="remark">
      Timedlineは<a href="#modal-how-data-are-treated" data-toggle="modal">あなたのTwitterアカウントと連携</a>します
    </div>
    
    <div class="wrap-btn-auth">
      <a href="/twitter/authorize" class="btn-auth">Sign in with Twitter</a>
    </div>
  
  </div>
  <!-- /.lower -->
  
</div>
<!-- /#wrap-login -->

<?php
    echo $this->element('modal-how-data-are-treated');
?>
