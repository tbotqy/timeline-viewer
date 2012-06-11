<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <?php
      echo $this->Html->css('common');
      echo $this->Html->script('libs/jquery-1.7.2.min.js');
      echo $this->Html->script('libs/bootstrap.js');
      echo $this->Html->script('libs/bootstrap-button.js');
      echo $this->Html->script('libs/bootstrap-tab.js');
      echo $this->Html->script('common.js');
      echo $this->fetch('meta');
      echo $this->fetch('css');
      echo $this->fetch('script');
    ?>
    <title><?php echo $title_for_layout; ?></title>
  </head>
  <body>
    
    <!-- #global-header -->
    <div id="container-header">
      <header>
	<div class="navbar navbar-fixed-top">
	  <nav>
	    <div class="navbar-inner">
	      <div class="container">
		<a  class="brand" href="/">timedline</a>
		<ul class="nav">
		  <li class="active">
		    <a href="/users/sent_tweets">タイムライン</a>
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
    
    <?php //echo $this->element('sql_dump');?>
    
    <div id="container-main">
      <div class="inner">
    <?php
      echo $this->fetch('content');
    ?>
      </div>
    </div>
    
    <div class="to-page-top">
      <a href="#"><img src="/img/icons/Arrow Up.png" title="ページトップへ" /><span>ページトップ</span></a>
    </div>
    
    <!-- #container-footer -->
    <div id="container-footer">
      <div class="inner">
	<footer>
	  copyright @ <?php
            echo $this->Html->link('timedline.com','http://www.timedline.com');
	  ?>
	</footer>
      </div>
    </div>
    <!-- /#container-footer-->
  </body>
</html>
