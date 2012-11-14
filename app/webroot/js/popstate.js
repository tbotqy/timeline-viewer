$(function(){ if('pushState' in history){ window.setTimeout(function(){ $(this).on("popstate",function(e){ var white_list = ['tweets','home_timeline','public_timeline']; var path = location.pathname; var actionTypeOk = false; var slashCountOk = false; for(var i=0;i<white_list.length;i++){ if(countStr(path,white_list[i]) > 0){ actionTypeOk = true; break;}
}
if( actionTypeOk ){ var date; var isPublicTimeline = path.indexOf("public_timeline") != -1; var threshold; if(isPublicTimeline){ threshold = 2;}else{ threshold = 3;}
if(countStr(path,"/") < threshold){ date = "notSpecified";}else{ date = detectDate(path);}
var action_type = detectActionType(path); ajaxSwitchTerm(date,action_type,"pjax"); $("#wrap-term-selectors").find("a.selected").removeClass("btn-primary selected");}
});},2000);}
}); 
