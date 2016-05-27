<div class="pa1 nopbottom desk-hidden" style="overflow:hidden">
	<a href="/index.php?op=panier"  class="mod right fondbuttonnoir pa1 rounded-pic mb1">
		<i class="icon-cart"></i>
		<span id="nbArtPanier">
			{if $panier.nb_art == 0}
				{$smarty.session.cste.CATA_YOUR_CART} ({$smarty.session.cste._EMPTY})
			{else}
				{$panier.nb_art}
				{if $panier.nb_art > 1}
					{$smarty.session.cste.ARTICLES|lower}
				{else}
					{$smarty.session.cste._ARTICLE|lower}
				{/if}
			{/if}
		</span>
	</a>
	{if (isset($switch_user_logged_out))}
		<a href="/index.php?op=connexion" class="mod right pa1 rounded-pic mr2 fondbuttonnoir mb1">
			<i class="icon2-enter title-icon"></i>
			<span>
				{$smarty.session.cste.CATA_CONNECTION}
			</span>
		</a>
	{else}
			<a href="/index.php?op=compte" class="mod right pa1 rounded-pic mr2 fondbuttonnoir mb1">
			<i class="icon2-user3 title-icon"></i>
			<span>
				{$smarty.session.cste._PERSONAL_SPACE}
			</span>
		</a>
	{/if}
	<a href="/accueil.html" class="mod right fondbuttonnoir pa1 rounded-pic mr2">
		<i class="icon2-home title-icon"></i>
		<span>
			{$smarty.session.cste.CATA_HOME}
		</span>
	</a>
	{if !empty($arianne)}
		<div class="arianne">
			Vous êtes ici :
			{foreach from=$arianne item=i name=it}
				{if not $smarty.foreach.it.last}
					<a href="{$i.link}">
				{/if}

				{$i.label}

				{if not $smarty.foreach.it.last}
					</a>
				{/if}

				{if not $smarty.foreach.it.last} > {/if}
			{/foreach}
		</div>
	{/if}
</div>