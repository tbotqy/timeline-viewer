<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <!--[endif]-->    
    <link href='http://fonts.googleapis.com/css?family=Contrail+One|Fugaz+One|Emblema+One|Questrial|PT+Sans' rel='stylesheet' type='text/css'>
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
    
    <meta property="og:title" content="<?php echo $title_for_layout;?>" />
    <meta property="og:description" content="Simple webapp to see your passed timeline." />
    <meta property="og:url" content="http://timedline.phpfogapp.com/" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="http://timedline.phpfogapp.com/favicon.ico" />

    <title><?php echo $title_for_layout; ?></title>
    <script>

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-33538459-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
      
    </script>
  </head>
  <body>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/ja_JP/all.js#xfbml=1&appId=258025897640441";
      fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));</script>

    <?php echo $this->element('global-header');?>

    <!-- #container-main -->
    <div id="container-main" class="<?php echo $actionType;?>">
      <div class="inner">
	<?php
          echo $this->fetch('content');
	?>
      </div>
    </div>
    <!-- /#container-main -->
    
    <?php 
      echo $this->element('to-page-top');
    ?>
    
    <?php 
      if($showFooter){
          echo $this->element('footer');
      }
    ?>

    <?php //echo $this->element('sql_dump');?>    

  </body>
</html>
