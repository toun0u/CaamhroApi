<link href="/modules/catalogue/templates/slideshows/anythingslider.css" rel="stylesheet" type="text/css" />
<link href="/modules/catalogue/templates/slideshows/theme-alone.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/modules/catalogue/templates/slideshows/jquery.anythingslider.min.js"></script>

<ul id="slider{$slideshow.id}" class="slider">
{foreach from=$slideshow.slide item=slide}
<li>
	<div class="caption-{$slide.descr_position}">
		{if isset($slide.descr_courte) && $slide.descr_courte != ''}
			{$slide.descr_courte}
		{/if}
		{if isset($slide.lien)}
		<!--a class="block" href="{$slide.lien}"><img src="/templates/frontoffice/artifetes/gfx/cliquez_ici_slider.png" alt="Suivre le lien {$slide.lien}" /></a-->
		{/if}
	</div>
	<img src="{$slide.image}" alt="{$slide.descr_courte}" />
</li>
{/foreach}
</ul>

<script type="text/javascript">
{literal}
$(function() {
	$('#slider{/literal}{$slideshow.id}{literal}')
		.anythingSlider({
			'width' : 755,
			'height' : 253,
			'theme' : 'alone',
			'buildArrows' : false,
			'hashTags' : false,
			'autoPlayLocked' : true,
			'resumeDelay' : 0,
			'delay' : 3500
			})
		.find('.panel')
			.find('div[class*=caption]').css({position:'absolute'}).end()
});
{/literal}
</script>
