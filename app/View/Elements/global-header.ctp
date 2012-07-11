<!-- #container-header -->
<div id="container-header">
  <header>
    
    <div class="navbar navbar-fixed-top">
      <nav>
	<!-- .navbar-inner -->
	<div class="navbar-inner">
	  
	  <!-- .container -->
	  <div class="container">
	    <a  class="brand" href="/">TimedLineβ</a>
	    <?php if($loggedIn):?>
	    
	    <?php if($userIsInitialized):?>	      
	    
	    <!-- .nav .pull-left -->
	    <ul class="nav pull-left">
	      <li class="dropdown">
		<a class="dropdown-toggle link-timeline" data-toggle="dropdown" href="#">  <i class="icon-comments-alt"></i>  <b class="caret"></b></a>
		
		<ul class="dropdown-menu">
		  <li><a href="/users/sent_tweets">ツイート</a></li>
		  <li><a href="/users/home_timeline">ホームタイムライン</a></li>
		  <li class="divider"></li>
		  <li><a href="/users/public_timeline">パブリックタイムライン</a></li>
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
	      
	      <li class="dropbown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-caret-down"></i></a>
	       
		<!-- .dropdown-menu -->
		<ul class="dropdown-menu">
		  <?php if($userIsInitialized):?>
		  <li>
		    <a class="link-config" href="/users/configurations"><i class="icon-cog"></i>設定</a>
		  </li>
		  <li class="divider"></li>
		  <?php endif;?>
		  <li>
		    <a class="link-logout" href="/users/logout">ログアウト</a>
		  </li>
		</ul>
		<!-- /.dropdown-menu -->
	      
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
