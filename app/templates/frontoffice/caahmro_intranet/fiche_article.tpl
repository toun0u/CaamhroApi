<div class="content-zone">
	<div class="pa1 nopbottom desk-hidden" style="overflow:hidden">
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
	<a class="blue" href="javascript:history.go(-1);"><i class="icon icon-arrow-left"></i><u>Retour au catalogue</u></a>
	<!--hr class="mt0"-->
	<div class="pa1 nopbottom phone-hidden" style="overflow:hidden">
		<div class="arianne">
			Vous êtes ici :
			{foreach from=$arianne item=i name=it}
				<a href="{$i.link}">
					{$i.label}
				</a>
				{if not $smarty.foreach.it.last} > {/if}
			{/foreach}
		</div>
	</div>
	<div class="mw1280p m-auto">
		<div class="pa1">
			<h2 class="secondary-title">
				<div class="product-title inbl"><i class="icon-tags"></i></div>
				{$article.label}
			</h2>

			<div class="ma3 pl2">
				<div class="grid3-1 line">
					<div class="mod">
						<div class="grid1-2 line">
							<div class="mod">
								<img src="{$article.image}" alt="{$article.label}" class="rounded-pic light-shadow w300p">
							</div>
							<div class="mod">
								<h4 class="mb2">Informations générales :</h4>
								Stock :
								{if $article.qte == 0 }
									Non disponible
								{else}
									Disponible
								{/if}
								<br>
								Référence : {$article.reference} <br>
								{if $article.cond != ""}
									Conditionnement : {$article.cond}<br>
								{/if}
								Partagez : <br>
								<a href=""><i class="icon-facebook biggest facebook"></i></a>
								<a href=""><i class="icon-twitter biggest twitter"></i></a>
								<a href=""><i class="icon-googleplus biggest gplus"></i></a>
							</div>
						</div>
					</div>
					{if $cata_mode_B2C || $smarty.session.dims.connected}
						<div class="mod">
							<div class="secondary-zone pa2 txtcenter rounded-pic content-zone">
								<h2>{$article.prix} &euro; HT</h2>
								{if $article.taxe_certiphyto > 0}
									Dont taxe phytosanitaire : {$article.taxe_certiphyto} € HT
								{/if}

								<label for="" class="mt1">Quantité :</label>
								<input id="qte" name="qte" type="number" min="0" class="w50p mt2" value="1">
								<br>
								<input id="add-to-cart-button" type="button" class="btn btn-primary mt2" value="Ajouter au panier">
							</div>
						</div>
					{/if}
				</div>

				{if $article.description != ''}
					<div class="grid2 line mt2">
						<div class="mod">
							<h4 class="mb2">Caractéristiques techniques :</h4>

							<div class="flash info mt2">
								<i>{$article.description}</i>
							</div>
						</div>
					</div>
				{/if}

				<div class="line mt2">
					{if sizeof($article.linked_articles)}
						{foreach from=$article.linked_articles key=type_link item=linked_arts}
							<h4 class="mb2">{$a_link_types[$type_link].label} :</h4>

							<table class="alternate-light-inv">
								{foreach from=$linked_arts item=art}
									<tr>
										<td class="w100p"><a href="/article/{$art->fields['urlrewrite']}.html"><img src="{$art->image}" alt="{$art->fields['label']}"></a></td>
										<td><a href="/article/{$art->fields['urlrewrite']}.html">{$art->fields['label']}</a></td>
										<td class="w100p"><a class="left blue-area round5" href="/article/{$art->fields['urlrewrite']}.html"><i class="icon-search"></i></a></td>
										{if $cata_mode_B2C || $smarty.session.dims.connected}
											<td class="w100p"><a href="javascript:void(0);" onclick="javascript:addToCart({$art->fields['id']});" class="left orange-area round5">
												<i class="icon-cart"></i>
											</a></td>
										{/if}
									</tr>
								{/foreach}
							</table>
						{/foreach}
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Retour au default.tpl pour fermeture du div -->

<script type="text/javascript">
	{literal}
	$('#add-to-cart-button').click(function() {
		addToCart({/literal}{$article.id}{literal}, $('#qte').val());
	});
	{/literal}

	{if sizeof($article.linked_articles)}
		{foreach from=$article.linked_articles key=type_link item=linked_arts}
			{foreach from=$linked_arts item=art}
				{literal}
				$('#add-to-cart-button-{/literal}{$art->fields['id']}{literal}').click(function() {
					addToCart({/literal}{$art->fields['id']}{literal}, 1);
				});
				{/literal}
			{/foreach}
		{/foreach}
	{/if}
</script>
