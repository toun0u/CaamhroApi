<link href="{$styles_path}" rel="stylesheet" type="text/css" media="screen" />
{if $alerts|@count > 0}
<div class="bloc_header_bloc_right">
	<h3>ALERTES</h3>
</div>
<div class="bloc_menu_right_content">
	{foreach from=$alerts key=k item=alert}
		<div class="alert">
			<p class="alert_date">{$alert.timestp_published|substr:6:2}. {$alert.timestp_published|substr:4:2}. {$alert.timestp_published|substr:0:4}</p>
			<div class="alert_title">
                <span class="level_alert {$alert.level_class}" title="{$alert.level_title}"></span>
                <a href="{$alert.link}">{$alert.title}</a>
            </div>
			<div class="alert_details">
				{if $alert.picto != ''}
					<div class="alert_picto">
						<img src="{$alert.picto}" alt="{$alert.title}" title="{$alert.title}" />
					</div>
				{/if}
				<div class="alert_description {if $alert.picto == ''}alert_full_place{/if}">
					{$alert.description}
				</div>
			</div>
		</div>
	{/foreach}
	{if $link_available}
		<div class="footer_alerts">
			<a href="/alertes.html" alt="Voir toutes les alertes">Voir toutes les alertes</a>
		</div>
	{/if}
</div>
{/if}