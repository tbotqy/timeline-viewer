$(document).ready(function(){

  $("#start").click(function(){

    $("#status").css({"display":"block"});
    
    //initial data to post
    var data_to_post = {"id_str_oldest":""};

    getStatuses(data_to_post);
    //ajaxTest();
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
	  }
	  return;
	}
      },
      error: function(){
	//show the error message in some element
	/* [debug] */
	alert('ajax error');
	return;
      }
    }
  );
}

function ajaxTest(){

  $.ajax(
    {
      url: 'ajax_test', 
      type:"POST",
      //dataType:"json",
      data: {"nullData":null},
      success: function(responce){
	alert(responce);
      },
      error: function(responce){
	console.log(responce);
	$('#status').html('[debug]Ajax処理エラー'+responce.data);
      }
      
    }
  );

}