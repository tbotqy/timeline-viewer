<!-- #container-footer -->
<div id="container-footer">
  
  <!-- .inner -->
  <div class="inner">

    <!-- .area-upper -->
    <div class="area-upper">

      <!-- .area-left -->
      <div class="area-left">

	<div class="adsense footer">
	  <?php
            echo $this->element('adsense728');
	  ?>
	</div>

      </div>
      <!-- /.area-left -->

      <!-- .area-right -->
      <div class="area-right">

	<div class="wrap-catch">
	  <p class="brand">Timedline</h1>
	  <p class="description">is a simple view system for those who want to have the access to their past timeline.</p>
	</div>

      </div>
      <!-- /.area-right -->
      
    </div>
    <!-- /.area-upper -->
    
    <footer>

      <!-- .container -->
      <div class="container">

	<span class="copyright">
	  <a href="http://<?php echo env('HTTP_HOST');?>">Timedline</a> © 2012
	</span>
	
	<!-- .wrap-social-plugins -->
	<div class="wrap-social-plugins">
	  
	  <div class="wrap-twitter-follow">
	    <a href="https://twitter.com/timedline_tw" class="twitter-follow-button" data-show-count="false" data-lang="ja">@timedline_twさんをフォロー</a>
	  </div>

	  <div class="wrap-fb">
	    <div class="fb-like" data-href="http://<?php echo env('HTTP_HOST');?>" data-send="false" data-show-faces="false" data-layout="button_count"></div>
	  </div>

	  <div class="wrap-twitter-tweet">
	    <?php
              echo $this->element('twitter-share-button');
	    ?>
	  </div>
      
	  <div class="wrap-hatena">
	    <a href="http://b.hatena.ne.jp/entry/http://<?php echo env('HTTP_HOST');?>" class="hatena-bookmark-button" data-hatena-bookmark-title="Timedline" data-hatena-bookmark-layout="standard" title="このエントリーをはてなブックマークに追加"><img src="http://b.st-hatena.com/images/entry-button/button-only.gif" alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;" /></a>
	  </div>      
	  
	</div>
	<!-- /.wrap-social-plugins -->
      </div>
      <!-- /.container -->

    </footer>
  </div>
  <!-- /.inner -->

</div>
<!-- /#container-footer-->
