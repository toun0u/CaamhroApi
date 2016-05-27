<rss version="2.0">
	<rdf:RDF 
		xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
		xmlns:ev="http://purl.org/rss/1.0/modules/event/"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns="http://purl.org/rss/1.0/"
	> 
		<channel>
			<title>{$rootpath}</title>
			<link>{$rootpath}/index.php?dims_op=planning_rss</link>
			<description><![CDATA[Flux RSS du planning de {$rootpath}]]></description>
			<language>fr-FR</language>
		    <!--<image>
		        <url>{$rootpath}/templates/frontoffice/casesv2/img/logo.png</url>
		        <title>{$rootpath}</title>
		        <link>{$rootpath}</link>
		    </image>-->
			{if $rss_elems|@count > 0}
				{foreach from=$rss_elems key=k item=planning}
					<item>
						<title><![CDATA[{$planning.libelle}]]></title>
						<description><![CDATA[{$planning.description}]]></description>
		                <pubDate>{$planning.date}</pubDate>
						<ev:type>{$planning.type}</ev:type>
						<ev:organizer><![CDATA[{$planning.organizer}]]></ev:organizer>
						<ev:location><![CDATA[{$planning.address} {$planning.cp} {$planning.city}]]></ev:location>
						<ev:startdate>{$planning.datejour} {$planning.heuredeb}</ev:startdate>
						<ev:enddate>{$planning.datefin} {$planning.heurefin}</ev:enddate>
					</item>
				{/foreach}
			{/if}
		</channel>
	</rdf:RDF>
</rss>