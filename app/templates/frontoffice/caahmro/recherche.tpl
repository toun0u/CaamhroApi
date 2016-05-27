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
	<div class="pa1 noptop">
		<div id="catalogue_content" class="phone-hidden">
			<div id="title nopbottom" {if !$catalogue.display.aff_cata}style="border-bottom: 1px solid {$colorfamily};"{/if}>
				<div class="h1_catatitle" style="float:left;">
					<h1 class="cata_title" style="color:#FF6200;">Recherche sur '{$motscles}' - L'index</h1>
				</div>
				<!--{if !$catalogue.display.aff_cata}
					<div class="switch_cata"><img id="transparence_eyes5_ie6" src="/assets/images/frontoffice/{$site.TEMPLATE_NAME}/design/the_eye_mini.png" style="vertical-align: bottom; margin-right:5px;"/><a href="/index.php?op=catalogue&aff_cata=1" style="color:#FF6200;">Afficher le catalogue</a></div>
				{else}
					<div class="switch_cata"><img id="transparence_eyes6_ie6" src="/assets/images/frontoffice/{$site.TEMPLATE_NAME}/design/the_closed_eye_mini.png" style="vertical-align: bottom; margin-right:5px;"/><a href="/index.php?op=catalogue&aff_cata=0" style="color:#FF6200;">Masquer le catalogue</a></div>
				{/if}-->
				<div style="clear:both;height:0px;">&nbsp;</div>
			</div>
			{if $catalogue.display.aff_cata}
			<div class="container" style='border-color:#3190CF'>
				<div id="famille1" class="famille-finder" style='border-right-color:#3190CF'>
					{foreach from=$catalogue.fam1 key=idh1 item=famille1}
						<a {if $famille1.sel=="selected"} class="selected" style='background-color:#3190CF'{elseif $famille1.in_path=="selected"} class="in_path" style='background-color:#3190CF'{/if} href="{$famille1.lien}">{$famille1.label}</a>
					{/foreach}
				</div>
				{if count($catalogue.fam2) > 0}
					<div id="famille2" class="famille-finder" style='border-right-color:#3190CF'>
						{foreach from=$catalogue.fam2 key=idh1 item=famille2}
							<a {if $famille2.sel=="selected"} class="selected" style='background-color:#64AEE0'{elseif $famille2.in_path=="selected"} class="in_path" style='background-color:#3190CF' {/if} href="{$famille2.lien}">{$famille2.label}</a>
						{/foreach}
					</div>
					{if count($catalogue.fam3) > 0}
						<div id="famille3" class="famille-finder">
							{foreach from=$catalogue.fam3 key=idh1 item=famille3}
								<a {if $famille3.sel=="selected"} class="selected" style='background-color:#3190CF'{elseif $famille3.in_path=="selected"} class="in_path" style='background-color:#3190CF' {/if} href="{$famille3.lien}">{$famille3.label}</a>
							{/foreach}
						</div>
						{if isset($catalogue.fam4) && count($catalogue.fam4) > 0}
							<div id="famille4" class="famille-finder">
								{foreach from=$catalogue.fam4 key=idh1 item=famille4}
									<a {if $famille4.sel=="selected"} class="selected" style='background-color:#3190CF'{elseif $famille4.in_path=="selected"} class="in_path" {/if} href="#3190CF">{$famille4.label}</a>
								{/foreach}
							</div>
						{/if}
					{else}
						{if isset($sliders) && count($sliders)}
						<div id="sans-famille2">
							{if (isset($switch_user_logged_in))}
								<p class="titre_cata_promo">Nos offres du moment :</p>
							{else}
								<p class="titre_cata_promo">Zoom sur :</p>
							{/if}
							<div id="articles_promo" class="">
								<div id="slideshow">
									<div id="slidesContainer">
										{if isset($catalogue.sliders) && $catalogue.sliders}
											{foreach from=$catalogue.sliders key=idh1 item=article name=arti}
												<div class="slide">
													{include file='slide_petit.tpl' article=$article}
												</div>
											{/foreach}
										{/if}
									</div>
								</div>
							</div>
						</div>
						{/if}
					{/if}
				{else}
					{if isset($current_description)}
						<div id="sans-famille">
							{$current_description}
						</div>
					{else}
						{if isset($sliders) && count($sliders)}
						<div id="sans-famille">
							{if (isset($switch_user_logged_in))}
								<p class="titre_cata_promo">Nos offres du moment :</p>
							{else}
								<p class="titre_cata_promo">Zoom sur :</p>
							{/if}
							<div id="articles_promo">
								<div id="slideshow">
									<div id="slidesContainer">
										{if isset($catalogue.sliders) && $catalogue.sliders}
											{foreach from=$catalogue.sliders key=idh1 item=article name=arti}
												<div class="slide">
													{include file='slide_grand.tpl' article=$article}
												</div>
											{/foreach}
										{/if}
									</div>
								</div>
							</div>
						</div>
						{/if}
					{/if}
				{/if}
			</div>
			{/if}
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
								<input type="hidden" name="op" value="recherche" />
								{if isset($smarty.get.motscles)}
									<input type="hidden" name="motscles" value="{$smarty.get.motscles}" />
								{/if}
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
											<div class="left inbl w60 txtcenter{if isset($article.prix_net)} prix-net-color{/if}">
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
