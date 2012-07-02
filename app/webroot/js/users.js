$(document).ready(function(){
  
  // mouseover action for year list in dashbord
  $(".list-years").find("li").mouseover(function(){
    
    // normalize all the buttons for years
    $(".list-years").find("a").removeClass("btn-primary selected");
 
    // apply unique css feature only to focused button 
    $(this).find('a').addClass("btn-primary selected");
    
    // hide all the lists for months and days
    $("#wrap-list-months").find("ul").css('display','none');
    $("#wrap-list-days").find("ul").css('display','none');
    
    // get the data-date value in hovered button
    var year = $(this).attr('data-date');

    // show the months list whose class is equal to var year
    $("#wrap-list-months").find("."+year).css('display','block');
    
  });

  // mouseover action for months list in dashbord
  $(".list-months").find("li").mouseover(function(){
    
    // normalize all the buttons for months
    $(".list-months").find("li").find("a").removeClass("btn-primary selected");
  
    // apply unique css feature only to focused button 
    $(this).find('a').addClass("btn-primary selected");

    // hide all the days lists
    $("#wrap-list-days").find("ul").css('display','none');

    // get the data-date value in hovered button 
    var month = $(this).attr('data-date');
   
    // show the days list whose class is equal to var month
    $("#wrap-list-days").find("."+month).css('display','block');
 
  });

  // click action to change the term of statuses to show
  $("#wrap-term-selectors").find("a").click(function(e){
 
    // prevent the page from reloading
    e.preventDefault();
    
    // get href attr in clicked button
    var href =$(this).attr('href');
    
    // acquire the date to fetch from clicked button
    var date = $(this).attr('data-date');
    var date_type = $(this).attr('data-date-type');
    
   // show the loading icon over the statuses area
    var wrap_timeline = $("#wrap-timeline");
 
    wrap_timeline.html("<div class=\"cover\"><span>Loading</span></div>");

    var cover = wrap_timeline.find('.cover');
 
    cover.css("height",160);
    
    cover.animate({
      opacity: 0.8
    },200);
    // check the type of data currently being shown
    var data_type = $("#wrap-dashbord").data("type");
    
    // fetch statuses 
    $.ajax({
      type: 'GET',
      dataType: 'html',
      url:'/ajax/switch_term',
      data:{"date":date,"date_type":date_type,"data_type":data_type},
      success: function(responce){

	// insert recieved html
	$("#wrap-main").html(responce);
      },
      error: function(responce){
	alert("読み込みに失敗しました。画面をリロードしてください");	
      },
      complete: function(){

	// scroll to top
	scrollToPageTop(e);

	// show the loaded html
	$("#wrap-main").fadeIn('fast');

	// let the button say that process has been done
	$("#wrap-term-selectors").find("a").button('complete');
	
	// record requested url in the histry
	window.history.pushState(null,null,href);
	
      }
    });
  });

  // click event for day selector
  $("#wrap-list-days").find("a").click(function(){
    
    // normalize all the buttons labeled as day selector
    $("#wrap-list-days").find(".selected").removeClass("selected btn-primary");

    // make clicked button selected
    $(this).addClass("selected btn-primary");
    
  });

  // click event to update statuses
  $("#update-statuses").click(function(){
    $(this).button('loading');
    checkUpdate();
  });

  // click event to delete account
   $("#delete-account").click(function(e){
     
     var deleted = false;

     showLoader();
     $(this).button("loading");

     $.ajax({
       type:"post",
       dataType:"text",
       url:"/ajax/delete_account",
       success:function(responce){
	 deleted = responce;
       },
       error:function(){
	 $("#modal-delete-account").find(".status").text("error");
       },
       complete:function(){
	 if(deleted){
	   $(this).button("complete").setTimeout(
	     function(){
	       location.href="/user/logout";
	     }
	     ,1000);
	 }else{
	   $(this).button("uncomplete");
	   $("#modal-delete-account").find(".status").text("処理が完了しませんでした。画面をリロードしてもう一度お試しください。");
	 }
       }
     });
       
   });
				       
});

function checkUpdate(){

  var ret = "";
  $.ajax({

    url:"/ajax/checkUpdate",
    type:"POST",
    dataType:"json",
    success: function(responce){
	newTweetHasCome = responce.result;
    },
  
    error: function(){
      console.log("an error occured");
    },
    
    complete: function(){
      if(newTweetHasCome){
	updateStatus();
      }else{
	noUpdateHasCome();
      }
    }
  });

};

var total_count = 0;
var oldest_id_str = "";
var destination_time = "";

function updateStatus(){

  $.ajax({

    url:"/ajax/update_statuses",
    type:"post",
    dataType:"json",
    data:{"oldest_id_str":oldest_id_str,"destination_time":destination_time},
    success: function(responce){
      
      if(responce.continue){
	total_count += responce.count_saved;
	oldest_id_str = responce.oldest_id_str;
	destination_time = responce.destination_time;
	updateStatus();
      }else{
	total_count += responce.count_saved;
      }
    },

    error: function(){
      alert("ツイートの更新に失敗しました。");
    },

    complete: function(responce){
      var progress_area = $(".update-statuses").find(".text");
      if(responce.continue){
	// show the total number of statuses that have been imported so far
	  progress_area.fadeOut().text(total_count+"件追加").fadeIn();
      }else{
	$("#update-statuses").text("更新完了");
	progress_area.fadeOut(function(){
	  $(this).text(total_count+"件追加しました。");
	}).fadeIn();
      }
    }
  
  });
};

function noUpdateHasCome(){

  $("#update-statuses").text("更新完了");
  $(".update-statuses").find(".text").fadeOut(function(){
    $(this).text("追加できるツイートはありません。");
  }).fadeIn();

}