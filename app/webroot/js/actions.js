$(document).ready(function(){

  // to-page-top
  $(".top-page-top a").click(function(e){
    e.preventDefault();
    window.scrollTo('0','0');
/* 
   $('html,body').animate({ scrollTop: $($(this).attr("href")).offset().top }, 'slow','swing');
    return false;*/
    //$('html,body').animate({ scrollTop: 0 }, 'slow');
  });

  // action for toggle in date-list 
  $("#date-list .toggle").click(function(){
   
    $(this).parent().find(".box-for-toggle:first").slideToggle();
    
  });

  // action for each status
  $(".status-content").click(function(){
    
    $(this).find(".bottom").slideToggle();
  });

  $("#read-more").click(function(){

    alert("OK");
    
  });
  
});
			       