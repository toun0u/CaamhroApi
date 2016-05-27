<div id="layerslider">
	{foreach from=$slideshow.slide item=slide}
	<div class="ls-layer" rel="slidedelay: 3000">
		<div class="line" style="width:100%">
			<div style="float:left">
				<img src="{$slide.filePath}" alt="Icone slider"  rel="durationin: 5800; easingin: easeOutQuad" class="ls-s1">
			</div>
		</div>
	</div>
	{/foreach}
</div>
<script type="text/javascript">
{literal}
$(function() {
	$('#slider{/literal}{$slideshow.id}{literal}')
		.anythingSlider({
			'buildArrows' : false,
			'hashTags' : false,
			'autoPlayLocked' : true,
			'resumeDelay' : 0,
			'delay' : 500,
			'enableNavigation' : false,
			'toggleControls'      : false,
			})
		.find('.panel')
			.find('div[class*=caption]').css({position:'absolute'}).end()
});
{/literal}
</script>
