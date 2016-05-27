<?php /* Smarty version Smarty-3.1.19, created on 2016-01-05 09:07:02
         compiled from "/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/homepage.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1093691624568b79a6df3820-76161365%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b56d00e567ec137ba3b0286be7ecfeb5e2cdcf50' => 
    array (
      0 => '/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/homepage.tpl',
      1 => 1451925995,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1093691624568b79a6df3820-76161365',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'show_info_certiphyto' => 0,
    'page' => 0,
    'headings' => 0,
    'menuprincipal' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568b79a6e63d99_45697709',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568b79a6e63d99_45697709')) {function content_568b79a6e63d99_45697709($_smarty_tpl) {?><div class="main">
	<header class="heading">
		<?php echo $_smarty_tpl->getSubTemplate ("_header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

	</header>

	<?php echo $_smarty_tpl->getSubTemplate ("_desktop_menu.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>


	<?php if (isset($_smarty_tpl->tpl_vars['show_info_certiphyto']->value)&&$_smarty_tpl->tpl_vars['show_info_certiphyto']->value) {?>
		<?php echo $_smarty_tpl->getSubTemplate ("_info_certiphyto.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

	<?php }?>

	<?php echo $_smarty_tpl->tpl_vars['page']->value['CONTENT'];?>

	<?php echo $_smarty_tpl->getSubTemplate ("right.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

	<div class="mw1280p m-auto">
		<footer class="txtcenter content-zone">
			<a href="#" class="right scrollup"><i class="icon2-arrow-up"></i></a>
			<div class="menu_footer">
				<?php if (isset($_smarty_tpl->tpl_vars['headings']->value['root2']['heading1'])) {?>
					<?php  $_smarty_tpl->tpl_vars['menuprincipal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['menuprincipal']->_loop = false;
 $_smarty_tpl->tpl_vars['idh1'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['headings']->value['root2']['heading1']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['menuprincipal']->key => $_smarty_tpl->tpl_vars['menuprincipal']->value) {
$_smarty_tpl->tpl_vars['menuprincipal']->_loop = true;
 $_smarty_tpl->tpl_vars['idh1']->value = $_smarty_tpl->tpl_vars['menuprincipal']->key;
?>
						<li class="border-left" id="home<?php echo $_smarty_tpl->tpl_vars['menuprincipal']->value['POSITION'];?>
">
							<?php if ($_smarty_tpl->tpl_vars['menuprincipal']->value['SEL']=="selected") {?>
								<a class="selected" title="<?php echo $_smarty_tpl->tpl_vars['menuprincipal']->value['LABEL'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['menuprincipal']->value['LINK'];?>
"><?php echo $_smarty_tpl->tpl_vars['menuprincipal']->value['LABEL'];?>
</a>
							<?php } else { ?>
								<a title="<?php echo $_smarty_tpl->tpl_vars['menuprincipal']->value['LABEL'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['menuprincipal']->value['LINK'];?>
"><?php echo $_smarty_tpl->tpl_vars['menuprincipal']->value['LABEL'];?>
</a>
							<?php }?>
						</li>
					<?php } ?>
				<?php }?>
			</div>
			<div class="pa2 right">
				&copy; 2015 CAAHMRO.fr - Tous Droits Réservés - Powered by Dims
			</div>
		</footer>
	</div><!-- !wrap1280p/m-auto -->
</div>
<?php }} ?>
