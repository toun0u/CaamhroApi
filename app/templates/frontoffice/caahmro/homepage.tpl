<div class="main">
	<header class="heading">
		{include file="_header.tpl"}
	</header>

	{include file="_desktop_menu.tpl"}

	{if isset($show_info_certiphyto) && $show_info_certiphyto}
		{include file="_info_certiphyto.tpl"}
	{/if}

	{$page.CONTENT}
	{include file="right.tpl"}
	<div class="mw1280p m-auto">
		<footer class="txtcenter content-zone">
			<a href="#" class="right scrollup"><i class="icon2-arrow-up"></i></a>
			<div class="menu_footer">
				{if isset($headings.root2.heading1)}
					{foreach from=$headings.root2.heading1 key=idh1 item=menuprincipal}
						<li class="border-left" id="home{$menuprincipal.POSITION}">
							{if $menuprincipal.SEL == "selected"}
								<a class="selected" title="{$menuprincipal.LABEL}" href="{$menuprincipal.LINK}">{$menuprincipal.LABEL}</a>
							{else}
								<a title="{$menuprincipal.LABEL}" href="{$menuprincipal.LINK}">{$menuprincipal.LABEL}</a>
							{/if}
						</li>
					{/foreach}
				{/if}
			</div>
			<div class="pa2 right">
				&copy; 2015 CAAHMRO.fr - Tous Droits Réservés - Powered by Dims
			</div>
		</footer>
	</div><!-- !wrap1280p/m-auto -->
</div>
