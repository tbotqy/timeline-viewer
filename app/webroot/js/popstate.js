$(function(){

  /////////////////////////////
  // code for popstate event //
  /////////////////////////////
  /*
  // event handler for browser's previous/next button
  $(window).bind("popstate",function(e){

    // acquire the date to fetch from clicked button
    var date = detectDateParameter(location.pathname);
    
    // check the type of data currently being shown
    var action_type = getActionType();
    console.log(action_type);
    
    // fetch content for dashbord
    ajaxSwitchDashbord(action_type);
    
    // fetch content for timeline
    ajaxSwitchTerm(date,action_type,"popstate",e);
    
  });
  */
});