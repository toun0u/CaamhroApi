<div id="bigSliderContainer">
	<ul id="slider{$slideshow.id}" class="slider">
		{if $slideshow.slide|@count > 0}
			{foreach from=$slideshow.slide item=slide}
				<li>
					<div class="caption {$slide.descr_position} captionBigSlider">
						{if isset($slide.titre) && $slide.titre != ''}
							<h3 class="topSliderTitre">{$slide.titre}</h3>
						{/if}
						{if isset($slide.descr_courte) && $slide.descr_courte != ''}
							<p class="topSliderDescription">{$slide.descr_courte}</p>
						{/if}

						{if isset($slide.lien)}
						<a class="block" href="{$slide.lien}">Voir le détail</a>
						{/if}
					</div>
					{if $slide.isVideo}
						<iframe frameBorder="0" scrolling="no" name="big_video_frame" class="big_video_frame" style="border:0;width:100%;height:100%;margin:0;padding:0;" src="{$slide.iframe_url}"></iframe>
					{else}
						<img src="{$slide.filePath}" alt="{$slide.descr_courte}" />
					{/if}
				</li>
			{/foreach}
		{/if}
	</ul>
{if $slideshow.slide|@count > 0}
	<link href="/common/templates/objects/slideshows/anythingslider.css" rel="stylesheet" type="text/css" />
	<link href="/common/templates/objects/slideshows/theme_topCarousel.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" language="javascript" src="/common/templates/objects/slideshows/jquery.anythingslider.min.js"></script>
	<script type="text/javascript">
	{literal}
	$(document).ready(function() {
		$('#slider{/literal}{$slideshow.id}{literal}')
			.anythingSlider({
				'width' : 765,
				'height' : 280,
				'theme' : 'cpe',
				'buildArrows' : true,
				'hashTags' : false,
				'autoPlayLocked' : false,
				'resumeDelay' : 0,
				'delay' : 3500,
				'startStopped' : true,
				'appendControlsTo' : ".controlBigSlideshow",
				onSlideComplete : function(){
					$(".controlBigSlideshow .thumbNav a").css({'background': '#777'});
					$(".controlBigSlideshow .thumbNav a").hover(
					  function () {
					    $(this).css('background', '#{/literal}{$slideshow.color}{literal}');
					  },
					  function () {
					    $(this).css({'background': '#777'});
					  });
					$(".controlBigSlideshow .thumbNav a.cur").hover(
					  function () {
					    $(this).css({'background': '#{/literal}{$slideshow.color}{literal}'});
					  },
					  function () {
					    $(this).css({'background': '#{/literal}{$slideshow.color}{literal}'});
					  });
					$(".controlBigSlideshow .thumbNav a.cur").css({'background': '#{/literal}{$slideshow.color}{literal}'});
				}
			})
			.find('.panel')
				.find('div[class*=caption]').css({position:'absolute'}).end();

		$("div#bigSliderContainer").prev("p").remove();
		$("div#bigSliderContainer").next("p").remove();

		$("#topContainer").css('background-color','#{/literal}{$slideshow.color}{literal}');

		$(".bloc_texte > div").removeAttr('style','');
		$(".arrow").appendTo(".controlBigSlideshow");

		$(".controlBigSlideshow .thumbNav a").css({'background': '#777'});
		$(".controlBigSlideshow .thumbNav a.cur").css({'background': '#{/literal}{$slideshow.color}{literal}'});

        if(document.all && !window.opera) $('#slider1').data('AnythingSlider').gotoPage(1);

		$(".controlBigSlideshow .thumbNav a").css({'background': '#777'});
		$(".controlBigSlideshow .thumbNav a").hover(
		  function () {
		    $(this).css('background', '#{/literal}{$slideshow.color}{literal}');
		  },
		  function () {
		    $(this).css({'background': '#777'});
		  });
		$(".controlBigSlideshow .thumbNav a.cur").hover(
		  function () {
		    $(this).css({'background': '#{/literal}{$slideshow.color}{literal}'});
		  },
		  function () {
		    $(this).css({'background': '#{/literal}{$slideshow.color}{literal}'});
		  });
		$(".controlBigSlideshow .thumbNav a.cur").css({'background': '#{/literal}{$slideshow.color}{literal}'});

		$(".controlBigSlideshow .arrow").css({'background-color': '#{/literal}{$slideshow.color}{literal}'});
	});
	{/literal}
	</script>
{/if}
</div>
