$(document).ready(function(){

  // [ToDo]event handler for browser's previous/next button
  window.addEventListener('popstate',function(e){
    
  },false);
  
  // to-page-top button
  $(".to-page-top a").click(function(e){
 
    e.preventDefault();
    $("html,body").animate({
      scrollTop: 0
    }, 500);

  });    
});