<div class="slider smallCarousel smallCarousel{$slideshow.id}">
	<h3 class="titreSlider slider{$slideshow.id}">{$slideshow.nom}</h3>
	<ul id="slider{$slideshow.id}" class="slider">
	{foreach from=$slideshow.slide item=slide}
	<li>
		<div class="caption {$slide.descr_position} caption{$slideshow.id}">
			{if isset($slide.descr_courte) && $slide.descr_courte != ''}
				{$slide.descr_courte}
			{/if}

			{if isset($slide.lien)}
			<a class="block" href="{$slide.lien}">Voir le détail</a>
			{/if}
		</div>
		{if $slide.isVideo}
			<iframe frameBorder="0" scrolling="no" name="small_video_frame" class="small_video_frame" style="border:0;width:236px;height:133px;margin:0;padding:0 7px 0 7px;" src="{$slide.iframe_url}"></iframe>
		{else}
			<img src="{$slide.filePath}" alt="{$slide.descr_courte}" />
		{/if}
	</li>
	{/foreach}
	</ul>
</div>

<link href="/common/templates/objects/slideshows/anythingslider.css" rel="stylesheet" type="text/css" />
<link href="/common/templates/objects/slideshows/theme_smallCarousel.css" rel="stylesheet" type="text/css" />
<!--[if IE 7]>
    <link rel="stylesheet" type="text/css" href="/common/templates/objects/slideshows/theme_smallCarousel_ie7.css" media="screen" />
<![endif]-->
<!--[if IE 8]>
    <link rel="stylesheet" type="text/css" href="/common/templates/objects/slideshows/theme_smallCarousel_ie8.css" media="screen" />
<![endif]-->
<!--[if IE 9]>
    <link rel="stylesheet" type="text/css" href="/common/templates/objects/slideshows/theme_smallCarousel_ie9.css" media="screen" />
<![endif]-->
<script type="text/javascript" language="javascript" src="/common/templates/objects/slideshows/jquery.anythingslider.min.js"></script>
<script type="text/javascript">
{literal}
$(function() {
	$('#slider{/literal}{$slideshow.id}{literal}')
		.anythingSlider({
			'width' : 250,
			'height' : 260,
			'theme' : 'cpe',
			'buildArrows' : true,
			'hashTags' : false,
			'autoPlayLocked' : false,
			'resumeDelay' : 0,
			'delay' : 3500,
			'startStopped' : true,
			onSlideComplete : function(){
				$(".smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav a").css({'background': '#777'});
				$(".smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav a").hover(
				  function () {
				    $(this).css('background', '#{/literal}{$slideshow.color}{literal}');
				  },
				  function () {
				    $(this).css({'background': '#777'});
				  });
				$(".smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav a.cur").hover(
				  function () {
				    $(this).css({'background': '#{/literal}{$slideshow.color}{literal}'});
				  },
				  function () {
				    $(this).css({'background': '#{/literal}{$slideshow.color}{literal}'});
				  });
				$(".smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav a.cur").css({'background': '#{/literal}{$slideshow.color}{literal}'});
			}
		})
		.find('.panel')
			.find('div[class*=caption]').css({position:'absolute'}).end()
			.addClass('smallCarousel');
});

$(document).ready(function() {
	$(".titreSlider.slider{/literal}{$slideshow.id}{literal}").css('background-color', '#{/literal}{$slideshow.color}{literal}');
	$(".caption.bottom.caption{/literal}{$slideshow.id}{literal}").css('color', '#{/literal}{$slideshow.color}{literal}');

	$(".smallCarousel{/literal}{$slideshow.id}{literal} .activeSlider .thumbNav a").css({'background': '#777'});
	$(".smallCarousel{/literal}{$slideshow.id}{literal} .activeSlider .thumbNav a.cur").css({'background': '#{/literal}{$slideshow.color}{literal}'});

	$(".smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav a").css({'background': '#777'});
	$(".smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav a").hover(
	  function () {
	    $(this).css('background', '#{/literal}{$slideshow.color}{literal}');
	  },
	  function () {
	    $(this).css({'background': '#777'});
	  });
	$(".smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav a.cur").hover(
	  function () {
	    $(this).css({'background': '#{/literal}{$slideshow.color}{literal}'});
	  },
	  function () {
	    $(this).css({'background': '#{/literal}{$slideshow.color}{literal}'});
	  });
	$(".smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav a.cur").css({'background': '#{/literal}{$slideshow.color}{literal}'});
	$(".smallCarousel{/literal}{$slideshow.id}{literal} .arrow a").css({'background-color': '#{/literal}{$slideshow.color}{literal}'});

	$(".smallCarousel .anythingControls").removeAttr("style").addClass("anythingControl").removeClass("anythingControls");

	// Centrage dynamique des boutons de navigation
	var thumbNavWidthSmall = $('.smallCarousel{/literal}{$slideshow.id}{literal} .thumbNav').width();
	var contentWidthSmall = $('.smallCarousel{/literal}{$slideshow.id}{literal} .anythingSlider').width();
	var contentHeightSmall = $('.smallCarousel{/literal}{$slideshow.id}{literal} .small_video_frame').height();
	$(".smallCarousel{/literal}{$slideshow.id}{literal} .anythingControl").css('left', ((contentWidthSmall / 2) - (thumbNavWidthSmall / 2)));
	$(".smallCarousel{/literal}{$slideshow.id}{literal} .anythingControl").css('top', (contentHeightSmall + 6));

	$(".smallCarousel{/literal}{$slideshow.id}{literal} .back").css('left', (contentWidthSmall / 2) - (thumbNavWidthSmall / 2) - 15);
	$(".smallCarousel{/literal}{$slideshow.id}{literal} .forward").css('left', (contentWidthSmall / 2) + (thumbNavWidthSmall / 2) + 15 - 11);

});
{/literal}
</script>
