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
             'common'
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

    <?php 
      echo $this->element('to-page-top');
    ?>

    <?php //echo $this->element('footer');?>
    <?php //echo $this->element('sql_dump');?>    
  </body>
</html>
