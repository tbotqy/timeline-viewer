$(function(){
      
  // set background image to dashbord same with html's background
  var urlToBg = "/img/html_bg_linen.png";
  var urlToDashbord = "/img/html_bg_linen.png";
  
  $("body").css("background-image","url("+urlToBg+")");
  $("#wrap-dashbord").find(".inner").css("background-image","url("+urlToDashbord+")");

  //////////////////////////
  // code for each status //
  //////////////////////////
  
  // click action to hide and show the bottom line in each status
  $(".status-content").live("click",function(e){
    
    // do process only if clicked element is not <a>
    if(!$(e.target).is('a i')){
      
      $(this).find(".bottom").slideToggle('fast');
      
    }
    
  });

  // click action to fire a delete ajax action
  $(".status-content").find(".link-delete a").live("click",function(e){
    
    e.preventDefault();
    
    if(confirm('ツイートを削除します。よろしいですか？')){

      var status_id_to_delete = $(this).parent().data('status-id');
      
      $.ajax({
        
        url: "/ajax/delete_status",
        type: "post",
        data:{"status_id_to_delete":status_id_to_delete},
        dataType: "json",
        success: function(responce){

          // checks if the status trying to deleted is owned by logging user
          if(responce.owns){
            
            // checks id delete process was correctly done
            if(responce.deleted){
                
              $("div[data-status-id="+status_id_to_delete+"]").fadeOut();
            }else{
              
              alert("ごめんなさい。削除に失敗しました。画面をリロードしてもう一度お試しください。");
            
            }
          
          }else{
            // the status trying to be deleted is not owned by logging user
            alert("不正な操作です。");
          }

        },
        
        error: function(){
          // internal error
          alert("エラーが発生しました。");
        }
      });
    }
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
	"destination_action_type":getActionType()
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

  var wrap_progress_bar = $(".wrap-progress-bar");
  var import_button = $("#start-import");  

  //click event activated when start button is clicked
  import_button.click(function(){
    
    // change the button statement
    import_button.button('loading');
    
    // show the loader icon
    showLoader();
    
    /// show the progress bar
    wrap_progress_bar.fadeIn(function(){

      // show the area displaying the status body currently saving
      //$("#status").css({"display":"block"});
      $("#status").fadeIn();
    
    });
      
    //initialize data to post
    var data_to_post = {"id_str_oldest":""};
    
    // post ajax request 
    getStatuses(data_to_post);
    
  });
  
  ////////////////////////////////////
  // code for /users/home_timeline  //
  ////////////////////////////////////
  
  $(".error-inner").find(".description").click(function(e){
    e.preventDefault();
    $(".error-inner").find(".invite-friends").fadeIn();
  });

  $(".error-inner").find(".invite-friends .close").click(function(e){
    e.preventDefault();
    $(".error-inner").find(".invite-friends").fadeOut();
  });

  ////////////////////////////////////
  // code for /users/configurations //
  ////////////////////////////////////

  /**
   * the process to update tweets
   */

  $("#update-statuses").click(function(){

    // change the button's statement
    $(this).button('loading');

    // show the loading icon 
    $(this).after("<img class=\"loader\" src=\"/img/ajax-loader.gif\" />");
    $(".tweets").find(".loader").fadeIn();

    checkStatusUpdate();

  });

  /**
   * the process to update friend list
   */

  $("#update-friends").click(function(){
    
    // change the button's statement
    $(this).button('loading');

    // show the loading icon
    $(this).after("<img class=\"loader\" src=\"/img/ajax-loader.gif\" />");
    $(".friends").find(".loader").fadeIn();

    checkFriendUpdate();
    
  });
  
  /**
   * the process for account deletion
   */

  var deleted = "";
 
  // click event to delete account
  $("#delete-account").click(function(){

    // disable cancel button
    $("#modal-delete-account").find(".modal-header .close").fadeOut();
    $("#modal-delete-account").find(".modal-footer .cancel-delete").addClass("disabled");

    $(this).button('loading');
    $("#modal-delete-account")
      .find(".status")
      .fadeOut(function(){
	$(this).html("処理中...<img src=\"/img/ajax-loader.gif\" class=\"loader\" />"); 
      })
      .fadeIn();
    
    $.ajax({
      
      url: '/ajax/delete_account',
      type: 'post',
      dataType: 'json',
      
      success: function(res){
	deleted = res.deleted;
	showDeleteCompleteMessage(res.deleted);
      },

      error: function(){
	showDeleteErrorMessage();
      },
      
      complete: function(){
	if(deleted){

	  setTimeout(
	    function(){
	      redirect();
	    }, 3000
	  );
	  
	}else{
          alert("処理がうまくいきませんでした。");
        }
      }

    });
  });


  ///////////////////////////////////////////////
  // code for the button to scroll to page top //
  ///////////////////////////////////////////////

  $(window).scroll(function() {
    var topy = $(document).scrollTop();
    if (topy >= 200) {
      $(".to-page-top").fadeIn();
    }else{
      $(".to-page-top").fadeOut();
    }
  });
  
  $(".to-page-top").find("a").click (function(e) {
    scrollToPageTop(e);
  });
  
  ////////////////////////////////
  // code for elements/dashbord //
  ////////////////////////////////

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
 
    cover.css("height",200);
    
    cover.animate({
      opacity: 0.8
    },200);

    // check the type of data currently being shown
    var action_type = $("#wrap-dashbord").data("type");
    
    // fetch statuses 
    $.ajax({
      type: 'GET',
      dataType: 'html',
      url:'/ajax/switch_term',
      data:{
        "date":date,
        "date_type":date_type,
        "action_type":action_type
      },
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

});