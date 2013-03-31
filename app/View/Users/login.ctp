<!-- #wrap-login -->
<div id="wrap-login">

  <!-- .upper -->
  <header class="upper caramel">
    <div class="brand">
      <h1 class="name">Timedline</h1>
      <span class="version">beta</span>
    </div>
    <div class="sub-title">
      
    </div>
  </header>
  <!-- /.upper -->

  <!-- .lower -->
  <div class="lower caramel">
    
    <!-- .inner -->
    <div class="inner">

      <!-- .lower .left -->
      <div class="left">
	
	<div class="remark">
	  <h3>つぶやきから分かる、あの日の出来事</h3>
	
	  <div class="description">
	    <p>人々がつぶやいた事を日付を指定して振り返る事ができます</p>
	    <span class="info alert alert-info"><a href="/statuses/sum">現在の共有ツイート数 : <?php echo $totalStatusNum;?>&nbsp;&nbsp;<i class="icon-share"></i></a></span>
	    
	  </div>
	
	</div>
	
	<div class="wrap-btn-public">
	  <a href="/public_timeline" class="link btn btn-primary" data-loading-text="パブリックタイムラインを見てみる">パブリックタイムラインを見てみる</a>
	</div>
	<script>
	  (function(){
	    $(".link.btn").click(function(){
	      $(this).button("loading");
	    });
	  })();
	</script>
	
      </div>
      <!-- /.lower .left -->
      
      <!-- .lower .right -->
      <div class="right">
	
	<div class="remark">
	  <h3>あの日の自分を振り返る</h3>
	  <div class="description">
	    <p>サインインすれば、あなたやあなたがフォローしている人たちによる<br/><span class="lower-line"> タイムラインを振り返ることもできます</span></p>
	  </div>
	</div>

	<div class="wrap-btn-auth">
	  <a href="/twitter/authorize" class="link btn-auth">Sign in with Twitter</a>
	</div>
	
	<div class="readme">
	  <a  href="#modal-how-data-are-treated" data-toggle="modal">※ご利用に際して</a>
	</div>
	
      </div>
      <!-- /.lower .right -->
      
    </div>
    <!-- /.inner -->
  </div>
  <!-- /.lower -->
  
</div>
<!-- /#wrap-login -->

<?php
    echo $this->element('modal-how-data-are-treated');
?>
