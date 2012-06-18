$(document).ready(function(){
  
  // mouseover action for year list in dashbord
  $(".list-years li").mouseover(function(){
    
    // normalize all the buttons for years
    $(".list-years a").removeClass("btn-primary selected");
    // apply unique css feature only to focused button 
    $(this).find('a').addClass("btn-primary selected");
    
    // hide all the lists for months and days
    $("#wrap-list-months ul").css('display','none');
    $("#wrap-list-days ul").css('display','none');
    
    // get the data-date value in hovered button
    var year = $(this).attr('data-date');

    // show the months list whose class is equal to var year
    $("#wrap-list-months").find("."+year).css('display','block');
    
  });

  // mouseover action for months list in dashbord
  $(".list-months li").mouseover(function(){
    
    // normalize all the buttons for months
    $(".list-months li a").removeClass("btn-primary selected");
    // apply unique css feature only to focused button 
    $(this).find('a').addClass("btn-primary selected");

    // hide all the days lists
    $("#wrap-list-days ul").css('display','none');

    // get the data-date value in hovered button 
    var month = $(this).attr('data-date');
   
    // show the days list whose class is equal to var month
    $("#wrap-list-days").find("."+month).css('display','block');
 
  });

  // click action for read more button
  $("#read-more").click(function(){
    
    // let button say 'loading'
    $("#read-more").button('loading');
    
    // fetch more statuses to show
    $.ajax({

      type:"POST",
      dataType:"html",
      data:{"last_status_id":$("#last-status-id").attr("value")},
      url: '/ajax/read_more',
      success: function(responce){
	// remove the element representing last status id
	$("#last-status-id").remove();
	
	// insert loaded html code 
	$(".land-mark").before(responce);

      },
      error: function(responce){
	alert("読み込みに失敗しました。画面をリロードしてください");
      },
      complete: function(){
	// let button say that process has been done
	$("#read-more").button('complete');	
      }
    });
  });

  // click action to change the term of statuses to show
  $("#wrap-term-selectors a").click(function(e){
  
    // prevent the page from reloading
    e.preventDefault();
    
    // get href attr in clicked button
    var href =$(this).attr('href');
    
    // let button say loading
    //$(this).button('loading');
   
    // change the color of buttons
    /* 
       var parent = $("#wrap-term-selectors");
       parent.find(".loaded").removeClass("btn-warning loaded");
       parent.find(".selected").addClass("btn-warning loaded");
       parent.find(".selected").removeClass("btn-primary selected");
    */

    // acquire the date to fetch from clicked button
    var date = $(this).attr('data-date');
    var date_type = $(this).attr('data-date-type');
    
    $("#wrap-read-more").fadeOut('fast'); 
    
    // show the loading icon over the statuses area
    $(".land-mark").before("<div class=\"cover\"><span>Loading</span></div>");
    var cover = $("#wrap-timeline .cover");
    var wrap_timeline = $("#wrap-timeline"); 
    cover.css("height",wrap_timeline.height());
    
    cover.animate({
      opacity: 0.8
    },200);

    // fetch statuses 
    $.ajax({
      type: 'GET',
      dataType: 'html',
      url:'/ajax/switch_term',
      data:{"date":date,"date_type":date_type},
      async: true,
      success: function(responce){
	// insert recieved html
	wrap_timeline.html(responce);

      },
      error: function(responce){
	alert("読み込みに失敗しました。画面をリロードしてください");	
      },
      complete: function(){
	
	// show the loaded html
	wrap_timeline.fadeIn('fast');
	
	$("#wrap-read-more").fadeIn('fast');

	// let the button say that process has been done
	$("#wrap-term-selectors a").button('complete');
	
	// record requested url in the histry
	window.history.pushState(null,null,href);
	
      }
    });
  });
 
});