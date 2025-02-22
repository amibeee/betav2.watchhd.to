<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="description" content="momovi.com HTML5 hls video player. Easy to use multi-platform video player. HLS vod and live streams on every device">
  <meta name="author" content="momovi.com - (c) 2014 The Netherlands">
  <meta property="og:title" content="momovi.com HTML5 hls video player">
  <meta property="og:description" content="momovi.com HTML5 hls video player. Easy to use multi-platform video player. HLS vod and live streams on every device">
  
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144-precomposed.png">
  <link rel="shortcut icon" href="favicon.ico">
  <title>momovi.com HTML5 hls video player</title>

  <script>
  function redirect_at_end(){  }
  </script>
  <script src="static/js/video.js"></script>
  <link href="static/css/video.css" rel="stylesheet" type="text/css">

  <style>
  body{
	margin: 0;
	padding: 0;
	background-color:#000000;
  } 
  </style> 
  <!--[if IE 9]>
  <link rel="stylesheet" type="text/css" href="static/css/ie9.css">
  <![endif]-->

  <!--[if IE 8]>
  <link rel="stylesheet" type="text/css" href="static/css/ie8.css">
  <![endif]-->
</head>
<body>


<div id="video-player">	</div>


<script src="static/js/jquery-1.10.2.min.js"></script>
<script src="static/js/jquery.ba-throttle-debounce.min.js"></script>
<script>
$.ajaxSetup({
    cache: false
});

function resizeVideo(){
	if( $('#momovi_video').length ) { 
		$("#momovi_video").width($( window ).width());
		$("#momovi_video").height($( window ).height());
	}
	if( $('#momovi_video_html5_api').length ) { 
	  $("#momovi_video_html5_api").width($( window ).width());
	  $("#momovi_video_html5_api").height($( window ).height());
	}
}
$(window).resize( function(){
   resizeVideo();
   setTimeout(resizeVideo, 1500);
});

$( document ).ready(
	resizeVideo
);

function newplayer(data){
		var poster = data.poster
		
		var posterimg = "<div id=\"video-player\"><img id=\"momovi_video\" src=\"" +poster+ "\"></div>";
		$("#video-player").replaceWith(posterimg);
		hlsplayer(data)
}

function checkm3u8available_helper(hlsdata,depth){
	if(depth > 20){
		alert("Could not open stream");
		return
	}
	var is_msie = navigator.userAgent.toLowerCase().indexOf('msie') > -1;
	if(is_msie){
	  runplayer(hlsdata);
	} else{
	var hlsurl = hlsdata.stream_url
	$.get(hlsurl,function(data,status){
    	if(data.indexOf(".ts") != -1 || data.indexOf("STREAM-INF") != -1){
			runplayer(hlsdata);
    	} else {
			depth=depth+1;
    		setTimeout(function(){checkm3u8available_helper(hlsdata,depth)},350);
    	}
  	 });
  	}
}

function checkm3u8available(data){
	var is_available = checkm3u8available_helper(data,0);

}

function hlsplayer(data){
	checkm3u8available(data);
}
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function runplayer(data) {
	var autoplay = "autoplay",
	    loop = "",
	    cast = "",
	    controls = "controls";
	var hlsurl = data.stream_url;
	var poster = data.poster;
	var urlvars=getUrlVars();
	if(urlvars['autoplay'] != null){ 
	 if(urlvars['autoplay'] == "false"){
	   autoplay = ""
	 }
	}
	if(urlvars['loop']){ 
	  if(urlvars['loop'] == "true" ){
	   loop = "loop"
	 }
	}

	if(urlvars['cast']){ 
	  if(urlvars['cast'] == "true" ){
	   cast = "cast"
	 }
	}	
	
	if( autoplay == "autoplay" ){
	  if(urlvars['controls']){ 
		if(urlvars['controls'] == "false" ){
		   controls = "";
		}
	  }
	}
	var vidplayer = "<video "+controls + "  "+cast + "id=\"momovi_video\" class=\"video-js vjs-default-skin\" preload=\"none\" width=\"300px\" height=\"200px\" "+autoplay + " " + loop + " poster=\""+ poster + "\"    data-setup=\'{ }'\>  <source src=\""+hlsurl+"\" type=\'video/mp4\' /></video>"
	$("#video-player").replaceWith(vidplayer);
	vjs.autoSetup();
	resizeVideo();
}
</script>
<script src='modernizr.custom.82348.js' type="text/javascript" charset="utf-8"></script>

<div id="jsonploader"></div>

<script language="JavaScript"> 
<!--
var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
var is_msie = navigator.userAgent.toLowerCase().indexOf('msie') > -1;
var is_safari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;
var is_idevice = (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i));

newplayer({"stream_url": "<?php echo $_GET['video']; ?>","poster":"video/poster.jpg"});
-->
</script>
</body>
</html>



