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
	<meta name="keywords" content="{$site.WORKSPACE_META_KEYWORDS},{$page.META_KEYWORDS}">
	<meta name="author" content="{$site.WORKSPACE_META_AUTHOR}">
	<meta name="copyright" content="{$site.WORKSPACE_META_COPYRIGHT}">
	<!--meta name="robots" content="{$site.WORKSPACE_META_ROBOTS}"-->
	<META NAME="Publisher" CONTENT="Caahmro">
	<meta name="reply-to" content=""/>
	<meta name="content-language" content="fr-FR"/>
	<meta name="robots" content="noindex,nofollow"/>
	<meta name="ICBM" content="47.8556020,1.9599150">
	<meta name="geo.position" content= "47.8556020,1.9599150">
	<meta name="geo.placename" content="Saint-Cyr-en-Val, Loiret, FRANCE">
	<meta name="geo.region" content="FR-45">
	<link rel="shortcut icon" type="image/x-icon" href="{$site.TEMPLATE_ROOT_PATH}/favicon.png" />
	<link rel="icon" type="image/png" href="/assets/images/frontoffice/{$site.TEMPLATE_NAME}/icon/logo-dims.png">
	{$styles}
	{$site.ADDITIONAL_CSS}

	{$scripts}
	<script type="text/javascript" src="/assets/javascripts/frontoffice/caahmro/catalogue.js?v=2"></script>
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

				// Menu
				$('nav .has-subnav > a').click(function(e) {
					$('nav .has-subnav > ul').hide();

					if ($(this).parent().hasClass('has-subnav-active')) {
						$('nav .has-subnav-active > ul').hide();
						$(this).parent().removeClass('has-subnav-active');
					}
					else {
						$('nav .has-subnav-active').removeClass('has-subnav-active');
						$(this).parent().addClass('has-subnav-active');
						$('nav .has-subnav-active > ul').show();
					}

					e.preventDefault();
				});
				// Sous-menu
				$('nav .third-level > a').click(function(e) {
					$('nav .third-level > ul').hide();

					if ($(this).parent().hasClass('third-level-active')) {
						$('nav .third-level-active > ul').hide();
						$(this).parent().removeClass('third-level-active');
					}
					else {
						$('nav .third-level-active').removeClass('third-level-active');
						$(this).parent().addClass('third-level-active');
						$('nav .third-level-active > ul').show();
					}

					e.preventDefault();
				});

			});
		{/literal}
	</script>
	<!--script src="/common/js/bootstrap.min.js"></script-->
</body>
</html>
