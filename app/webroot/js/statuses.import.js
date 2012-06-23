$(document).ready(function(){
  
  //click event activated when start button is clicked
  $("#start-import").click(function(){
    
    // change the button statement
    $("#start-import").button('loading');
    
    /// show the progress bar
    $(".wrap-progress-bar").fadeIn();

    // show the area displaying the status body currently saving
    $("#status").css({"display":"block"});
    
    //initialize data to post
    var data_to_post = {"id_str_oldest":""};
    
    // post ajax request 
    getStatuses(data_to_post);
  
  });
});

var total_count = 0;  
function getStatuses(params){

  /**
   * throw request to acquire all the statuses recursively
   */

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
	    $(".wrap-progress-bar .total").html(total_count+"件");
	    $(".wrap-tweet .body").html(ret.status.text);
	    $(".wrap-tweet .date").html(ret.status.date);
	  });
	  //throw new request
	  data_to_post.id_str_oldest = ret.id_str_oldest;
	  getStatuses(data_to_post);
	  progress = getPersentage(total_count);
	  
	}else{
	  
	  if(total_count == 0){
	    $(".progress .bar").html("There is no status to retrieve.");
	  }else{
	    $(".progress .bar").html("complete!");
	    progress = 100;
	 
	    //show the result
	    $("#start-import").button('complete');
	    $("#start-import").addClass("disabled");
	    $("#start-import").text(total_count + "件取得しました");
	    $(".progress").removeClass("active");

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
      $(".wrap-importing-status").fadeIn();
      
      // animate progress bar
      setProgress(progress);
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
  $(".progress .bar").css("width",persentage+"%");
}
