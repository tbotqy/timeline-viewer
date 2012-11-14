$(function(){ var userAgent = getUserAgent(); var uaWhiteList = ['chrome','safari','firefox']; var isValidUA = false; setTimeout(function(){ $.getScript('/js/twitter_follow_button.js'); $.getScript('/js/twitter_tweet_button.js'); $.getScript('//b.st-hatena.com/js/bookmark_button.js'); facebook(document, 'script', 'facebook-jssdk');},3000); $("#wrap-timeline-lower").on("click",".status-content",function(e){ var clicked = $(e.target); if(!clicked.is('a') && !clicked.is('i')){ $(this).find(".bottom").slideToggle('fast');}
}); $("#wrap-timeline-lower").on("click",".status-content .link-delete a",function(e){ e.preventDefault(); if(confirm('ツイートを削除します。よろしいですか？')){ var status_id_to_delete = $(this).parent().data('status-id'); $.ajax({ url: "/ajax/delete_status", type: "post", data:{"status_id_to_delete":status_id_to_delete}, dataType: "json", success: function(responce){ if(responce.owns){ if(responce.deleted){ $("div[data-status-id="+status_id_to_delete+"]").fadeOut();}else{ alert("ごめんなさい。削除に失敗しました。画面をリロードしてもう一度お試しください。");}
}else{ alert("不正な操作です。");}
}, error: function(){ alert("エラーが発生しました。");}
});}
}); $("#wrap-timeline-lower").on("click","#read-more",function(e){ var self = $(this); e.preventDefault(); var distance = self.offset().top; self.button('loading'); var elmOldestTimestamp = $(".oldest-timestamp"); var oldestTimestamp = elmOldestTimestamp.val(); console.log(oldestTimestamp); $.ajax({ type:"POST", dataType:"html", data:{ "oldest_timestamp":oldestTimestamp, "destination_action_type":detectActionType(location.pathname)
}, url: '/ajax/read_more', success: function(responce){ elmOldestTimestamp.remove(); $("#wrap-read-more").remove(); $(".wrap-each-status:last").after(responce);}, error: function(responce){ alert("読み込みに失敗しました。");}, complete: function(){ scrollDownToDestination(e,distance);}
});}); var wrap_progress_bar = $(".wrap-progress-bar"); var import_button = $("#start-import"); import_button.click(function(){ import_button.button('loading'); showLoader("#wrap-import"); wrap_progress_bar.fadeIn(function(){ $("#status").fadeIn();}); var data_to_post = {"id_str_oldest":""}; getStatuses(data_to_post);}); var elmErrorInner = $(".error-inner"); elmErrorInner.find(".description").click(function(e){ e.preventDefault(); elmErrorInner.find(".invite-friends").fadeIn();}); elmErrorInner.find(".invite-friends .close").click(function(e){ e.preventDefault(); elmErrorInner.find(".invite-friends").fadeOut();}); $("#update-profile").click(function(){ var self = $(this); self.button('loading'); self.after("<img class=\"loader\" src=\"/img/ajax-loader.gif\" />"); $(".wrap-profile").find(".loader").fadeIn(); checkProfileUpdate();}); $("#update-statuses").click(function(){ var self = $(this); self.button('loading'); self.after("<img class=\"loader\" src=\"/img/ajax-loader.gif\" />"); $(".tweets").find(".loader").fadeIn(); checkStatusUpdate();}); $("#update-friends").click(function(){ var self = $(this); self.button('loading'); self.after("<img class=\"loader\" src=\"/img/ajax-loader.gif\" />"); $(".friends").find(".loader").fadeIn(); checkFriendUpdate();}); var deleted = ""; $("#delete-account").click(function(){ var elmModalDeleteAccount = $("#modal-delete-account"); elmModalDeleteAccount.find(".modal-header .close").fadeOut(); elmModalDeleteAccount.find(".modal-footer .cancel-delete").addClass("disabled"); $(this).button('loading'); elmModalDeleteAccount
.find(".status")
.fadeOut(function(){ $(this).html("処理中...<img src=\"/img/ajax-loader.gif\" class=\"loader\" />");})
.fadeIn(); $.ajax({ url: '/ajax/delete_account', type: 'post', dataType: 'json', success: function(res){ deleted = res.deleted; showDeleteCompleteMessage(res.deleted);}, error: function(){ showDeleteErrorMessage();}, complete: function(){ if(deleted){ setTimeout( function(){ redirect();}, 3000 );}else{ alert("処理がうまくいきませんでした。");}
}
});}); $(window).scroll(function() { var topy = $(document).scrollTop(); var elmToPageTop = $(".to-page-top"); if (topy >= 200) { elmToPageTop.fadeIn();}else{ elmToPageTop.fadeOut();}
}); $(".to-page-top").find("a").click (function(e) { scrollToPageTop(e);}); $(".list-years").find("li").mouseover(function(){ var self = $(this); $(".list-years").find("a").removeClass("btn-primary selected"); self.find('a').addClass("btn-primary selected"); $("#wrap-list-months").find("ul").css('display','none'); $("#wrap-list-days").find("ul").css('display','none'); var year = self.attr('data-date'); $("#wrap-list-months").find("."+year).css('display','block');}); $(".list-months").find("li").mouseover(function(){ var self = $(this); $(".list-months").find("li").find("a").removeClass("btn-primary selected"); self.find('a').addClass("btn-primary selected"); $("#wrap-list-days").find("ul").css('display','none'); var month = self.attr('data-date'); $("#wrap-list-days").find("."+month).css('display','block');}); if('pushState' in history){ $("#wrap-term-selectors").find("a").click(function(e){ var self = $(this); e.preventDefault(); var href = self.attr('href'); var date = self.attr('data-date'); var date_type = self.attr('data-date-type'); var wrap_timeline = $("#wrap-timeline"); wrap_timeline.html("<div class=\"cover\"><span>Loading</span></div>"); var cover = wrap_timeline.find('.cover'); cover.css("height",200); cover.animate({ opacity: 0.8
},200); var path = location.pathname; var action_type = detectActionType(path); $.ajax({ type: 'GET', dataType: 'html', url:'/ajax/switch_term', data:{ "date":date, "date_type":date_type, "action_type":action_type
}, success: function(responce){ $("#wrap-main").html(responce);}, error: function(responce){ alert("読み込みに失敗しました。画面をリロードしてください");}, complete: function(){ scrollToPageTop(e); $("#wrap-main").fadeIn('fast'); $("#wrap-term-selectors").find("a").button('complete'); window.history.pushState(null,null,href);}
});});}
$("#wrap-list-years").find("a").click(function(){ $("#wrap-list-months").find(".selected").removeClass("selected btn-primary"); $("#wrap-list-days").find(".selected").removeClass("selected btn-primary"); $(this).addClass("selected btn-primary");}); $("#wrap-list-months").find("a").click(function(){ $("#wrap-list-days").find(".selected").removeClass("selected btn-primary"); $(this).addClass("selected btn-primary");}); $("#wrap-list-days").find("a").click(function(){ $("#wrap-list-days").find(".selected").removeClass("selected btn-primary"); $(this).addClass("selected btn-primary");});}); 
