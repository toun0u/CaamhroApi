<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-FR">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="description" content="{$site.WORKSPACE_META_DESCRIPTION}" />
	<meta name="keywords" content="{$site.WORKSPACE_META_KEYWORDS}" />
	<meta name="author" content="{$site.WORKSPACE_META_AUTHOR}" />
	<meta name="copyright" content="{$site.WORKSPACE_META_COPYRIGHT}" />
	<meta name="robots" content="{$site.WORKSPACE_META_ROBOTS}" />

	<link rel="icon" href="{$site.TEMPLATE_ROOT_PATH}/image/favicon.png" type="image/png" />
	{$styles}
	{$scripts}

	{$site.ADDITIONAL_CSS}
	{literal}
	<script type="text/javascript">
	//<!--
	{/literal}
	{$site.ADDITIONAL_JAVASCRIPT}
	{literal}
	/* Affichage d'un sous menu (niv2) du niveau 1*/
	function afficher_bloc1(position) {
		{/literal}
			{foreach from=$headings.root1.heading1 key=id1 item=heading1}
				// désactivation de tous les blocs
				element = dims_getelem('root1heading2_bloc{$heading1.POSITION}');
				element.style.display = 'none';

				element = dims_getelem('root1heading1_lien{$heading1.POSITION}');
				element.setAttribute('class','root1heading1');
				element.setAttribute('className','root1heading1'); //IE
			{/foreach}
		{literal}
		/* affichage du bloc sélectionné */
		element = dims_getelem('root1heading2_bloc'+position);
		element.style.display = 'block';

		element = dims_getelem('root1heading1_lien'+position);
		element.setAttribute('class','root1heading1selected');
		element.setAttribute('className','root1heading1selected'); //IE
	}

	function openwin(url,w,h,name) {
		var top = (screen.height-(h+60))/2;
		var left = (screen.width-w)/2;

		if(name == '') name = 'dimswin';
		dimswin=window.open(url,name,'top='+top+',left='+left+',width='+w+', height='+h+', status=no, menubar=no, toolbar=no, scrollbars=yes, resizable=yes, screenY=20, screenX=20');
		dimswin.focus();
	}

	{/literal}
	//-->
	</script>

	<title>{$site.SITE_TITLE} - &nbsp;size: {$site.DIMS_PAGE_SIZE} kB | render: {$site.DIMS_EXEC_TIME} ms | sql: {$site.DIMS_NUMQUERIES} q ({$site.DIMS_SQL_P100} %)</title>
</head>
<body>

<div id="outer">
	<div id="upbg"></div>

	<div id="inner">

		<div id="header">
			<h1><span>{$site.TITLE}</h1>
		</div>

		<div id="splash"></div>

		<div id="menu">
			<ul>
				{if isset($headings.root1.heading1)}
				{foreach from=$headings.root1.heading1 key=idh1 item=heading1}
						<li class="rub1{$heading1.SEL}"><a  accesskey="{$heading1.POSITION}" title="{$heading1.LABEL}" href="{$heading1.LINK}">{$heading1.LABEL}</a></li>
				{/foreach}
				{/if}
			</ul>
		</div>

		<div id="primarycontent">
			<div id="pagecontent">

					{$page.CONTENT}

			</div>
			<!-- primary content end -->
		</div>

		<div id="secondarycontent">
			<div class="content">
				<ul class="linklist">
					{if isset($headings.root1.heading1)}
						{foreach from=$headings.root1.heading1 key=idh1 item=heading1}
							{if isset($heading1.heading2) }
								{foreach from=$heading1.heading2 key=idh2 item=heading2}
									<li class="heading2{$heading2.SEL}"><a href="{$heading2.LINK}">{$heading2.LABEL}</a></li>
								{/foreach}
							{/if}
						{/foreach}
					{/if}
				</ul>
			</div>
			<!-- secondary content end -->
		</div>

		<div id="footer">
			Design by <a href="http://www.nodethirtythree.com/" target="_blank">NodeThirtyThree</a>.
		</div>
	</div>
</div>

</body>
</html>
