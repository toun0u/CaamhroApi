<div class="content-zone pa1">
	{include file="_mobile_menu.tpl"}
	<div class="pa1">
		<div id="catalogue_content" class="phone-hidden">
			<div class="arianne">
				Vous êtes ici :
				{foreach from=$ariane item=i name=it}
					<a href="{$i.link}">
						{$i.label}
					</a>
					{if not $smarty.foreach.it.last} > {/if}
				{/foreach}
			</div>
		</div>
	</div>
	<h3 class="titleh2_orange"><i class="icon-user title-icon orange"></i> {$smarty.session.cste._PERSONAL_SPACE}</h3>
	<br/>
	<hr class="mt0">
	<div class="gridcompte grid6 line pa2">
		{foreach from=$links item=link}
			{if $link.cond}
				<div class="mod txtcenter pa2 item_compte">
					<a href="{$link.href}" title="{$link.label}"><i class="orange enormous icon2-{$link.img} title-icon"><font style="font-family: helvetica">{$link.text}</font></i></a>
					<h3 class="medium nomargin txtcenter"><a href="{$link.href}" title="{$link.label}">{$link.label}</a></h3>
				</div>
			{/if}
		{/foreach}
	</div>
</div>
