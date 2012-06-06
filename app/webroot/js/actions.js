$(document).ready(function(){

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
			       