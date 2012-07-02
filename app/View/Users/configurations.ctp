<?php
echo $this->Html->css('header-small',null,array('inline'=>false));
?>

<!-- #wrap-configurations -->
<div id="wrap-configurations">
  
  <h1><i class="icon-cogs"></i><span>データ管理</span></h1>
  
  <section>
    <button id="update-statuses" class="btn btn-success" data-loading-text="読み込み中"><i class="icon-refresh icon-white"></i>ツイートを更新する</button>
  </section>

  <section>
    <button id="update-friends" class="btn btn-success" data-loading-text="読み込み中">
      <i class="icon-refresh icon-white"></i>フォローリストを更新する
    </button>
  </section>
  
  <section>
    <button class="btn btn-danger" href="#modal-delete-account" data-toggle="modal"><i class="icon-trash icon-white"></i>アカウントを削除する</button>
  </section>

</div>
<!-- /#wrap-configurations -->

<?php
    echo $this->element('modal-delete-account');
?>
