{foreach from=$sitemap_elements key=k item=heading}
    {if isset($heading.label) && isset($heading.is_sitemap) && $heading.is_sitemap==1}
    <ul>
		<li style="margin-left: {$heading.depth*20}px;height:12px;">
			<a href="{$heading.url}">{$heading.label}</a>
		</li>
		
		{if isset($heading.articles) && count($heading.articles)}
            <ul>
            {foreach from=$heading.articles key=a item=article}
                <li style="margin-left: {$heading.depth*20}px;height:12px; margin-top: 10px;">
                    <a href="{$article.url}">{$article.title}</a>
                </li>
            {/foreach}
            </ul>
		{/if} 
    </ul>
    {/if}
{/foreach}
