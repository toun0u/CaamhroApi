<?php /* Smarty version Smarty-3.1.19, created on 2016-01-05 09:07:02
         compiled from "/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1523962070568b79a6cbef61-94719647%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b19098566906da8e0c4c61b31bfa2db6b3bfa947' => 
    array (
      0 => '/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/index.tpl',
      1 => 1451925995,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1523962070568b79a6cbef61-94719647',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
    'site' => 0,
    'styles' => 0,
    'scripts' => 0,
    'is_homepage' => 0,
    'into_cata' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568b79a6ded3c5_56343230',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568b79a6ded3c5_56343230')) {function content_568b79a6ded3c5_56343230($_smarty_tpl) {?><!DOCTYPE html>
<!--[if IE 9]><html class="ie9"><![endif]-->
<!--[if IE 8]><html class="ie8"><![endif]-->
<!--[if IE 7]><html class="ie7"><![endif]-->
<!--[if gt IE 9]><!--><html><!--<![endif]-->
<head>
	<title><?php if ((isset($_smarty_tpl->tpl_vars['page']->value['TITLE']))) {?> <?php echo $_smarty_tpl->tpl_vars['page']->value['TITLE'];?>
 - <?php }?> <?php if ($_smarty_tpl->tpl_vars['site']->value['SITE_TITLE']!='') {?><?php echo $_smarty_tpl->tpl_vars['site']->value['SITE_TITLE'];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['site']->value['TITLE'];?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['site']->value['DEBUG_MODE'])&&$_smarty_tpl->tpl_vars['site']->value['DEBUG_MODE']) {?> &nbsp;| render: <?php echo $_smarty_tpl->tpl_vars['site']->value['DIMS_EXEC_TIME'];?>
 ms | sql: <?php echo $_smarty_tpl->tpl_vars['site']->value['DIMS_NUMQUERIES'];?>
 q (<?php echo $_smarty_tpl->tpl_vars['site']->value['DIMS_SQL_P100'];?>
 %) <?php }?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=10">
	<meta name="description" content="<?php if (!empty($_smarty_tpl->tpl_vars['page']->value['META_DESCRIPTION'])) {?><?php echo $_smarty_tpl->tpl_vars['page']->value['META_DESCRIPTION'];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['site']->value['WORKSPACE_META_DESCRIPTION'];?>
<?php }?>">
	<meta name="author" content="<?php echo $_smarty_tpl->tpl_vars['site']->value['WORKSPACE_META_AUTHOR'];?>
">
	<meta name="copyright" content="<?php echo $_smarty_tpl->tpl_vars['site']->value['WORKSPACE_META_COPYRIGHT'];?>
">
	<!--meta name="robots" content="<?php echo $_smarty_tpl->tpl_vars['site']->value['WORKSPACE_META_ROBOTS'];?>
"-->
	<META NAME="Publisher" CONTENT="Caahmro">
	<meta name="reply-to" content=""/>
	<meta name="content-language" content="fr-FR"/>
	<meta name="robots" content="index,follow"/>
	<meta name="ICBM" content="47.8556020,1.9599150">
	<meta name="geo.position" content= "47.8556020,1.9599150">
	<meta name="geo.placename" content="Saint-Cyr-en-Val, Loiret, FRANCE">
	<meta name="geo.region" content="FR-45">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo $_smarty_tpl->tpl_vars['site']->value['TEMPLATE_ROOT_PATH'];?>
/favicon.png" />
	<link rel="icon" type="image/png" href="/assets/images/frontoffice/<?php echo $_smarty_tpl->tpl_vars['site']->value['TEMPLATE_NAME'];?>
/icon/logo-dims.png">
	<?php echo $_smarty_tpl->tpl_vars['styles']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['site']->value['ADDITIONAL_CSS'];?>


	<?php echo $_smarty_tpl->tpl_vars['scripts']->value;?>

	<!--script type="text/javascript" src="/assets/javascripts/frontoffice/caahmro/catalogue.js?v=2"></script-->
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
	<div class="outwrap">
		<div class="wrap">
			<?php if ($_smarty_tpl->tpl_vars['is_homepage']->value&&!$_smarty_tpl->tpl_vars['into_cata']->value) {?>
				<?php echo $_smarty_tpl->getSubTemplate ("homepage.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

			<?php } else { ?>
				<?php echo $_smarty_tpl->getSubTemplate ("default.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

			<?php }?>
		</div>
	</div>
	<div id="flashpopup"></div>

	<script type="text/javascript">
		<?php echo $_smarty_tpl->tpl_vars['site']->value['ADDITIONAL_JAVASCRIPT'];?>

		
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
		
	</script>
</body>
</html>
<?php }} ?>
