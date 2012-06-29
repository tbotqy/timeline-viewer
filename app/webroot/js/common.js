$(document).ready(function(){
  
  // set background image to dashbord same with html's background
  var urlToBg = "/img/html_bg_linen.png";
  var urlToDashbord = "/img/html_bg_linen.png";

  $("body").css("background-image","url("+urlToBg+")");
  $("#wrap-dashbord").find(".inner").css("background-image","url("+urlToDashbord+")");

  // event handler for browser's previous/next button
  setTimeout(function(){
    window.addEventListener('popstate',function(e){
      var nextPage = location.href;
      location.href = nextPage;
    },false);
  },100);

  $(window).scroll(function() {
    var topy = $(document).scrollTop();
    if (topy >= 200) {
      $(".to-page-top").fadeIn();
    }else{
      $(".to-page-top").fadeOut();
    }
  });
  
  $(".to-page-top").find("a").click (function(e) {
    scrollToPageTop(e);
  });
  
});