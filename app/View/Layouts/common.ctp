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
    
    <?php echo $this->element('global-header');?>
    
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
