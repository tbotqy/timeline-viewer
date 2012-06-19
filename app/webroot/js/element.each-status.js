$(document).ready(function(){

  // click action for each status
  // hide and show the bottom line in each status
  $(".status-content").live("click",function(){
    $(this).find(".bottom").slideToggle('fast');
  });

});