function facebook(d,s,a){var b,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(a))return;b=d.createElement(s);b.id=a;b.src="//connect.facebook.net/ja_JP/all.js#xfbml=1&appId=258025897640441";fjs.parentNode.insertBefore(b,fjs)}function getUserAgent(){var a=window.navigator.userAgent.toLowerCase();if(a.indexOf('opera')!=-1){return'opera'}else if(a.indexOf('msie')!=-1){return'ie'}else if(a.indexOf('chrome')!=-1){return'chrome'}else if(a.indexOf('safari')!=-1){return'safari'}else if(a.indexOf('firefox')!=-1){return'firefox'}else{return false}}function scrollToPageTop(e){if(e){e.preventDefault()}$("html, body").animate({scrollTop:0},{easing:"swing",duration:500});return false}function scrollDownToDestination(e,a){e.preventDefault();a-=160;$("html, body").animate({scrollTop:a},{easing:"swing",duration:500});return false}function showLoader(a){var b=typeof(a);if(b=="string"){$(a).find(".loader").fadeIn()}else if(b=="object"){a.find(".loader").fadeIn()}}function hideLoader(a){var b=typeof(a);if(b=="string"){$(a).find(".loader").fadeOut()}else if(b=="object"){a.find(".loader").fadeOut()}}function checkStatusUpdate(){var b=false;var c="";var d=$("#wrap-configurations").find(".tweets");$.ajax({url:"/ajax/check_status_update",type:"post",dataType:"json",success:function(a){b=a.result;c=a.updated_date;if(b){updateStatus()}else{$("#update-statuses").text("処理終了");d.find(".loader").fadeOut();d.find(".additional-num").fadeOut(function(){$(this).addClass("alert alert-info").text("変更はありません")}).fadeIn();d.find(".last-update .date").fadeOut(function(){$(this).text(c)}).fadeIn()}},error:function(){d.find(".additional-num").fadeOut(function(){$(this).addClass("alert alert-danger").text("もう一度お試しください")}).fadeIn();$("#update-statuses").text("エラー");d.find(".loader").fadeOut()}})};function checkFriendUpdate(){var b;var c;var d;var e=$("#wrap-configurations").find(".friends");$.ajax({url:"/ajax/check_friend_update",type:"post",dataType:"json",success:function(a){b=a.count_friends;c=a.updated;d=a.updated_date;if(c){e.find(".count .total-num").fadeOut(function(){$(this).text(b)}).fadeIn();e.find(".count .additional-num").fadeOut(function(){$(this).addClass("alert alert-success").text("更新しました")}).fadeIn();$("#update-friends").text("更新完了")}else{e.find(".count .additional-num").fadeOut(function(){$(this).addClass("alert alert-info").text("変更はありません")}).fadeIn();$("#update-friends").text("処理終了")}e.find(".last-update .date").fadeOut(function(){$(this).text(d)}).fadeIn()},error:function(){e.find(".count .additional-num").fadeOut(function(){$(this).addClass("alert alert-danger").text("もう一度お試しください")}).fadeIn();$("#update-friends").text("エラー")},complete:function(){hideLoader(".friends")}})}function checkProfileUpdate(){var h=false;var i,updated_value;var j=$(".wrap-profile");$.ajax({url:"/ajax/check_profile_update",type:"post",dataType:"json",success:function(d){h=d.updated;i=d.updated_date;updated_value=d.updated_value;if(h){$.each(updated_value,function(a,b){var c=a.split("_").join("-");if($("."+c)[0]){if(c.indexOf("image")!=-1){$("."+c).fadeOut(function(){$(this).attr("src",b.replace("_normal","_reasonably_small"))}).fadeIn();$("header").find(".twitter-profile img").fadeOut(function(){$(this).attr("src",b)}).fadeIn()}else{$("."+c).fadeOut(function(){$(this).text(b)}).fadeIn();if(c.indexOf("screen-name")!=-1){$("header").find(".twitter-profile a:last").fadeOut(function(){$(this).text("@"+b)}).fadeIn()}}}})}$(".updated-date").fadeOut(function(){$(this).text(i)}).fadeIn();hideLoader(".wrap-profile");var e=h?"更新完了":"処理終了";$("#update-profile").text(e);var f=h?"alert-success":"alert-info";var g=h?"更新しました":"変更はありません";j.find(".area-result .alert").addClass(f).text(g).fadeIn()},error:function(){j.find(".area-result .alert").addClass("alert-danger").text("もう一度お試しください").fadeIn();$("#update-profile").text("エラー");hideLoader(".wrap-profile")}})}var total_count=0;var oldest_id_str="";var continue_process="";var updated_date="";function updateStatus(){var d=$("#wrap-configurations").find(".tweets");var e=$("#update-statuses");$.ajax({url:"/ajax/update_statuses",type:"post",dataType:"json",data:{"oldest_id_str":oldest_id_str},success:function(a){continue_process=a.continue;updated_date=a.updated_date;if(continue_process){total_count+=a.count_saved;oldest_id_str=a.oldest_id_str;d.find(".additional-num").fadeOut(function(){$(this).text("+ "+total_count)}).fadeIn();updateStatus()}else{total_count+=a.count_saved;var b=0;var c=parseInt($(".tweets").find(".count .total-num").text());b=c+parseInt(total_count);d.find(".total-num").fadeOut(function(){$(this).text(b).fadeIn()});d.find(".additional-num").fadeOut(function(){$(this).addClass("alert alert-success").text(total_count+"件追加")}).fadeIn();d.find(".last-update .date").fadeOut(function(){$(this).text(updated_date)}).fadeIn();e.text("更新完了")}},error:function(){d.find(".additional-num").fadeOut(function(){$(this).addClass("alert alert-danger").text("もう一度お試しください")}).fadeIn();e.text("エラー");$(".loader").fadeOut()},complete:function(){if(!continue_process){$(".loader").fadeOut()}}})};function showDeleteCompleteMessage(a){var b="";var c=$("#modal-delete-account").find(".status");if(a){b="アカウント削除が完了しました。自動的にログアウトします。"}else{b="すみません！処理が完了しませんでした。画面をリロードしてもう一度お試しください。"}c.fadeOut(function(){$(this).text(b)}).fadeIn()}function showDeleteErrorMessage(){return showCompleteMessage(false)}function redirect(){location.href="/users/logout"}var total_imported_count=0;function getStatuses(b){var c=$(".wrap-progress-bar");var d=$(".wrap-tweet");var e=$("#start-import");var f="";var g=b;var h;$.ajax({url:"/ajax/acquire_statuses",type:"POST",dataType:"json",data:g,success:function(a){total_imported_count+=a.saved_count;f=a.noStatusAtAll;if(a.continue){$(".wrap-importing-status").fadeOut(function(){c.find(".total").html(total_imported_count+"件");d.find(".body").html(a.status.text);d.find(".date").html(a.status.date)});g.id_str_oldest=a.id_str_oldest;h=getPersentage(total_imported_count);getStatuses(g)}else{if(total_imported_count==0){e.text("...?");c.find(".progress").fadeOut(function(){c.append("<div class=\"alert alert-info\"><p>取得できるツイートが無いようです</p></div>");c.find(".alert").fadeIn()})}else{c.find(".bar").html("complete!");h=100;e.addClass('disabled');e.text(total_imported_count+"件取得しました");$(".progress").removeClass("active");hideLoader("#wrap-import")}}},error:function(){$(".progress").removeClass("active");hideLoader("#wrap-import");$(".wrap-progress-bar").fadeOut(function(){$(".wrap-lower").html("<div class=\"alert alert-warning\"><p>サーバーが混み合っているようです。<br/>すみませんが、しばらくしてからもう一度お試しください。</p></div>");$("#start-import").text("...oops")})},complete:function(){if(f){hideLoader("#wrap-import")}else{setProgress(h);$(".wrap-importing-status").fadeIn()}if(h==100){setTimeout(function(){location.href="/your/tweets"},2000)}}})}function getPersentage(a){a=parseInt(a);var b=parseInt($("#statuses-count").val());var c="";if(a>3200){c=(a/3200)*100}else{c=(a/b)*100}return parseInt(c)}function setProgress(a){$(".progress").find(".bar").css("width",a+"%")}function ajaxSwitchDashbord(b){var c=$("#wrap-dashbord");var d=$("#wrap-term-selectors");$.ajax({url:"/ajax/switch_dashbord",type:"post",dataType:"html",data:{"action_type":b},success:function(a){d.html(a);c.attr('data-type',b)},error:function(){alert("ページの読み込みがうまくいきませんでした。リロードしてみて下さい。")}})}function getDashbordType(){var a=$("#wrap-dashbord").data('type');if(!a){return false}return a}function ajaxSwitchTerm(b,c,d){if(!b||!c||!c||!d){alert("required params not supplied");return}var e=$("#wrap-timeline");e.html("<div class=\"cover\"><span>Loading</span></div>");var f=e.find('.cover');f.css("height",200);f.animate({opacity:0.8},200);$.ajax({type:'GET',dataType:'html',url:'/ajax/switch_term?ajax=true',data:{"date":b,"date_type":detectDateType(b),"action_type":c},success:function(a){$("#wrap-main").html(a)},error:function(a){alert("読み込みに失敗しました。画面をリロードしてください")},complete:function(){scrollToPageTop();$("#wrap-main").fadeIn('fast');$("#wrap-term-selectors").find("a").button('complete');if(d=="click"){window.history.pushState(null,null,href)}}})}function countStr(a,b){var c;var d=0;var e=0;while(true){c=a.indexOf(b,e);if(c!=-1){d++;e=c+1}else{break}}return d}function detectDate(a){var b=a.lastIndexOf("/");if(b==-1){return false}var c=a.substring(b+1);return c}function detectDateType(a){var b="-";var c;if(a.indexOf(b)==-1){if(a.length>=4){c="year"}else{c=false}}else{if(a.indexOf(b)==a.lastIndexOf(b)){c="month"}else{c="day"}}return c}function detectActionType(a){var b=a.indexOf("/");if(b==-1){return false}if(a.indexOf("public_timeline")!=-1){return"public_timeline"}var c=a.indexOf("/",b+1);if(c==-1){return false}var d=a.indexOf("/",c+1);if(d==-1){return a.substr(c+1)}else{var e=d-c;return a.substr(c+1,e-1)}}
