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
	    <?php if($loggedIn):?>
	    
	    <?php if(!$userIsInitialized):?>	      
	    
	    <!-- .nav .pull-left -->
	    <ul class="nav pull-left">
	      <li class="dropdown">
		<a class="dropdown-toggle link-timeline" data-toggle="dropdown" href="#">タイムライン<b class="caret"></b></a>
		
		<ul class="dropdown-menu">
		  <li><a href="/users/sent_tweets">ツイート</a></li>
		  <li><a href="/users/home_timeline">ホームタイムライン</a></li>
		</ul>
	      </li>
	    </ul>
	    <!-- /.nav .pull-left -->
	    <?php endif;?>
	    
	    <!-- .nav .pull-right -->
	    <ul class="nav pull-right">
	      <li class="twitter-profile">
		<a href="https://twitter.com/<?php echo $loggingUser['Twitter']['screen_name'];?>" target="_blank">
		  <img width="20" src="<?php echo $loggingUser['Twitter']['profile_image_url_https'];?>" />
		</a>
                <a href="https://twitter.com/<?php echo $loggingUser['Twitter']['screen_name'];?>" target="_blank">
		  @<?php echo $loggingUser['Twitter']['screen_name'];?>
		</a>
	      </li>
	      <li>
		<a class="link-config" href="/users/configurations"><i class="icon-cog"></i></a>
	      </li>
	      <li class="divider-vertical"></li>
	      <li>
		<a class="pull-right link-logout" href="/users/logout">ログアウト</a>
	      </li>
	    </ul>
	    <!-- /.nav .pull-right -->
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
