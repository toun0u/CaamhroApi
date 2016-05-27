{if (isset($global_filter_label))}
	<div class="mod content-zone-green">
		<article class="mod txtcenter mod-separator">
			<div class="pa1">
				<div id="global_filter_info" class="mw1280p m-auto txtwhite">
					Vous êtes actuellement dans l'espace "{$global_filter_label}"<br/>
					<a class="btn btn-primary" href="{$returnURI}">Retourner au site complet</a>
				</div>
			</div>
		</article>
	</div>
{/if}

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

	<div class="pa1 noptop">
		<!-- articles -->
		{if isset($a_filters)}
			{assign var=nb_cols value=4}

			<div class="grid1-3 line mb2 pl2 pr2">
				<div class="mod w20">
					<a href="javascript:void(0);" class="large-hidden tablet-hidden" id="switch">Afficher les filtres</a>
					<div id="filters" class="simple-phone-hidden">
						<div class="Filtre">
							<form name="f_filters" action="" method="get">
								<h3 class="cata_title orange">Filtres</h3>
								{foreach from=$a_filters item=filter}
									<h6 class="cata_title nomtop">{$filter.filter.libelle}</h6>
									<ul class="mb2">
										{assign var="compteur" value=0}
										{foreach from=$filter.values key=id item=fields}
											{assign var="compteur" value="`$compteur+1`"}
											<li class="filterOption{$filter.filter.id}" {if $compteur >= 15 && !$fields.selected} style="display: none;"{/if}>
												<input class="filter" type="checkbox" id="filter{$filter.filter.id}_{$id}" name="filter{$filter.filter.id}[]" value="{$id}" {if $fields.selected == $id}checked="checked"{/if} {if $fields.disabled == 1}disabled="disabled"{/if}>
												<label for="filter{$filter.filter.id}_{$id}" {if $fields.disabled == 1}style="color:#ccc;"{/if}>{$fields.label}</label>
											</li>
										{/foreach}
									</ul>
									{if $compteur >= 15}
										<a class="mb2" id="showMore{$filter.filter.id}" class="orange" href="javascript:void(0);" onclick="javascript:showMoreFilters({$filter.filter.id});">En voir plus...</a>
									{/if}
								{/foreach}
							</form>
								<hr class="mb2 mt1">
						</div>
					</div>
		{else}
			{assign var=nb_cols value=5}
			<div class="line mb2 pl2 pr2">
		{/if}
			</div>

			<div class="mod">
				<p class="tot_articles"><strong class="orange">{$nb_total_articles}</strong> articles correspondent à vos critères</p>

				<div class="cata">
					{if isset($catalogue.articles) && sizeof($catalogue.articles) > 0}
						{foreach from=$catalogue.articles item=article name=it}

							{if $smarty.foreach.it.index % $nb_cols == 0}
								<div class="grid{$nb_cols} line mb2 pl2 pr2">
							{/if}
								<div class="mod clearfix rounded-pic secondary-zone light-shadow mt1">
									<a href="/article/{$article.urlrewrite}.html" title="{$article.label}">
										<img src="{$article.image}" alt="{$article.label}" style="height: 100px; display: block; margin: 0 auto;">
									</a>

									<div class="pa1 line" style="min-height: 80px;">
										<div class="mod wordbnormal small">
											{$article.label}
										</div>
									</div>
									<div class="bigger">
										<a href="/article/{$article.urlrewrite}.html" title="{$article.label}" class="left inbl blue-area w20 txtcenter">
											<i class="icon-search"></i>
										</a>
										{if $cata_mode_B2C || $smarty.session.dims.connected}
											<div class="left inbl w60 txtcenter">
												{$article.prix} &euro; HT
											</div>
										{/if}
									</div>
								</div>

							{if not $smarty.foreach.it.first and ($smarty.foreach.it.index + 1) % $nb_cols == 0}
								</div>
							{/if}
						{/foreach}
					{else}
						<div class="txtcenter ma1">
							Il n'y a aucun article.
						</div>
					{/if}
					<!-- end articles -->

					{if !empty($pagination_liens)}
						<div id="liens_pagination">
							<span>Page :</span>
							{foreach from=$pagination_liens key=pageNum item=page}
								{if $page.link != ''}
									<a href="{$page.link}">{$page.label}</a>
								{elseif $page.current}
									<span class="current">{$page.label}</span>
								{else}
									{$page.label}
								{/if}
							{/foreach}
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Retour au default.tpl pour fermeture du div -->

<script type="text/javascript">
	$(document).ready(function() {
		$('.filter').change(function() {
			document.f_filters.submit();
		});

		$('#switch').click(function(){
			if($('#filters').is(':visible')){
				$('#filters').hide();
				$(this).text('Afficher les filtres');
			}
			else{
				$('#filters').show();
				$(this).text('Masquer les filtres');
			}
		});
	});
</script>
