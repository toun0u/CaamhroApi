$(document).ready(function(){
	if($('object[type="application/x-shockwave-flash"]').length){
		$('object[type="application/x-shockwave-flash"]').each(function(){
			var url = $(this).attr('data'),
				urlPlayer = '/common/libs/f4player/player.swf?v1.3.5';
			if(url == undefined && $('embed:first',$(this)).length){
				url = $('embed:first',$(this)).attr('src');
				$('embed:first',$(this)).remove();
			}
			$(this).removeAttr('codebase');
			if($(this).attr('width') == undefined) $(this).attr('width',500);
			if($(this).attr('height') == undefined) $(this).attr('height',500);
			$('param[name="allowNetworking"]',this).remove();
			if(!$('param[name="movie"]',this).length){
				$(this).append('<param name="movie" value="'+urlPlayer+'" />');
			}else{
				$('param[name="movie"]',this).attr('value',urlPlayer);
			}
			$('param[name="allowScriptAccess"]',this).attr('value',"always");
			$(this).append('<param name="flashvars" value="skin=/common/libs/f4player/skins/mySkin.swf&video='+url+'" /><param value="true" name="swlivevonnect" /><param name="cachebusting" value="true" /><param name="scale" value="noscale" /><param name="allowfullscreen" value="true" />');
			$(this).attr('data',urlPlayer);
		});
	}
});