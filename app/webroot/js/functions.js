function scrollToPageTop(e){
   e.preventDefault();
  
  $("html, body").animate(
    {scrollTop:0},
    {easing:"swing",duration:500}
  );
  
  return false;
}

function scrollDownToDestination(e,distance){
  e.preventDefault();
  distance -= 160;
  $("html, body").animate(
    {scrollTop: distance},
    {easing:"swing",duration:500}
  );

  return false;
}

function showLoader(){
  $(".loader").fadeIn();
}

function hideLoader(){
  $(".loader").fadeOut();
}

  function checkStatusUpdate(){
    /*
     * check if there is any tweets yet to have imported
     */
    var doUpdate = false;
    var updated_date = "";

    $.ajax({
      
      url:"/ajax/check_update",
      type:"post",
      dataType:"json",
      success: function(responce){
	doUpdate = responce.result;
	updated_date = responce.updated_date;

      },
  
      error: function(){
	// [ToDo] manage error
	alert("an error has occured");
      },
    
      complete: function(){
	
	if(doUpdate){
	  
	  updateStatus();
	  
	}else{
	  // change the view statements
	  $("#update-statuses").text("更新完了");

	  $(".tweets").find(".loader").fadeOut();
	  
	  var box_tweets =$("#wrap-configurations").find(".tweets");
	  box_tweets.find(".additional-num").fadeOut(function(){
	    $(this).text("(+0件)");
	  }).fadeIn();
	  
	  box_tweets.find(".last-update .date").fadeOut(function(){
	    $(this).text(updated_date);
	  }).fadeIn();

	}
      }
 
    });
    
  };

  var total_count = 0;
  var oldest_id_str = "";
  var continue_process = "";
  var updated_date = "";

function updateStatus(){
    

  $.ajax({
    
    url:"/ajax/update_statuses",
    type:"post",
    dataType:"json",
    data:{"oldest_id_str":oldest_id_str},
      
    success: function(responce){
      continue_process = responce.continue;
      updated_date = responce.updated_date;

      if(continue_process){
	
	total_count += responce.count_saved;
	oldest_id_str = responce.oldest_id_str;
	updateStatus();
	
      }else{
	  
	total_count += responce.count_saved;
	
      }
      
    },
    
    error: function(){
      // [ToDo] manage error
      alert("ツイートの更新に失敗しました。");
      
    },
      
    complete: function(){
      
      if(continue_process){
	
	// show the total number of statuses that have been imported so far
	$(".tweets").find(".additional-num").fadeOut(function(){
	  $(this).text("+ "+total_count);
	}).fadeIn();
	
      }else{

	var final_total = 0;
	var current_num = parseInt($(".tweets").find(".count .total-num").text());
	var area_count = $(".tweets").find(".count");
	
	final_total = current_num + parseInt(total_count);
	
	area_count.find(".total-num").fadeOut(function(){
	  $(this).text(final_total).fadeIn();
	});
	
	area_count.find(".additional-num").fadeOut(function(){
	  $(this).text("(+"+total_count+"件)");
	}).fadeIn();

	$(".tweets").find(".last-update .date").fadeOut(function(){
	  $(this).text(updated_date);
	
	}).fadeIn();
	
	// change the statement on button
	var update_button = $("#update-statuses");
	update_button.text("更新完了");

	// disable button
	update_button.addClass("disabled");
	
	// hide the loader
	$(".loader").fadeOut();
      }
    }
    
  });
};

function showDeleteCompleteMessage(flag){
  
  var message = "";
  var area_status = $("#modal-delete-account").find(".status");		  
  
  if(flag){
    message = "アカウント削除が完了しました。自動的にログアウトします。";
  }else{
    message = "すみません！処理が完了しませんでした。画面をリロードしてもう一度お試しください。";  
  }

  area_status.fadeOut(function(){
    $(this).text(message);
  }).fadeIn();

}

function showDeleteErrorMessage(){
  return showCompleteMessage(false);
}

function redirect(){
  location.href="/users/logout";
}
  
