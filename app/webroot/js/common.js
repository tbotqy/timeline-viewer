$(document).ready(function(){

  // [ToDo]event handler for browser's previous/next button
  window.addEventListener('popstate',function(e){
    
  },false);
  
  // to-page-top button
/*
  $(".to-page-top a").click(function(e){
 
    e.preventDefault();
    $("html,body").animate({
      scrollTop: 0
    }, 500);

  });    
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