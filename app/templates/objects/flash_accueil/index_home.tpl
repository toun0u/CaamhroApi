<link href="{$styles_path}" rel="stylesheet" type="text/css" media="screen" />
{if $news|@count > 0}
<div class="bloc_header_bloc_left" style="margin-top: 20px;">
	<!--h3 class="title_news" >{$title}</h3-->
</div>
<div class="bloc_menu_left_content">
	{foreach from=$news key=k item=actu}
		<div class="actu" style="font-size: 0.9em !important;">
			<div class="actu_title"><a href="{$actu.link}">{$actu.title}</a></div>
			<div class="actu_details">
				{if $actu.picto != ''}
					<div class="actu_picto">
						<img src="{$actu.picto}" alt="{$actu.title|replace:'"':'\"'}" title="{$actu.title|replace:'"':'\"'}" />
					</div>
				{/if}
				<div class="actu_description {if $actu.picto == ''}actu_full_place{/if}">
					{$actu.description}
				</div>
			</div>
		</div>
	{/foreach}
	{if $link_available}
		<div class="footer_actus">
			<a href="/news.html" alt="Voir toutes les actues">Voir toutes les actualités</a>
		</div>
	{/if}
</div>
{/if}
