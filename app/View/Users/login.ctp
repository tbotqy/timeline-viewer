<!-- #wrap-login -->
<div id="wrap-login">

  <!-- .upper -->
  <header class="upper caramel">
    <div class="brand">
      <h1 class="name">Timedline</h1>
      <span class="version">beta</span>
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
	  <h3>Jump to that day</h3>
	
	  <div class="description">
	    <p>Timedlineでは、日付を指定してタイムラインをさかのぼる事ができます</p>
     <span class="info alert alert-info">現在の共有ツイート数 : <?php echo $totalStatusNum;?></span>
	  </div>
	
	</div>
	
	<div class="wrap-btn-public">
	  <a href="/public_timeline" class="link btn btn-primary">パブリックタイムラインを見てみる</a>
	</div>
	
      </div>
      <!-- /.lower .left -->
      
      <!-- .lower .right -->
      <div class="right">
	
	<div class="remark">
	  <h3>Look back what you said</h3>
	  <div class="description">
	    <p>サインインすれば、あなたやあなたがフォローしている人たちによる<br/><span class="lower-line"> タイムラインを振り返ることもできます</span></p>
	  </div>
	</div>

	<div class="wrap-btn-auth">
	  <a href="/twitter/authorize" class="link btn-auth">Sign in with Twitter</a>
	</div>
	
	<div class="readme">
	  <a  href="#modal-how-data-are-treated" data-toggle="modal">利用に際して</a>
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
