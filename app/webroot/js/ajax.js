$(document).ready(function(){

  $("#start").click(function(){
    $("#status").css({"display":"block"});
    //getStatuses(data_to_post);
    ajaxTest(data_to_post);
  });
});







































function getStatuses(params){
/*
 * acquire all the statuses 
 */

  var data_to_post = params;
  
  $.ajax(
    {
      //url: "acquire_statuses",
      // test code //
      url: "ajax_test",
      type: "POST",
      data: data_to_post,
      success: function(ret){
	if(ret.continue){
	  total_count += ret.count_saved;

	  //show the result
	  $("#status").html("Retrieved " + total_count + " statuses so far.");
	  data.id_str_oldest = ret.id_str_oldest;
	  data.initial_request = false;
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

var num = 1;
function ajaxTest(data){
  var data = data;
  $.ajax(
    {
      url: 'ajax_test', 
      type:"POST",
      dataType:"json",
      data: data,
      success: function(responce){
	num = responce.num;
	if(num < 300){
	  $("#status").html(num);
	  ajaxTest(data);
	}else{
	  num = 1;
	  $("#status").html('finished');
	}
      },
      error: function(responce){
	console.log(responce);
	$('#status').html('[debug]Ajax処理エラー'+responce.data);
      }
      
    }
  );

}