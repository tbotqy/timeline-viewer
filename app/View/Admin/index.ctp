<?php
  $dir = $isDebug ? "/js/unpacked/" : "";
  echo $this->Html->script($dir.'admin',array('inline'=>false));
?>
<!-- #wrap-main -->
<div id="wrap-admin">

  <header><h1>メニュー</h1></header>
  
  <ul class="nav nav-pills">
    <li><a href="/admin/accounts">ユーザーアカウント管理</a></li>
    <li><a href="/admin/statuses">ツイート数の確認</a></li>
  </ul>

</div>
<!-- /#wrap-admin -->
