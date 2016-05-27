<?php /* Smarty version Smarty-3.1.19, created on 2016-01-05 09:07:02
         compiled from "/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/_desktop_menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1371954476568b79a6eff881-21667959%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e4c2018e04874fcce9b3b11621606f1b121afe1' => 
    array (
      0 => '/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/_desktop_menu.tpl',
      1 => 1451925995,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1371954476568b79a6eff881-21667959',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'switch_user_logged_out' => 0,
    'panier' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568b79a7043e17_80449228',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568b79a7043e17_80449228')) {function content_568b79a7043e17_80449228($_smarty_tpl) {?><div class="action_nav phone-hidden">
	<div class="bloc_action">
		<a href="/accueil.html">
			<i class="icon2-home title-icon"></i>
			<span>
				<?php echo $_SESSION['cste']['CATA_HOME'];?>

			</span>
		</a>
		<?php if ((isset($_smarty_tpl->tpl_vars['switch_user_logged_out']->value))) {?>
			<a href="/index.php?op=connexion" class="phone-hidden">
				<i class="icon2-enter title-icon"></i>
				<span>
					<?php echo $_SESSION['cste']['CATA_CONNECTION'];?>

				</span>
			</a>
		<?php } else { ?>
			<a href="/index.php?op=compte" class="phone-hidden">
				<i class="icon2-user3 title-icon"></i>
				<span>
					<?php echo $_SESSION['cste']['_PERSONAL_SPACE'];?>

				</span>
			</a>
			<a title="Me déconnecter" href="/index.php?dims_logout=1">
				<i class="icon2-exit title-icon"></i>
				<?php echo $_SESSION['cste']['_SIGN_OUT'];?>

			</a>
			<hr class="bgwhite">
			<a href="/index.php?op=panier"  class="phone-hidden">
				<i class="icon-cart"></i>
				<span id="nbArtPanier">
					<?php if (isset($_smarty_tpl->tpl_vars['panier']->value)) {?>
						<?php if ($_smarty_tpl->tpl_vars['panier']->value['nb_art']==0) {?>
							<?php echo $_SESSION['cste']['CATA_YOUR_CART'];?>
 (<?php echo $_SESSION['cste']['_EMPTY'];?>
)
						<?php } else { ?>
							<?php echo $_smarty_tpl->tpl_vars['panier']->value['nb_art'];?>

							<?php if ($_smarty_tpl->tpl_vars['panier']->value['nb_art']>1) {?>
								<?php echo mb_strtolower($_SESSION['cste']['ARTICLES'], 'UTF-8');?>

							<?php } else { ?>
								<?php echo mb_strtolower($_SESSION['cste']['_ARTICLE'], 'UTF-8');?>

							<?php }?>
						<?php }?>
					<?php }?>
				</span>
			</a>
		<?php }?>
	</div>
</div><?php }} ?>
