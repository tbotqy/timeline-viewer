<!-- .error-inner -->
<div class="error-inner">

  <!-- .area-alert -->
  <div class="wrap area-alert">

    <div class="alert alert-warning not-found">
      <p><i class="icon-info-sign"></i>Friend Not Found </p>
    </div>

    <h4>
      Timedlineをやっている友達がいないようです
      <a class="description" href="#"><i class="icon-question-sign"></i></a>
 
    </h4>

    <!-- .invite-friends -->
    <div class="alert alert-info invite-friends">
      <button class="close">×</button>
      <p>あなたがTwitterでフォローしている人たちがTimedlineを始めると、<br/>このページに彼らのツイートが現れます。<br/>そうすると、あなたは時間を指定してタイムラインを遡ることができるようになります。</p>
    </div>
    <!-- /.invite-friends -->

  </div>
  <!-- /.area-alert -->  

  <!-- .area-intent -->
  <div class="wrap area-intent">
    <?php
          echo $this->element('twitter-share-button');
    ?>

  </div>
  <!-- /.area-intent -->

</div>
<!-- /.error-inner -->
