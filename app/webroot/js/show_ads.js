(function(){var g=!0,h=null,j=!1,aa=function(a,b,c){return a.call.apply(a.bind,arguments)},ba=function(a,b,c){if(!a)throw Error();if(2<arguments.length){var e=Array.prototype.slice.call(arguments,2);return function(){var c=Array.prototype.slice.call(arguments);Array.prototype.unshift.apply(c,e);return a.apply(b,c)}}return function(){return a.apply(b,arguments)}},k=function(a,b,c){k=Function.prototype.bind&&-1!=Function.prototype.bind.toString().indexOf("native code")?aa:ba;return k.apply(h,arguments)};var l=(new Date).getTime();var ca=/&/g,ea=/</g,fa=/>/g,ga=/\"/g,n={"\x00":"\\0","\b":"\\b","\f":"\\f","\n":"\\n","\r":"\\r","\t":"\\t","\x0B":"\\x0B",'"':'\\"',"\\":"\\\\"},s={"'":"\\'"};var x=window,y,ha=h,z=document.getElementsByTagName("script");z&&z.length&&(ha=z[z.length-1].parentNode);y=ha;var ia=function(a){a=parseFloat(a);return isNaN(a)||1<a||0>a?0:a},ja=/^([\w-]+\.)*([\w-]{2,})(\:[0-9]+)?$/;var ka=function(){var a;a=(a="".match(ja))?a[0]:"pagead2.googlesyndication.com";return a};ka();var A=function(a){return!!a&&"function"==typeof a&&!!a.call},la=function(a,b){if(!(2>arguments.length))for(var c=1,e=arguments.length;c<e;++c)a.push(arguments[c])},ma=function(a,b){if(!(1E-4>Math.random())){var c=Math.random();if(c<b)return a[Math.floor(c/b*a.length)]}return h},B=function(a){try{return!!a.location.href||""===a.location.href}catch(b){return j}};var C=h,na=function(){if(!C){for(var a=window,b=a,c=0;a!=a.parent;)if(a=a.parent,c++,B(a))b=a;else break;C=b}return C};var D,E=function(a){this.c=[];this.b=a||window;this.a=0;this.d=h},oa=function(a,b){this.l=a;this.i=b};E.prototype.o=function(a,b){0==this.a&&0==this.c.length&&(!b||b==window)?(this.a=2,this.f(new oa(a,window))):this.g(a,b)};E.prototype.g=function(a,b){this.c.push(new oa(a,b||this.b));H(this)};E.prototype.p=function(a){this.a=1;a&&(this.d=this.b.setTimeout(k(this.e,this),a))};E.prototype.e=function(){1==this.a&&(this.d!=h&&(this.b.clearTimeout(this.d),this.d=h),this.a=0);H(this)};E.prototype.q=function(){return g};
E.prototype.nq=E.prototype.o;E.prototype.nqa=E.prototype.g;E.prototype.al=E.prototype.p;E.prototype.rl=E.prototype.e;E.prototype.sz=E.prototype.q;var H=function(a){a.b.setTimeout(k(a.m,a),0)};E.prototype.m=function(){if(0==this.a&&this.c.length){var a=this.c.shift();this.a=2;a.i.setTimeout(k(this.f,this,a),0);H(this)}};E.prototype.f=function(a){this.a=0;a.l()};
var pa=function(a){try{return a.sz()}catch(b){return j}},qa=function(a){return!!a&&("object"==typeof a||"function"==typeof a)&&pa(a)&&A(a.nq)&&A(a.nqa)&&A(a.al)&&A(a.rl)},I=function(){if(D&&pa(D))return D;var a=na(),b=a.google_jobrunner;return qa(b)?D=b:a.google_jobrunner=D=new E(a)},ra=function(a,b){I().nq(a,b)},sa=function(a,b){I().nqa(a,b)};var ta=/MSIE [2-7]|PlayStation|Gecko\/20090226/i,ua=/Android|Opera/,va=function(){var a=J,b=K.google_ad_width,c=K.google_ad_height,e=["<iframe"],d;for(d in a)a.hasOwnProperty(d)&&la(e,d+"="+a[d]);e.push('style="left:0;position:absolute;top:0;"');e.push("></iframe>");b="border:none;height:"+c+"px;margin:0;padding:0;position:relative;visibility:visible;width:"+b+"px";return['<ins style="display:inline-table;',b,'"><ins id="',a.id+"_anchor",'" style="display:block;',b,'">',e.join(" "),"</ins></ins>"].join("")};var wa=/^true$/.test("true")?g:j;var xa=function(a,b){var c=ka();b||(b=wa?"https":"http");return[b,"://",c,a].join("")};var ya=function(){},Aa=function(a,b,c){switch(typeof b){case "string":za(b,c);break;case "number":c.push(isFinite(b)&&!isNaN(b)?b:"null");break;case "boolean":c.push(b);break;case "undefined":c.push("null");break;case "object":if(b==h){c.push("null");break}if(b instanceof Array){var e=b.length;c.push("[");for(var d="",f=0;f<e;f++)c.push(d),Aa(a,b[f],c),d=",";c.push("]");break}c.push("{");e="";for(d in b)b.hasOwnProperty(d)&&(f=b[d],"function"!=typeof f&&(c.push(e),za(d,c),c.push(":"),Aa(a,f,c),e=
","));c.push("}");break;case "function":break;default:throw Error("Unknown type: "+typeof b);}},Ba={'"':'\\"',"\\":"\\\\","/":"\\/","\b":"\\b","\f":"\\f","\n":"\\n","\r":"\\r","\t":"\\t","\x0B":"\\u000b"},Ca=/\uffff/.test("\uffff")?/[\\\"\x00-\x1f\x7f-\uffff]/g:/[\\\"\x00-\x1f\x7f-\xff]/g,za=function(a,b){b.push('"');b.push(a.replace(Ca,function(a){if(a in Ba)return Ba[a];var b=a.charCodeAt(0),d="\\u";16>b?d+="000":256>b?d+="00":4096>b&&(d+="0");return Ba[a]=d+b.toString(16)}));b.push('"')};var L="google_ad_block google_ad_channel google_ad_client google_ad_format google_ad_height google_ad_host google_ad_host_channel google_ad_host_tier_id google_ad_output google_ad_override google_ad_region google_ad_section google_ad_slot google_ad_type google_ad_width google_adtest google_allow_expandable_ads google_alternate_ad_url google_alternate_color google_analytics_domain_name google_analytics_uacct google_bid google_city google_color_bg google_color_border google_color_line google_color_link google_color_text google_color_url google_container_id google_contents google_country google_cpm google_ctr_threshold google_cust_age google_cust_ch google_cust_gender google_cust_id google_cust_interests google_cust_job google_cust_l google_cust_lh google_cust_u_url google_disable_video_autoplay google_ed google_eids google_enable_ose google_encoding google_font_face google_font_size google_frame_id google_gl google_hints google_image_size google_kw google_kw_type google_language google_loeid google_max_num_ads google_max_radlink_len google_mtl google_num_radlinks google_num_radlinks_per_unit google_num_slots_to_rotate google_only_ads_with_video google_only_pyv_ads google_only_userchoice_ads google_override_format google_page_url google_previous_watch google_previous_searches google_referrer_url google_region google_reuse_colors google_rl_dest_url google_rl_filtering google_rl_mode google_rt google_safe google_sc_id google_scs google_skip google_tag_info google_targeting google_tdsma google_tfs google_tl google_ui_features google_ui_version google_video_doc_id google_video_product_type google_with_pyv_ads google_yt_pt google_yt_up".split(" ");var Da=function(a){this.b=a;a.google_iframe_oncopy||(a.google_iframe_oncopy={handlers:{}});this.j=a.google_iframe_oncopy},Ea;var M="var i=this.id,s=window.google_iframe_oncopy,H=s&&s.handlers,h=H&&H[i],w=this.contentWindow,d;try{d=w.document}catch(e){}if(h&&d&&(!d.body||!d.body.firstChild)){if(h.call){setTimeout(h,0)}else if(h.match){w.location.replace(h)}}";
/[&<>\"]/.test(M)&&(-1!=M.indexOf("&")&&(M=M.replace(ca,"&amp;")),-1!=M.indexOf("<")&&(M=M.replace(ea,"&lt;")),-1!=M.indexOf(">")&&(M=M.replace(fa,"&gt;")),-1!=M.indexOf('"')&&(M=M.replace(ga,"&quot;")));Ea=M;Da.prototype.set=function(a,b){this.j.handlers[a]=b;this.b.addEventListener&&this.b.addEventListener("load",k(this.k,this,a),j)};Da.prototype.k=function(a){a=this.b.document.getElementById(a);var b=a.contentWindow.document;if(a.onload&&b&&(!b.body||!b.body.firstChild))a.onload()};var Fa=function(){var a="script";return["<",a,' src="',xa("/pagead/js/r20121031/r20120730/show_ads_impl.js",""),'"></',a,">"].join("")},Ga=function(){var a="script";return["<",a,' src="',xa("/pagead/expansion_embed.js"),'"></',a,">"].join("")},Ha=function(a){var b;if(!(b="expt"!=a.google_expand_experiment))a:{var c=a.document;try{var e;if(!(e=a.google_allow_expandable_ads===
j)){var d;if(!(d=!c.body)){var f;if(!(f=a.google_ad_output&&"html"!=a.google_ad_output)){var m;if(!(m=isNaN(a.google_ad_height))){var t;if(!(t=isNaN(a.google_ad_width))){var v;if(!(v=c.domain!=a.location.hostname)){var u;b:{a=navigator;var p=a.userAgent,F=a.platform;if(/Win|Mac|Linux/.test(F)&&!/^Opera/.test(p)){var R=(/WebKit\/(\d+)/.exec(p)||[0,0])[1],da=(/rv\:(\d+\.\d+)/.exec(p)||[0,0])[1];if(/Win/.test(F)&&/MSIE.*Trident/.test(p)&&7<c.documentMode||!R&&"Gecko"==a.product&&1.7<da&&!/rv\:1\.8([^.]|\.0)/.test(p)||
524<R){u=g;break b}}u=j}v=!u}t=v}m=t}f=m}d=f}e=d}if(e){b=g;break a}}catch(S){b=g;break a}b=j}return b?j:g},Ia=function(a,b,c,e){return function(){var d=j;e&&I().al(3E4);try{if(B(a.document.getElementById(b).contentWindow)){var f=a.document.getElementById(b).contentWindow,m=f.document;if(!m.body||!m.body.firstChild)m.open(),f.google_async_iframe_close=g,m.write(c)}else{var t=a.document.getElementById(b).contentWindow,v;f=c;f=String(f);if(f.quote)v=f.quote();else{for(var m=['"'],u=0;u<f.length;u++){var p=
f.charAt(u),F=p.charCodeAt(0),R=m,da=u+1,S;if(!(S=n[p])){var G;if(31<F&&127>F)G=p;else{var r=p;if(r in s)G=s[r];else if(r in n)G=s[r]=n[r];else{var q=r,w=r.charCodeAt(0);if(31<w&&127>w)q=r;else{if(256>w){if(q="\\x",16>w||256<w)q+="0"}else q="\\u",4096>w&&(q+="0");q+=w.toString(16).toUpperCase()}G=s[r]=q}}S=G}R[da]=S}m.push('"');v=m.join("")}t.location.replace("javascript:"+v)}d=g}catch(vb){t=na().google_jobrunner,qa(t)&&t.rl()}d&&(new Da(a)).set(b,Ia(a,b,c,j))}},Ja=Math.floor(1E6*Math.random()),Ka=
function(a){a=a.data.split("\n");for(var b={},c=0;c<a.length;c++){var e=a[c].indexOf("=");-1!=e&&(b[a[c].substr(0,e)]=a[c].substr(e+1))}b[1]==Ja&&(window.google_top_url=b[3])};var La=ia("0.0"),Ma=ia("0.001");window.google_loader_used=g;var N=window;if(!("google_onload_fired"in N)){N.google_onload_fired=j;var Na=function(){N.google_onload_fired=g};N.addEventListener?N.addEventListener("load",Na,j):N.attachEvent&&N.attachEvent("onload",Na)}var Oa=window,Pa=2;try{Oa.top.document==Oa.document?Pa=0:B(Oa.top)&&(Pa=1)}catch(Qa){}
if(2===Pa&&top.postMessage&&!window.google_top_experiment&&(window.google_top_experiment=ma(["jp_e","jp_c"],Ma),"jp_e"===window.google_top_experiment)){var O=window;O.addEventListener?O.addEventListener("message",Ka,j):O.attachEvent&&O.attachEvent("onmessage",Ka);var Ra={"0":"google_loc_request",1:Ja},Sa=[],Ta;for(Ta in Ra)Sa.push(Ta+"="+Ra[Ta]);top.postMessage(Sa.join("\n"),"*")}window.google_expand_experiment||(window.google_expand_experiment=ma(["expt","control"],La)||"none");var Ua;
if(window.google_enable_async===j)Ua=0;else{var Va=navigator.userAgent;Ua=(ta.test(Va)||ua.test(Va)?j:g)&&!window.google_container_id&&(!window.google_ad_output||"html"==window.google_ad_output)}
if(Ua){var Wa=window;Wa.google_unique_id?++Wa.google_unique_id:Wa.google_unique_id=1;for(var K=window,_script$$inline_83="script",P,J={allowtransparency:'"true"',frameborder:'"0"',height:'"'+K.google_ad_height+'"',hspace:'"0"',marginwidth:'"0"',marginheight:'"0"',onload:'"'+Ea+'"',scrolling:'"no"',vspace:'"0"',width:'"'+K.google_ad_width+'"'},Xa=K.document,Q=J.id,Ya=0;!Q||K.document.getElementById(Q);)Q="aswift_"+Ya++;J.id=Q;J.name=Q;Xa.write(va());P=Q;var Za;K.google_page_url&&(K.google_page_url=
String(K.google_page_url));for(var $a=[],ab=0,bb=L.length;ab<bb;ab++){var cb=L[ab];if(K[cb]!=h){var db;try{var eb=[];Aa(new ya,K[cb],eb);db=eb.join("")}catch(fb){}db&&la($a,cb,"=",db,";")}}Za=$a.join("");var T=window,gb=T.google_ad_output,U=T.google_ad_format;if(!U&&("html"==gb||gb==h))U=T.google_ad_width+"x"+T.google_ad_height;U=U&&(!T.google_ad_slot||T.google_override_format)?U.toLowerCase():"";T.google_ad_format=U;var hb,ib=[x.google_ad_slot,x.google_ad_format,x.google_ad_type,x.google_ad_width,
x.google_ad_height];if(y){var V;if(y){for(var jb=[],kb=0,W=y;W&&25>kb;W=W.parentNode,++kb)jb.push(9!=W.nodeType&&W.id||"");V=jb.join()}else V="";V&&ib.push(V)}var lb=0;if(ib){var mb=ib.join(":"),nb=mb.length;if(0==nb)lb=0;else{for(var X=305419896,ob=0;ob<nb;ob++)X^=(X<<5)+(X>>2)+mb.charCodeAt(ob)&4294967295;lb=0<X?X:4294967296+X}}hb=lb.toString();a:{var Y=window,Z=Y.google_async_slots;Z||(Z=Y.google_async_slots={});var pb=Y.google_unique_id,$=String("number"==typeof pb?pb:0);if($ in Z&&($+="b",$ in
Z))break a;Z[$]={sent:j,w:Y.google_ad_width||"",h:Y.google_ad_height||"",adk:hb,type:Y.google_ad_type||"",slot:Y.google_ad_slot||"",fmt:Y.google_ad_format||"",cli:Y.google_ad_client||"",saw:[]}}for(var qb=0,rb=L.length;qb<rb;qb++)K[L[qb]]=h;var sb=(new Date).getTime(),tb=K.google_top_experiment,ub=K.google_expand_experiment,wb="";Ha(K)&&(wb=Ga());var xb=["<!doctype html><html><body><",_script$$inline_83,">",Za,"google_show_ads_impl=true;google_unique_id=",K.google_unique_id,';google_async_iframe_id="',
P,'";google_ad_unit_key="',hb,'";google_start_time=',l,";",tb?'google_top_experiment="'+tb+'";':"",ub?'google_expand_experiment="'+ub+'";':"","google_bpp=",sb>l?sb-l:1,";</",_script$$inline_83,">",wb,Fa(),"</body></html>"].join("");(K.document.getElementById(P)?ra:sa)(Ia(K,P,xb,g))}else window.google_start_time=l,!("object"==typeof window.n&&"function"==typeof window.n.createIframe)&&Ha(window)&&document.write(Ga()),document.write(Fa());})();
