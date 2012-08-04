<!-- #wrap-login -->
<div id="wrap-login">

  <header>
    <h1 class="brand">Timedline</h1><span class="version">beta</span>
    <p class="catch">あの日のタイムラインを眺められるちょっとしたアプリケーション</p>
  </header>

  <section>
    <div class="remark">
      Timedlineは<a href="#modal-how-data-are-treated" data-toggle="modal">あなたのTwitterアカウントと連携</a>します
    </div>
      
    <div class="wrap-btn-auth">

      <a href="/twitter/authorize" class="btn-auth">Sign in with Twitter</a>
    
    </div>
  </section>

</div>
<!-- /#wrap-login -->

<?php
    echo $this->element('modal-how-data-are-treated');
?>
