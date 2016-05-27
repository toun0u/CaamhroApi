<rss version="2.0">
	<channel>
		<title>{$rootpath}</title>
		<link>{$rootpath}/index.php?dims_op=alerts_rss</link>
		<description></description>
		<language>fr-FR</language>
        <image>
            <url>{$rootpath}/templates/frontoffice/casesv2/img/logo.png</url>
            <title>{$rootpath}</title>
            <link>{$rootpath}</link>
        </image>
		{if $rss_elems|@count > 0}
			{foreach from=$rss_elems key=k item=alert}
				<item>
					<title>{$alert.title}</title>
					<link>{$alert.link}</link>
					<description><![CDATA[{$alert.description}]]></description>
                    <pubDate>{$alert.date}</pubDate>
					<guid isPermaLink="true">{$alert.link}</guid>
                    {if $alert.picto_length > 0}
                        <enclosure url="{$alert.picto}" length="{$alert.picto_length}" type="image/*" />
                    {/if}
				</item>
			{/foreach}
		{/if}
	</channel>
</rss>