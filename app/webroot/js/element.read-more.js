$(document).ready(function(){
 
  // click action for read more button
  $("#read-more").live("click",function(e){
    
    e.preventDefault();
    var distance = $(this).offset().top;

    // let button say 'loading'
    $(this).button('loading');
    
    // fetch more statuses to show
    $.ajax({

      type:"POST",
      dataType:"html",
      data:{
	"oldest_timestamp":$("#oldest-timestamp").attr("value"),
	"destination_data_type":$("#wrap-dashbord").data("type")
      },
      url: '/ajax/read_more',
      success: function(responce){
	// remove the element representing last status's timestamp
	$("#oldest-timestamp").remove();
	
	$("#wrap-read-more").remove();

	// insert loaded html code 
	$(".wrap-each-status:last").after(responce);
      },
      error: function(responce){
	alert("読み込みに失敗しました。");
      },
      complete: function(){
	
	scrollDownToDestination(e,distance);

      }
    });
  });
  
});