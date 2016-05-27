<div class="action_nav phone-hidden">
	<div class="bloc_action">
		<a href="/accueil.html">
			<i class="icon2-home title-icon"></i>
			<span>
				{$smarty.session.cste.CATA_HOME}
			</span>
		</a>
		{if (isset($switch_user_logged_out))}
			<a href="/index.php?op=connexion" class="phone-hidden">
				<i class="icon2-enter title-icon"></i>
				<span>
					{$smarty.session.cste.CATA_CONNECTION}
				</span>
			</a>
		{else}
			<a href="/index.php?op=compte" class="phone-hidden">
				<i class="icon2-user3 title-icon"></i>
				<span>
					{$smarty.session.cste._PERSONAL_SPACE}
				</span>
			</a>
			<a title="Me déconnecter" href="/index.php?dims_logout=1">
				<i class="icon2-exit title-icon"></i>
				{$smarty.session.cste._SIGN_OUT}
			</a>
			<hr class="bgwhite">
			<a href="/index.php?op=panier"  class="phone-hidden">
				<i class="icon-cart"></i>
				<span id="nbArtPanier">
					{if isset($panier)}
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
					{/if}
				</span>
			</a>
		{/if}
	</div>
</div>