<link href="{$wceobject.TEMPLATE_OBJECT_PATH}/style_news.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="/js/portal_v5.js"></script>
<div id="actualite">
	<div id="title">
		<!--img src="{$wceobject.TEMPLATE_OBJECT_PATH}/gfx/journal.png" border="0"-->
		<a>{$big_title}</a>
		<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay('filter');" class="link_filter">
			<img src="{$wceobject.TEMPLATE_OBJECT_PATH}/gfx/filtrer.png" border="0" style="float: left; padding-left: 10px; padding-right: 5px; margin: 2px;" align="right"><font style="color: #B70B01;">Filtrer les actualités</font>
		</a>
	</div>
	<div id="filter" {if $isDefaultDate==true && $keywords==''}style="display: none;{/if}">
		<div id="filter_interne">
			<form name="news_filter_form" id="news_filter_form" action="/index.php" method="POST">
				<span>Filtrer les actualités</span><a href="javascript:void(0);" onclick="javascript:dims_switchdisplay('filter');"><img src="{$wceobject.TEMPLATE_OBJECT_PATH}/gfx/fermer_filtre.png" border="0"></a>
				<div id="filtre_bloc1">
					<div id="title_filtre">Rechercher une actualité</div>
					<div id="recherche">
						<input type="text" name="keywords" id="keywords" value="{$keywords}">
					</div>
				</div>
				<div id="filtre_bloc2">
					<div id="title_filtre">Historique des actualités</div>
					<div id="result_historique">
						<table>
							<tr>
								<td style="text-align:right"><label for="news_filter_year">Année : </label></td>
								<td>
									<select name="news_filter_year" id="news_filter_year">
										{foreach from=$years key=k item=annee}
											{if isset($annee.year)}
											<option value="{$k}" {if isset($annee.sel) && $annee.sel==1}selected="selected"{/if}>{$annee.year}</option>
											{/if}
										{/foreach}
									</select>
								</td>
							</tr>
							<tr>
								<td style="text-align:right"><label for="news_filter_year">Mois : </label></td>
								<td>
									<select name="news_filter_month" id="news_filter_month">
										{foreach from=$months key=k item=mois}
											<option value="{$k}" {if isset($mois.sel) && $mois.sel==1}selected="selected"{/if}>{$mois.label}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div id="filtre_button"><input type="submit" value="Filtrer"/></div>
			</form>
		</div>
		<div id="filter_bas"></div>
	</div>

	<div class="pagination">
		{foreach from=$pagination key=k item=page}
			{if $page.url!=''}
				<a href="{$page.url}" title="{$page.title}">{$page.label}</a>
			{else}
				<span>{$page.label}</span>
			{/if}
		{/foreach}
	</div>
	<div class="liste_news">
	{if $object_articles|@count > 0}
		{foreach from=$object_articles key=k item=article}
			<table class="one_breve">
				<tr>
					<td>
						<p><a href="{$article.link}" {$article.target}>{$article.title}</a></p>
					</td>
				</tr>
				<tr>
					<td>
						Le <font style="color: #B70B01;">{$article.day}</font> {$article.month}. <font style="color: #B70B01;">{$article.year}
						{if $article.permalink!=''}
						- <a href="mailto:?subject={$article.mail_subject}&body=Consultez l'article {$article.mail_subject} sur {$article.permalink}"><img src="{$wceobject.TEMPLATE_OBJECT_PATH}/gfx/envoyer_ami.png" border="0" center> Envoyer cet article</font></a>
						{/if}
					</td>
					<!--<td><font style="color: #009EE1;"><a href="mailto:?subject={$article.mail_subject}&body=Consultez l'article {$article.mail_subject} sur {$article.permalink}"><img src="{$wceobject.TEMPLATE_OBJECT_PATH}/gfx/envoyer_ami.png" border="0" center> Envoyer à un ami</font></a></td>-->
				</tr>
			</table>
			<div class="content_actu">
				{if $article.path!=''}
					<div class="content_picture" style="min-width: 50px; float: left; margin-right: 20px;">
						<img src="{$article.path}" alt="{$article.title}" title="{$article.title}" />
					</div>
					<div  class="content_desc">
					   {$article.description}
					   {if $article.link!=''}
							<br><a href="{$article.link}" {$article.target}>{if $article.link_mode=="interne"}Lire la suite ...{else}Consulter la source...{/if}</a>
						{/if}
					</div>
				{else}
					<div  class="content_desc_no_picture">
					   {$article.description}
					   {if $article.link!=''}
							<br><a href="{$article.link}" {$article.target}>{if $article.link_mode=="interne"}Lire la suite ...{else}Consulter la source...{/if}</a>
						{/if}
					</div>
				{/if}
			</div>
			<div class="source" style="margin-bottom: 30px;">
					<font style="color: #BFBFBF;">Source :</font>
					{if $article.source!=''}
						<a class="source_link" href="{if $article.source_link!=''}{$article.source_link}{else}{$article.link}{/if}" {$article.target} title="Consulter l'article sur {$article.source}">{$article.source}</a>
					{else}<span class="source_no_link">URPS</span>{/if}
				</font>
			</div>
		{/foreach}
	{else}
		<div class="no_breve">
				Aucune actualité ne correspond à vos critères de filtrage.
		</div>
	{/if}
	</div>
	<div class="separateur_news"></div>
	<div class="pagination">
		{foreach from=$pagination key=k item=page}
			{if $page.url!=''}
				<a href="{$page.url}" title="{$page.title}">{$page.label}</a>
			{else}
				<span>{$page.label}</span>
			{/if}
		{/foreach}
	</div>
</div>
