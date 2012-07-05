$(document).ready(function(){
  
  ////////////////////////////////////
  // code for /users/configurations //
  ////////////////////////////////////

  /*
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

/*
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

  
  /*
   * the process for account deletion
   */

  var deleted = "";
 
  // click event to delete account
  $("#delete-account").click(function(){

    $(this).button('loading');
    $("#modal-delete-account")
      .find(".status")
      .fadeOut(function(){
	$(this).html("処理中...<img src=\"/img/ajax-loader.gif\" class=\"loader\" />"); 
      }).fadeIn();
    
    $.ajax({
      
      url: '/ajax/delete_account',
      type: 'post',
      dataType: 'text',
      success: function(res){
	deleted = res;
	showDeleteCompleteMessage(res);
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
	  
	}
      }

    });
  });
});

