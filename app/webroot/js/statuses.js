$(document).ready(function(){
 
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
      $("#status").css({"display":"block"});
    
    });
      
    //initialize data to post
    var data_to_post = {"id_str_oldest":""};
    
    // post ajax request 
    getStatuses(data_to_post);
    
  });
});

// initialize value
var total_count = 0;

function getStatuses(params){

  /**
   * throw request to acquire all the statuses recursively
   */

  var wrap_progress_bar = $(".wrap-progress-bar");
  var wrap_tweet = $(".wrap-tweet");
  var import_button = $("#start-import");  
  
  var data_to_post = params;

  $.ajax({
      
      url: "/ajax/acquire_statuses",
      type: "POST",
      dataType:"json",
      data: data_to_post,
    
    success: function(ret){
  
	total_count += ret.saved_count;
	
	if(ret.continue){

	  $(".wrap-importing-status").fadeOut(function(){
	    //show the result
	    wrap_progress_bar.find(".total").html(total_count+"件");
	    wrap_tweet.find(".body").html(ret.status.text);
	    wrap_tweet.find(".date").html(ret.status.date);
	    
	  });
	  
	  //throw new request
	  data_to_post.id_str_oldest = ret.id_str_oldest;
	  progress = getPersentage(total_count);
	  getStatuses(data_to_post);
	  
	}else{
	  
	  if(total_count == 0){
	   
	    wrap_progress_bar.find(".bar").html("There is no status to retrieve.");
	  
	  }else{
	    
	    wrap_progress_bar.find(".bar").html("complete!");
	    
	    progress = 100;
	    
	    //show the result
	    import_button.addClass('disabled');
	    import_button.text(total_count + "件取得しました");
	   
	    // stop animation
	    $(".progress").removeClass("active");
	    
	    hideLoader();
	  }
	}
    },
    
    error: function(){
    
      //show the error message in some element
      /* [debug] */
      $(".progress").removeClass("active");
      alert('Ajax error');
    
    },
    
    complete: function(ret){

      // animate progress bar
      setProgress(progress);

      $(".wrap-importing-status").fadeIn()
      
    }
  });
}


function getPersentage(fetched_status_count){
  fetched_status_count = parseInt(fetched_status_count);
  var total = parseInt($("#statuses-count").val()); 
  
  var ret = "";
  if(fetched_status_count > 3200){
    ret = (fetched_status_count / 3200) * 100;
  }else{
    ret = (fetched_status_count / total) * 100;
  }
  
  return parseInt(ret);
}

function setProgress(persentage){
  $(".progress").find(".bar").css("width",persentage+"%");
}
