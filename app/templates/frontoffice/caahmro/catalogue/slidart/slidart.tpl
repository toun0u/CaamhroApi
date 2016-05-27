{assign var=nb_cols value=3}

<div id="slideshow_{$slider.id}" class="slideshow">
	<div id="slidesContainer_{$slider.id}" class="slidesContainer">
		{foreach from=$articles item=art_slide name=art_slide}

			{if $smarty.foreach.it.index % $nb_cols == 0}
				<div class="grid{$nb_cols}">
			{/if}

			<div class="slide_{$slider.id} slide">
				{include file='slide.tpl' article=$art_slide}
			</div>

			{if not $smarty.foreach.it.first and ($smarty.foreach.it.index + 1) % $nb_cols == 0}
				</div>
			{/if}

		{/foreach}
	</div>
</div>
