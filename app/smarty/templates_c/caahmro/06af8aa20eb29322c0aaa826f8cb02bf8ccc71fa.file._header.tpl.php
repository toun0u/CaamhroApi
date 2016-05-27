<?php /* Smarty version Smarty-3.1.19, created on 2016-01-05 09:07:02
         compiled from "/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/_header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:264058689568b79a6e69b41-36780860%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '06af8aa20eb29322c0aaa826f8cb02bf8ccc71fa' => 
    array (
      0 => '/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/_header.tpl',
      1 => 1451925995,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '264058689568b79a6e69b41-36780860',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'site' => 0,
    'familles' => 0,
    'fam1' => 0,
    'fam2' => 0,
    'fam3' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568b79a6ef3eb6_76578081',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568b79a6ef3eb6_76578081')) {function content_568b79a6ef3eb6_76578081($_smarty_tpl) {?><header class="line m-auto">
	<div class="large-hidden pa1">
		<h1><img src="/assets/images/frontoffice/<?php echo $_smarty_tpl->tpl_vars['site']->value['TEMPLATE_NAME'];?>
/design/caahmro.jpg" alt="Caahmro"></h1>
		<div class="toggle"></div>
	</div>
</header>
<nav class="opacity">
	<!--[if lt IE 9]>
	<div class="ienomore">
		<table>
			<tr>
				<td style="width: auto !important;"><strong><i class="icon2-warning"></i></strong></td>
				<td style="width: 400px; !important;"><strong>Vous utilisez un navigateur dépassé depuis 2014.</strong><br />Pour une meilleure expérience web, prenez le temps de mettre à jour votre navigateur.<br />Si vous êtes équipé de windows XP, utilisez un autre navigateur qu'Internet Explorer.</td>
				<td><a href="https://www.mozilla.org/fr/firefox/desktop/" title="Firefox" class="navigateur"><i class="icon2-firefox"></i></a></td>
				<td><a href="http://windows.microsoft.com/fr-fr/internet-explorer/download-ie" title="IE" class="navigateur"><i class="icon2-IE"></i></a></td>
				<td><a href="http://support.apple.com/kb/dl1531" title="Safari" class="navigateur"><i class="icon2-safari"></i></a></td>
				<td><a href="https://www.google.fr/chrome/browser/desktop/" title="Chrome" class="navigateur"><i class="icon2-chrome"></i></a></td>
				<td><a href="http://www.opera.com/fr/computer/linux" title="Opera" class="navigateur"><i class="icon2-opera"></i></a></td>
			</tr>
		</table>
	</div><![endif]-->
	<div class="toggle"></div>
	<div class="line"></div>
	<ul class="mw1280p m-auto">
		<!--li class="width70">
			<a class="width70" href="/accueil.html">Accueil</a>
		</li-->
		<?php if (isset($_smarty_tpl->tpl_vars['familles']->value['cata1']['famille1'])) {?>
			<?php  $_smarty_tpl->tpl_vars['fam1'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['fam1']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['familles']->value['cata1']['famille1']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['fam1']->key => $_smarty_tpl->tpl_vars['fam1']->value) {
$_smarty_tpl->tpl_vars['fam1']->_loop = true;
?>
				<li <?php if (isset($_smarty_tpl->tpl_vars['fam1']->value['famille2'])) {?>class="has-subnav<?php if (isset($_smarty_tpl->tpl_vars['fam1']->value['ISLAST'])&&$_smarty_tpl->tpl_vars['fam1']->value['ISLAST']) {?> last-child<?php }?>"<?php }?>>
					<a href="<?php echo $_smarty_tpl->tpl_vars['fam1']->value['LINK'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['fam1']->value['LABEL'];?>
"><?php echo $_smarty_tpl->tpl_vars['fam1']->value['LABEL'];?>
</a>
					<?php if (isset($_smarty_tpl->tpl_vars['fam1']->value['famille2'])) {?>
					<ul>
						<?php  $_smarty_tpl->tpl_vars['fam2'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['fam2']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['fam1']->value['famille2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['fam2']->key => $_smarty_tpl->tpl_vars['fam2']->value) {
$_smarty_tpl->tpl_vars['fam2']->_loop = true;
?>
							<li <?php if (isset($_smarty_tpl->tpl_vars['fam2']->value['famille3'])) {?>class="third-level"<?php }?>>
								<a href="<?php echo $_smarty_tpl->tpl_vars['fam2']->value['LINK'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['fam2']->value['LABEL'];?>
"><?php echo $_smarty_tpl->tpl_vars['fam2']->value['LABEL'];?>
</a>
								<?php if (isset($_smarty_tpl->tpl_vars['fam2']->value['famille3'])) {?>
									<ul>
										<?php  $_smarty_tpl->tpl_vars['fam3'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['fam3']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['fam2']->value['famille3']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['fam3']->key => $_smarty_tpl->tpl_vars['fam3']->value) {
$_smarty_tpl->tpl_vars['fam3']->_loop = true;
?>
											<li>
												<a href="<?php echo $_smarty_tpl->tpl_vars['fam3']->value['LINK'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['fam3']->value['LABEL'];?>
"><?php echo $_smarty_tpl->tpl_vars['fam3']->value['LABEL'];?>
</a>
											</li>
										<?php } ?>
									</ul>
								<?php }?>
							</li>
						<?php } ?>
					</ul>
					<?php }?>
				</li>
			<?php } ?>
		<?php }?>
	</ul>
</nav><?php }} ?>
