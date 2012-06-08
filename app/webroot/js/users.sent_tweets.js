$(document).ready(function(){

  // action for dashbord
  $(".list-years li").hover(function(){

    $(".list-years a").removeClass("btn-primary active");
    $(this).find('a').addClass("btn-primary");
   
    $("#wrap-list-months ul").css('display','none');
    $("#wrap-list-days ul").css('display','none');

    // get the class value in hovering element > a
    var year = $(this).attr('data-date');
    
    // show the ul element with fetched year
    $("#wrap-list-months").find("."+year).css('display','block');
    
    //$("#wrap-list-months").find("."+year).animate({opacity:"toggle"},500);}
  });

  $(".list-months li").hover(function(){
    
    $(".list-months li a").removeClass("btn-primary active");
    $(this).find('a').addClass("btn-primary");

    $("#wrap-list-days ul").css('display','none');

    // get the class value in hovering element > a
    var month = $(this).attr('data-date');
    // show the ul element with fetched year
    //$("#wrap-list-months").find("."+year).toggle();
    $("#wrap-list-days").find("."+month).css('display','block');
  });

  // action for toggle in date-list 
  $("#date-list .toggle").click(function(){
   
    $(this).parent().find(".box-for-toggle:first").slideToggle();
    
  });

  // action for each status
  $(".status-content").live("click",function(){
    $(this).find(".bottom").slideToggle('fast');
  });

  // read more
  $("#read-more").click(function(){
  
    $("#read-more").button('loading');
    $.ajax({

      type:"POST",
      dataType:"html",
      data:{"last_status_id":$("#last-status-id").attr("value")},
      url: '/statuses/read_more',
      success: function(responce){
	// remove element representing last status id
	$("#last-status-id").remove();
	
	// insert loaded html code 
	$(".land-mark").before(responce);
	$("#read-more").button('complete');	
      },
      error: function(responce){
	// handle with error
	alert("error");
      }
    });
  });

  // change statuses term to show
  $("#wrap-term-selectors a").click(function(e){
    
    e.preventDefault();
    var d = $(this);
    $(this).button('loading');
    var date = $(this).attr('data-date');
    var date_type = $(this).attr('data-date-type');

    $.ajax({
      type: 'GET',
      dataType: 'html',
      url:'/statuses/switch_term',
      data:{"date":date,"date_type":date_type},
      success: function(responce){
	// update screen 
	$("#wrap-timeline").html(responce);
	$("#wrap-term-selectors a").button('complete');
      },
      error: function(responce){

      }
    });
  });
 
});
			       