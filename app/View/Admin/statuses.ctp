<?php
  $dir = $isDebug ? "/js/unpacked/" : "";
  echo $this->Html->script(array($dir.'admin','/js/libs/highcharts','graph'),array('inline'=>false));
?>
<!-- #wrap-main -->
<div id="wrap-admin">
 
  <header><h1>メニュー</h1></header>
  
  <ul class="nav nav-pills">
    <li><a href="/admin/accounts">ユーザーアカウント管理</a></li>
    <li><a href="/admin/statuses">ツイート数の確認</a></li>
  </ul>

  <hr/>
  
  <div class="wrap-buttons">
    <button id="show-months" class="btn btn-primary">毎月</button>
    <button id="show-days" class="btn btn-primary">毎日</button>
  
</div>

  <div id="container-graph"></div>

</div>
<!-- /#wrap-admin -->
