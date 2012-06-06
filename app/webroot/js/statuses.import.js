$(document).ready(function(){
  $("#start").click(function(){
    $("#start").button('loading');
    $("#status").css({"display":"block"});
    //initial data to post
    var data_to_post = {"id_str_oldest":""};
    getStatuses(data_to_post);
   
  });
});

var total_count = 0;  
function getStatuses(params){
/*
 * throw request to acquire all the statuses 
 */

  var data_to_post = params;
  $.ajax(
    {
      url: "acquire_statuses",
      type: "POST",
      dataType:"json",
      data: data_to_post,
      success: function(ret){
	total_count += ret.saved_count;
	if(ret.continue){
	  //show the result
	  $("#status .progress").html("Retrieved " + total_count + " statuses so far.");
	  $("#status .text").html(ret.status.text);
	  $("#status .date").html(ret.status.date);
	  data_to_post.id_str_oldest = ret.id_str_oldest;
	  //throw new request
	  getStatuses(data_to_post);
	}else{
	  if(total_count == 0){
	    $("#status").html("There is no status to retrieve.");
	  }else{
	    //show the result
	    $("#status").html("Completed retrieving " + total_count + "of statuses.");
	    $("#start").button('complete');
	  }
	  return;
	}
      },
      error: function(){
	//show the error message in some element
	/* [debug] */
	alert('Ajax error');
	return;
      }
    }
  );
}