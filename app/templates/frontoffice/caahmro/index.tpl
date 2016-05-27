<!DOCTYPE html>
<!--[if IE 9]><html class="ie9"><![endif]-->
<!--[if IE 8]><html class="ie8"><![endif]-->
<!--[if IE 7]><html class="ie7"><![endif]-->
<!--[if gt IE 9]><!--><html><!--<![endif]-->
<head>
	<title>{if (isset($page.TITLE)) } {$page.TITLE} - {/if} {if $site.SITE_TITLE != ''}{$site.SITE_TITLE}{else}{$site.TITLE}{/if}{if isset($site.DEBUG_MODE) && $site.DEBUG_MODE} &nbsp;| render: {$site.DIMS_EXEC_TIME} ms | sql: {$site.DIMS_NUMQUERIES} q ({$site.DIMS_SQL_P100} %) {/if}</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=10">
	<meta name="description" content="{if !empty($page.META_DESCRIPTION)}{$page.META_DESCRIPTION}{else}{$site.WORKSPACE_META_DESCRIPTION}{/if}">
	<meta name="author" content="{$site.WORKSPACE_META_AUTHOR}">
	<meta name="copyright" content="{$site.WORKSPACE_META_COPYRIGHT}">
	<!--meta name="robots" content="{$site.WORKSPACE_META_ROBOTS}"-->
	<META NAME="Publisher" CONTENT="Caahmro">
	<meta name="reply-to" content=""/>
	<meta name="content-language" content="fr-FR"/>
	<meta name="robots" content="index,follow"/>
	<meta name="ICBM" content="47.8556020,1.9599150">
	<meta name="geo.position" content= "47.8556020,1.9599150">
	<meta name="geo.placename" content="Saint-Cyr-en-Val, Loiret, FRANCE">
	<meta name="geo.region" content="FR-45">
	<link rel="shortcut icon" type="image/x-icon" href="{$site.TEMPLATE_ROOT_PATH}/favicon.png" />
	<link rel="icon" type="image/png" href="/assets/images/frontoffice/{$site.TEMPLATE_NAME}/icon/logo-dims.png">
	{$styles}
	{$site.ADDITIONAL_CSS}

	{$scripts}
	<!--script type="text/javascript" src="/assets/javascripts/frontoffice/caahmro/catalogue.js?v=2"></script-->
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
	<div class="outwrap">
		<div class="wrap">
			{if $is_homepage && !$into_cata}
				{include file="homepage.tpl"}
			{else}
				{include file="default.tpl"}
			{/if}
		</div>
	</div>
	<div id="flashpopup"></div>

	<script type="text/javascript">
		{$site.ADDITIONAL_JAVASCRIPT}
		{literal}
			$('#layerslider').layerSlider({
				autoStart           : true,
				navStartStop        : false,
				pauseOnHover        : true
			});
			$('document').ready(function(){
				$('#add-to-cart-button, .cart-add').click(function() { flashPopup('L\'article a été ajouté au panier.') });

				$('img').bind('contextmenu', function() {
					return false;
				});
				$('.toggle').click(function(e){
					var obj = $('.wrap').toggleClass('open');
					$('nav').toggleClass('open');
					e.preventDefault();
				});

				$('.has-subnav > a').before('<div class="toggle-link">+</div>');
				$('.third-level > a').before('<div class="subtoggle-link">+</div>');

				$('.toggle-link').click(function(e) {

					if($(this).hasClass('active')) {
						$(this).removeClass('active');
						var obj = $(this).next().next().slideUp('fast');

						obj.promise()
							.done(function() {
								$(this).removeClass('active');
							})
					}
					else {
						$('.toggle-link').each(function(i) {
							$(this).removeClass('active');
							var obj = $(this).next().next().slideUp();

							obj.promise()
								.done(function() {
									$(this).removeClass('active');
								})
						});

						$(this).addClass('active');
						var obj = $(this).siblings('ul').slideDown('fast');

						obj.promise()
							.done(function() {
								$(this).addClass('active').siblings('ul').addClass('active');
							})
					}

					e.preventDefault();
				});

				$('.subtoggle-link').click(function(e) {
					if($(this).hasClass('active')) {
						$(this).removeClass('active');
						var obj = $(this).next().next().slideUp('fast');

						obj.promise()
							.done(function() {
								$(this).removeClass('active');
							})
					}
					else {
						$('.subtoggle-link').each(function(i) {
							$(this).removeClass('active');
							var obj = $(this).next().next().slideUp();

							obj.promise()
								.done(function() {
									$(this).removeClass('active');
								})
						});

						$(this).addClass('active');
						var obj = $(this).siblings('ul').slideDown('fast');

						obj.promise()
							.done(function() {
								$(this).addClass('active').siblings('ul').addClass('active');
							})
					}

					e.preventDefault();
				});
			});

			// google analytics
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-62253901-1', 'auto');
			ga('send', 'pageview');
		{/literal}
	</script>
</body>
</html>
