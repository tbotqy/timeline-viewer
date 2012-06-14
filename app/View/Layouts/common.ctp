<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    
    <?php
      echo $this->Html->css('common');
      echo $this->Html->script('libs/jquery-1.7.2.min');
      echo $this->Html->script('libs/bootstrap');
      echo $this->Html->script('libs/bootstrap-button');
      echo $this->Html->script('libs/bootstrap-tab');
      echo $this->Html->script('common');
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

    <?php //echo $this->element('footer');?>
    
  </body>
</html>
