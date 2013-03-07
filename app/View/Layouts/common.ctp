<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8"/>
    <?php if(Configure::read('underConstruction') || Configure::read('useDbConfig') == "dev"):
    ?>
    <meta name="robots" content="noindex,nofollow,nocache"/>
    <?php else:?>
    <meta name="robots" content="index,follow"/>
    <?php endif;?>
    <meta name="description" content="あの日のタイムラインを眺められる、ちょっとしたアプリケーション"/>
    <meta name="keywords" content="タイムライン,過去のタイムライン,過去のつぶやき,過去のツイート"/>

    <meta property="og:locale" content="ja_JP">
    <meta property="fb:app_id" content="258025897640441" />
    <meta property="og:site_name" content="Timdeline" />
    <meta property="og:title" content="<?php echo $title_for_layout;?>" />
    <meta property="og:description" content="Timedline is a simple view system for those who want to have the access to their past timeline." />
    <meta property="og:url" content="http://<?php echo env('HTTP_HOST');?>" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="http://<?php echo env('HTTP_HOST');?>/favicon.ico" />
    <!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
    <![endif]-->    
    <link href='http://fonts.googleapis.com/css?family=Contrail+One|Fugaz+One|Emblema+One|Questrial|PT+Sans' rel='stylesheet' type='text/css'>

    <?php
      echo $this->Html->css
      (array(
             'bootstrap.min',
             'bootstrap.2.1.nav.min',
             'font-awesome',
             'style',
             )
       );

      // switch the js file
      $jsLoadPath = $isDebug ? "/js/unpacked/" : "";

      echo $this->Html->script
      (array(
             //'libs/jquery-1.8.2.min',
             'libs/jquery-1.9.0.min',
             'libs/bootstrap.min',
             $jsLoadPath.'functions',
             $jsLoadPath.'common',
             'twitter_tweet_button',
             'twitter_follow_button'
             )
       );
      
      echo $this->fetch('meta');
      echo $this->fetch('css');
      echo $this->fetch('script');
    ?>
    <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>    
    <title><?php echo $title_for_layout; ?></title>
    <?php
      if(stripos(env('HTTP_HOST'),'dev') === false):
    ?>
   
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-28886746-8']);
      _gaq.push(['_trackPageview']);
      
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
      
    </script>

    <?php
      endif;
    ?>
  </head>
  <body>
    <div id="fb-root"></div>
    
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
