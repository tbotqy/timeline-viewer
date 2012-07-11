<?php
  echo $this->Html->script('popstate.js',array('inline'=>false));
?>

<?php

// check error type
switch($error_type){

case 'noFriendList':

    echo "<!-- #wrap-main.home_timeline.error -->";
    echo "<div id=\"wrap-main\" class=\"home_timeline error\">";
    echo $this->element('no-friend-list');
    echo "</div>";
    echo "<!-- /#wrap-main.home_timeline.error -->";
    break;

case 'noRegisteredFriend':
      
    echo "<!-- #wrap-main.home_timeline.error -->";
    echo "<div id=\"wrap-main\" class=\"home_timeline error\">";
    echo $this->element('no-registered-friend');
    echo "</div>";
    echo "<!-- /#wrap-main.home_timeline.error -->";
    break;

default:

    if($date_list){
        echo $this->element('dashbord');
    }
    

    if($statuses){
        echo "<!-- #wrap-main-->";
        echo "<div id=\"wrap-main\">";

        echo $this->element('timeline');

        echo "</div>";
        echo "<!-- /#wrap-main -->";

    }else{

        echo "<!-- #wrap-main.home_timeline.error -->";
        echo "<div id=\"wrap-main\" class=\"home_timeline error\">";

        echo $this->element('no-status-found');

        echo "</div>";
        echo "<!-- /#wrap-main.home_timeline.error -->";
        
    }
    break;
  }
