<!-- #wrap-login -->
<div id="wrap-login">

  <!-- .upper -->
  <div class="upper">
    <header>
      <h1 class="brand"><span>Timedline</span><span class="version">beta</span></h1>
      <p class="catch">あの日のタイムラインを眺められるちょっとしたアプリケーション</p>

    <a href="/public_timeline" target="_self" class="btn btn-primary">パブリックタイムラインを見る</a>
    </header>
  </div>
  <!-- /.upper -->
  
  <!-- .lower -->
  <div class="lower">
    
    <section>
      
      <div class="remark">
	Timedlineは<a href="#modal-how-data-are-treated" data-toggle="modal">あなたのTwitterアカウントと連携</a>します
      </div>
      
      <div class="wrap-btn-auth">
	<a href="/twitter/authorize" class="btn-auth">Sign in with Twitter</a>
      </div>
      
    </section>
    
  </div>
  <!-- /.lower -->
  
</div>
<!-- /#wrap-login -->

<?php
    echo $this->element('modal-how-data-are-treated');
?>
