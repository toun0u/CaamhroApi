<div class="content-zone pa1">
	<div class="nopbottom desk-hidden" style="overflow:hidden">
		<a href="/index.php?op=panier"  class="mod right fondbuttonnoir pa1 rounded-pic mb1">
			<i class="icon-cart"></i>
			<span id="nbArtPanier">
				{if $panier.nb_art == 0}
					Votre panier (vide)
				{else}
					{$panier.nb_art}
					{if $panier.nb_art > 1}
						articles
					{else}
						article
					{/if}
				{/if}
			</span>
		</a>
		{if (isset($switch_user_logged_out))}
			<a href="/index.php?op=connexion" class="mod right pa1 rounded-pic mr2 fondbuttonnoir mb1">
				<i class="icon2-enter title-icon"></i>
				<span>
					Connexion
				</span>
			</a>
		{else}
			<a href="/index.php?op=compte" class="mod right pa1 rounded-pic mr2 fondbuttonnoir mb1">
				<i class="icon2-user3 title-icon"></i>
				<span>
					Mon compte
				</span>
			</a>
		{/if}
		<a href="/accueil.html" class="mod right fondbuttonnoir pa1 rounded-pic mr2">
			<i class="icon2-home title-icon"></i>
			<span>
				Accueil
			</span>
		</a>
	</div>
	<h1 class="txtcenter"><i class="icon-user title-icon"></i> Mon compte</h1>
	<br/>
	<hr class="mt0">
	<div class="gridcompte grid4 line pa2">
		{foreach from=$links item=link}
			{if $link.cond}
				<div class="mod txtcenter pa2">
					<a href="{$link.href}" title="{$link.label}"><i class="orange enormous icon2-{$link.img} title-icon"></i></a>
					<h3 class="medium nomargin txtcenter"><a href="{$link.href}" title="{$link.label}">{$link.label}</a></h3>
					<!--p class="w100 nopadding nomargin">{$link.comment}</p-->
				</div>

			{/if}
		{/foreach}
	</div>
</div>
