<!-- #global-header -->
<div id="container-header">
  <header>
    <div class="navbar navbar-fixed-top">
      <nav>
	<div class="navbar-inner">
	  <div class="container">
	    <a  class="brand" href="/">timedline</a>
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
	    <p class="navbar-text">
	      <?php
                echo $this->Html->link('ログアウト',array('controller'=>'users','action'=>'logout'),array('class'=>'pull-right'));
	      ?>
	    </p>
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
