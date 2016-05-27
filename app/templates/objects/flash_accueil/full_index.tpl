<div class="wiki_articlecontent">
	<div id="full_index">
	<link href="{$styles_path}" rel="stylesheet" type="text/css" media="screen" />
	{if $news|@count > 0}
		{foreach from=$news key=k item=actu}
            <div>
                <a name="actu_{$actu.id}"></a>
                <div class="actu" style="font-size: 1.2m !important;">
                    <div class="actu_title">{if $actu.link != ''}<a href="{$actu.link}">{$actu.title}</a>{else} {$actu.title} {/if}{if !empty ($actu.timestp_published)} - <font style="color:#8CB410"><span class="actu_date">{$actu.timestp_published|substr:6:2}. {$actu.timestp_published|substr:4:2}. {$actu.timestp_published|substr:0:4}</span></font>{/if}</div>
                    <div class="actu_details" style="overflow:hidden">
                        {if $actu.picto != ''}
                            <div class="actu_picto" style="float:left;padding-right:10px">
                                <img src="{$actu.picto}" alt="{$actu.title}" title="{$actu.title}" />
                            </div>
                        {/if}
                        <div class="actu_description">
                            <p>{$actu.description}</p>
                        </div>
                    </div>
                    {if $actu.link != ''}
                        <div class="link_actu" style="overflow:hidden">
                            <a href="{$actu.link}" title="{$actu.title}">Voir l'article...</a>
                        </div>
                    {/if}
                </div>
            </div>
		{/foreach}
    {/if}
	</div>
</div>
