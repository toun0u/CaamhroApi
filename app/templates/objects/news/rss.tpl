<rss version="2.0">
	<channel>
		<title>{$rootpath}</title>
		<link>{$rootpath}/index.php?dims_op=news_rss</link>
		<description></description>
		<language>fr-FR</language>
        <!--<image>
            <url>{$rootpath}/templates/frontoffice/casesv2/img/logo.png</url>
            <title>{$rootpath}</title>
            <link>{$rootpath}</link>
        </image>-->
		{if $rss_elems|@count > 0}
			{foreach from=$rss_elems key=k item=rss}
				<item>
					<title><![CDATA[{$rss.title}]]></title>
					<link><![CDATA[{$rss.link}]]></link>
					<description><![CDATA[{$rss.description}]]></description>
                    <pubDate>{$rss.date}</pubDate>
					<guid isPermaLink="true"><![CDATA[{$rss.link}]]></guid>
                    {if $rss.picto_length > 0}
                        <enclosure url="{$alert.picto}" length="{$alert.picto_length}" type="image/*" />
                    {/if}
				</item>
			{/foreach}
		{/if}
	</channel>
</rss>
