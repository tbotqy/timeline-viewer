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
  var box_tweets =$("#wrap-configurations").find(".tweets");
 
  $.ajax({
      
    url:"/ajax/check_status_update",
    type:"post",
    dataType:"json",
    
    success: function(responce){
      doUpdate = responce.result;
      updated_date = responce.updated_date;
     
      if(doUpdate){
	
	updateStatus();
	
      }else{
	// change the view statements
	$("#update-statuses").text("処理終了");
	
	box_tweets.find(".loader").fadeOut();
	
	box_tweets.find(".additional-num").fadeOut(function(){
	  $(this).addClass("alert alert-info").text("変更はありません");
	}).fadeIn();
	
	box_tweets.find(".last-update .date").fadeOut(function(){
	  $(this).text(updated_date);
	}).fadeIn();
	
      }
      
    },
    error: function(){
      
      box_tweets.find(".additional-num").fadeOut(function(){
	$(this).addClass("alert alert-danger").text("もう一度お試しください");
      }).fadeIn();
      
      // change the view statements
      $("#update-statuses").text("エラー");
      
      box_tweets.find(".loader").fadeOut();
    }
  });
};

function checkFriendUpdate(){
 
  /**
   * checks if there is any new friend on twitter
   */
  
  var count;
  var updated;
  var updated_date;
  var area_friends = $("#wrap-configurations").find(".friends");
  $.ajax({

    url: "/ajax/check_friend_update",
    type: "post",
    dataType: "json",
    success: function(responce){
      count = responce.count_friends;
      updated = responce.updated;
      updated_date = responce.updated_date;
      // show the result
      if(updated){
	
	area_friends.find(".count .total-num").fadeOut(function(){
	  $(this).text(count);
	}).fadeIn();

	area_friends.find(".count .additional-num").fadeOut(function(){
	  $(this).addClass("alert alert-success").text("更新しました");
	}).fadeIn();

	$("#update-friends").text("更新完了");
	
      }else{

	area_friends.find(".count .additional-num").fadeOut(function(){
	  $(this).addClass("alert alert-info").text("変更はありません");
	}).fadeIn();
	
	$("#update-friends").text("処理終了");
      }

      area_friends.find(".last-update .date").fadeOut(function(){
	$(this).text(updated_date);
      }).fadeIn();
      
    },
    error: function(){
      
      area_friends.find(".count .additional-num").fadeOut(function(){
	$(this).addClass("alert alert-danger").text("もう一度お試しください");
      }).fadeIn();
  
      $("#update-friends").text("エラー");

    },
    complete: function(){

      // hide the loading icon
      $(".loader").fadeOut();

    }

  });

}


var total_count = 0;
var oldest_id_str = "";
var continue_process = "";
var updated_date = "";

function updateStatus(){
    
  var area_tweets = $("#wrap-configurations").find(".tweets");
  var update_button = $("#update-statuses");
  
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

	// show the total number of statuses that have been imported so far
	area_tweets.find(".additional-num").fadeOut(function(){
	  $(this).text("+ "+total_count);
	}).fadeIn();


	updateStatus();
	
      }else{
	  
	total_count += responce.count_saved;

	var final_total = 0;
	var current_num = parseInt($(".tweets").find(".count .total-num").text());
	
	final_total = current_num + parseInt(total_count);
	
	area_tweets.find(".total-num").fadeOut(function(){
	  $(this).text(final_total).fadeIn();
	});
	
	area_tweets.find(".additional-num").fadeOut(function(){
	  $(this).addClass("alert alert-success").text(total_count+"件追加");
	}).fadeIn();

	area_tweets.find(".last-update .date").fadeOut(function(){
	  $(this).text(updated_date);
	
	}).fadeIn();

	// change the button statement
	update_button.text("更新完了");
	
      }
      
    },
    
    error: function(){

      area_tweets.find(".additional-num").fadeOut(function(){
	$(this).addClass("alert alert-danger").text("もう一度お試しください");
      }).fadeIn();

      // change the button statement
      update_button.text("エラー");
	
      // hide the loader
      $(".loader").fadeOut();
      
    },
      
    complete: function(){
      if(!continue_process){
	
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
  
