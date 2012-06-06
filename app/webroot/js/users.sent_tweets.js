$(document).ready(function(){
  
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
  $("#date-list a").click(function(e){
    
    e.preventDefault();
    var date = $(this).attr('name');
    var date_type = $(this).attr('class');

    $.ajax({
      type: 'GET',
      dataType: 'html',
      url:'/statuses/switch_term',
      data:{"date":date,"date_type":date_type},
      success: function(responce){
	// update screen 
	$("#wrap-timeline").html(responce);
      },
      error: function(responce){

      }
    });
  });
 
});
			       