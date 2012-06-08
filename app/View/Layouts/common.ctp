<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <?php
      echo $this->Html->css('bootstrap');
      echo $this->Html->css('common');
 //echo $this->Html->script('jquery-1.7.2.min.js');
      echo $this->Html->script('jquery-1.7.2.js');
      echo $this->Html->script('bootstrap.js');
      echo $this->Html->script('bootstrap-button.js');
      echo $this->Html->script('bootstrap-tab.js');
    ?>
    <?php
      echo $this->fetch('meta');
      echo $this->fetch('css');
      echo $this->fetch('script');
    ?>
    <title><?php echo $title_for_layout?></title>
  </head>
  <body>
    <div id="containerHeader">
      <header id="global">
	<div class="header_inner">
	  <h1 class="page_logo">
	    <?php 
              echo $this->Html->link('APP_NAME','/',array('class'=>'link_top'));
        ?>
	  </h1>
	  <?php
            echo $this->Html->link('ログアウト',array('controller'=>'users','action'=>'logout'),array('class'=>'link_logout'));
	  ?>
	</div>
      </header>
    </div><!-- container header end -->
    <div id="container-main">
    <?php
      echo $this->fetch('content');
    ?>
    </div>
    <div class="to-page-top">
      <a href="#"><img src="/img/icons/Arrow Up.png" title="ページトップへ" /><span>ページトップ</span></a>
    </div>
    <div id="containerFooter">
      <footer>
	copyright @ <?php
          echo $this->Html->link('timedline.com','http://www.timedline.com');
    ?>
      </footer>
    </div><!-- container footer end -->
  </body>
</html>
