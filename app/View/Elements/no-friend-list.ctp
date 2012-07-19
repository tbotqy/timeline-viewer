<!-- .error-inner -->
<div class="error-inner">

  <!-- .area-alert -->
  <div class="wrap area-alert">

    <div class="alert alert-warning not-found">
      <p><i class="icon-info-sign"></i>Friend Not Found </p>
    </div>

    <h4>
      フォローリストが空です
      <a class="description" href="#"><i class="icon-question-sign"></i></a>
    </h4>

    <!-- .invite-friends -->
    <div class="alert alert-info invite-friends">
      <button class="close">×</button>
      <p>もしあなたがTwitterで誰かをフォローしているにも関わらずこの画面が出たならば、<br/><a href="/users/configurations">設定画面</a>からフォローリストを更新してみてください。</p>
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
