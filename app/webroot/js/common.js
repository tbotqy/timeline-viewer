$(document).ready(function(){

  // [ToDo]event handler for browser's previous/next button
  /*
  window.addEventListener('popstate',function(e){
  },false);
 */

  $(window).scroll(function() {
    var topy = $(document).scrollTop();
    if (topy >= 200) {
      $(".to-page-top").fadeIn();
    }else{
      $(".to-page-top").fadeOut();
    }
  });
  
  $(".to-page-top a").click (function() {

    $("html, body").animate({scrollTop:0}, {easing:"swing",duration:500});
    return false;
 
  });
  
});