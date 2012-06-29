<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    
    <?php
      echo $this->Html->css
      (array(
             'bootstrap',
             'font-awesome',
             'style',
             )
       );
      echo $this->Html->script
      (array(
             'libs/jquery-1.7.2.min',
             'libs/bootstrap',
             'functions',
             'common',
             'elements',
             'users',
             'statuses'
             )
       );
      
      echo $this->fetch('meta');
      echo $this->fetch('css');
      echo $this->fetch('script');
    ?>
    <title><?php echo $title_for_layout; ?></title>
  </head>
  <body>
    
    <?php echo $this->element('global-header');?>
        
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

    <?php //echo $this->element('footer');?>
    <?php //echo $this->element('sql_dump');?>    
  </body>
</html>
