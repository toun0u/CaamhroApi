<div class="content-zone">
	{include file="_mobile_menu.tpl"}
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
								<a target="_blank" title="Facebook" href="https://www.facebook.com/sharer.php?u={$smarty.server.SCRIPT_URI}&t={$article.label}" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=700');return false;"><i class="icon-facebook biggest facebook"></i></a>
								<a target="_blank" title="Twitter" href="https://twitter.com/share?url={$smarty.server.SCRIPT_URI}&text={$article.label}&via={$smarty.session.dims.currentworkspace.twitter}" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=700');return false;"><i class="icon-twitter biggest twitter"></i></a>
								<a target="_blank" title="Google +" href="https://plus.google.com/share?url={$smarty.server.SCRIPT_URI}&hl=fr" rel="nofollow" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=450,width=650');return false;"><i class="icon-googleplus biggest gplus"></i></a>
							</div>
						</div>
					</div>
					{if $cata_mode_B2C || $smarty.session.dims.connected}
						<div class="mod">
							<div class="secondary-zone pa2 txtcenter rounded-pic content-zone">
								<h2{if isset($article.prix_net)} class="prix-net-color"{/if}>{$article.prix} &euro; HT</h2>
								{if $article.taxe_certiphyto > 0}
									Dont taxe phytosanitaire : {$article.taxe_certiphyto} € HT
								{/if}
								{if isset($article.degressifs)}
									{foreach from=$article.degressifs key=qte_degr item=pu_degr}
										{$pu_degr} € HT à partir de {$qte_degr} pièces<br>
									{/foreach}
								{/if}

								<label for="" class="mt2">Quantité :</label>
								<input id="qte" name="qte" type="number" min="0" class="w80p mt2 txtright" value="1">
								<br>
								<input id="add-to-cart-button" type="button" class="btn btn-primary mt2" value="Ajouter au panier">
							</div>
						</div>
					{/if}
				</div>

				<div class="grid2 mt2">
					{if $article.description != ''}
						<div class="mod">
							<h4 class="mb2">Caractéristiques techniques :</h4>

							<div class="flash info mt2">
								{$article.description}
							</div>
						</div>
					{/if}

					{if !empty($article.references)}
						<div class="mod" style="margin-bottom: 20px;">
							<h4 class="mb2">Informations complémentaires :</h4>

							<ul class="references" style=" list-style-type: none;margin-top:0px;">
								{foreach $article.references as $reference}
									<li style="width:90%" class="pb1">
										{if $reference.pdf}
											<i class="icon2-file-pdf red"></i>
											<a style="text-decoration: underline;font-weight: bold;" href="/common/js/pdf.js/web/viewer.html?file={$reference.link}" target="_blank">{$reference.label}
											</a>
										{elseif $reference.video}
											<i class="icon2-youtube red"></i><a style="text-decoration: underline;font-weight: bold;" class="gallery video" href="{$reference.link}" target="_blank">{$reference.label}
											</a>
										{elseif $reference.image}
											<i class="icon2-image red"></i><a style="text-decoration: underline;font-weight: bold;" class="gallery image" href="{$reference.link}" target="_blank">{$reference.label}
											</a>
										{else}
											<i class="icon2-link red"></i><a style="text-decoration: underline;font-weight: bold;" href="{$reference.link}" target="_blank">{$reference.label}
											</a>
										{/if}
									</li>
								{/foreach}
							</ul>
						</div>
					{/if}
				</div>

				{if isset($debug_text)}
					<div class="grid2 line pt2">
						<div class="mod">
							<h4 class="mb2">Infos de debug :</h4>

							<div class="flash info mt2">
								{$debug_text}
							</div>
						</div>
					</div>
				{/if}

				<div class="line pt2" style="margin-top:10px;">
					{if sizeof($article.linked_articles)}
						{foreach from=$article.linked_articles key=type_link item=linked_arts}
							{if sizeof($linked_arts)}
								<h4 class="mb1">{$a_link_types[$type_link].label} :</h4>

								<table class="alternate-light-inv">
									{foreach from=$linked_arts item=art}
										<tr>
											<td class="w100p"><a href="/article/{$art->fields['urlrewrite']}.html"><img src="{$art->image}" alt="{$art->fields['label']}"></a></td>
											<td><a href="/article/{$art->fields['urlrewrite']}.html">{$art->fields['label']}</a></td>
											<td class="w100p"><a class="left blue-area round5 nounderline" href="/article/{$art->fields['urlrewrite']}.html"><i class="icon-search"></i></a></td>
											{if $cata_mode_B2C || $smarty.session.dims.connected}
												<td class="w100p"><a href="javascript:void(0);" onclick="javascript:addToCart({$art->fields['id']});" class="cart-add left orange-area round5 nounderline">
													<i class="icon-cart"></i>
												</a></td>
											{/if}
										</tr>
									{/foreach}
								</table>
							{/if}
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

	{literal}
		$('a.gallery.image').colorbox({
			rel: 'gallery',
			width: "75%",
			height: "75%",
		});
		$('a.gallery.video').colorbox({
			rel: 'gallery',
			iframe: true,
			width: "75%",
			height: "75%",
		});
	{/literal}
</script>
