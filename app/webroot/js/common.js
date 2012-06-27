$(document).ready(function(){
  
  // set background image to dashbord same with html's background
  var urlToBg = "/img/html_bg_linen.png";
  var urlToDashbord = "/img/html_bg_linen.png";

  $("body").css("background-image","url("+urlToBg+")");
  $("#wrap-dashbord .inner").css("background-image","url("+urlToDashbord+")");
  //$("#wrap-dashbord").css("background",$("body").css("background"));

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
  
  $(".to-page-top a").click (function(e) {
    scrollToPageTop(e);
  });
  
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