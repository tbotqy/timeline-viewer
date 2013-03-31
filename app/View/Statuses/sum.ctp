<?php
  $dir = $isDebug ? "/js/unpacked/" : "";
  echo $this->Html->script(array($dir.'graph','/js/libs/highcharts'),array('inline'=>false));
  echo $this->Html->script(array('/js/libs/highcharts','graph'),array('inline'=>false));
?>

<header>
  <h1>共有ツイート数の推移</h1>
  <nav class="wrap-buttons btn-group pull-right" data-toggle="buttons-radio">
    <button id="show-months" class="btn btn-primary active">毎月</button>
    <button id="show-days" class="btn btn-primary">毎日</button>
  </nav>
<d
</header>
<hr/>
<div class="pull-right">
  <a href="https://twitter.com/share" class="twitter-share-button" data-via="timedline_tw" data-lang="ja" data-hashtags="timedline">ツイート</a>
  <a href="http://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="simple-balloon" title="このエントリーをはてなブックマークに追加"><img src="http://b.st-hatena.com/images/entry-button/button-only.gif" alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;" /></a>
</div>

<div id="container-graph"></div>
