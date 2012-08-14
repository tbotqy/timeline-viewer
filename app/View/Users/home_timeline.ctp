<?php
  echo $this->Html->script('popstate.js',array('inline'=>false));
?>

<?php
  // check error type
  if($error_type == 'noFriendList'){

      echo "<!-- #wrap-main.home_timeline.error -->";
      echo "<div id=\"wrap-main\" class=\"home_timeline error\">";
      echo $this->element('no-friend-list');
      echo "</div>";
      echo "<!-- /#wrap-main.home_timeline.error -->";

  }elseif($error_type == 'noRegisteredFriend'){

      echo "<!-- #wrap-main.home_timeline.error -->";
      echo "<div id=\"wrap-main\" class=\"home_timeline error\">";
      echo $this->element('no-registered-friend');
      echo "</div>";
      echo "<!-- /#wrap-main.home_timeline.error -->";

  }else{

      if($date_list){
          echo $this->element('dashbord');
      }
    
      if($statuses){
          echo "<!-- #wrap-timeline-lower -->";
          echo "<div id=\"wrap-timeline-lower\">";
          echo "<!-- #wrap-main-->";
          echo "<div id=\"wrap-main\">";
          
          echo $this->element('timeline');
          
          echo "</div>";
          echo "<!-- /#wrap-main -->";

          echo $this->element('ads-timeline');

          echo "</div>";
          echo "<!-- /#wrap-timeline-lower -->";

      }else{
          
          echo "<!-- #wrap-main.home_timeline.error -->";
          echo "<div id=\"wrap-main\" class=\"home_timeline error\">";
          
          echo $this->element('no-status-found');
          
          echo "</div>";
          echo "<!-- /#wrap-main.home_timeline.error -->";
          
      }

  }
?>