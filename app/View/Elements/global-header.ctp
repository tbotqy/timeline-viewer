<!-- #container-header -->
<div id="container-header">
  <header>
    
    <div class="navbar navbar-fixed-top">
      <nav>
	<!-- .navbar-inner -->
	<div class="navbar-inner">
	  
	  <!-- .container -->
	  <div class="container">
	    <a  class="brand" href="/">TimedLine</a>
	    
	    <!-- .nav -->
	    <ul class="nav">

	      <li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">タイムライン<b class="caret"></b></a>

		<ul class="dropdown-menu">
		  <li><a href="/users/sent_tweets">ツイート</a></li>
		  <li><a href="/users/home_timeline">ホームタイムライン</a></li>
		</ul>

	      </li>

	      <li>
		<a href="#">プロフィール</a>
	      </li>

	    </ul>
	    <!-- /.nav -->
	    <?php if($loggedIn):?>
	    <p class="navbar-text">
	      <?php
                echo $this->Html->link('ログアウト',array('controller'=>'users','action'=>'logout'),array('class'=>'pull-right'));
                
	      ?>
	    </p>
	    <?php endif;?>
	  </div>
	  <!-- /.container -->

	</div>
	<!-- /.navbar-inner -->
      </nav>

    </div>
    <!-- /.navbar -->
  </header>

</div>
<!-- /#global-header -->
