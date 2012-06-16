<?php

$atext = $this->Text->autoLinkUrls($text);
echo $atext;
echo "<br/>";
$aatext = $this->Text->autoLinkUrls($atext);
echo $aatext;
