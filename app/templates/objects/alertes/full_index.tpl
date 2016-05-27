<div id="full_index">
	<link href="{$styles_path}" rel="stylesheet" type="text/css" media="screen" />
	{if $alerts|@count > 0}
	<h1>Toutes les alertes</h1>
	<br/>
	{foreach from=$alerts key=k item=alert}
		<a name="alert_{$alert.id}"></a>
		<div class="alert">
			<div class="alert_title"><span class="level_alert {$alert.level_class}" title="{$alert.level_title}"></span>{if $alert.link != ''}<a href="{$alert.link}">{$alert.title}</a>{else} {$alert.title} {/if} - <span class="alert_date">{$alert.timestp_published|substr:6:2}. {$alert.timestp_published|substr:4:2}. {$alert.timestp_published|substr:0:4}</span></div>
			<div class="alert_details">
				{if $alert.picto != ''}
					<div class="alert_picto">
						<img src="{$alert.picto}" alt="{$alert.title|replace:'"':'\"'}" title="{$alert.title|replace:'"':'\"'}" />
					</div>
				{/if}
				<div class="alert_description">
					{$alert.description}
				</div>
			</div>
			{if $alert.link != ''}
				<div class="link_alert">
					<a href="{$alert.link}" title="{$alert.title|replace:'"':'\"'}">Voir l'article...</a>
				</div>
			{/if}
		</div>
	{/foreach}
	{/if}
</div>