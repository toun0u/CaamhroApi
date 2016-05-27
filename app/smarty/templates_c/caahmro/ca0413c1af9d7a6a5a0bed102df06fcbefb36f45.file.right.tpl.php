<?php /* Smarty version Smarty-3.1.19, created on 2016-01-05 09:07:03
         compiled from "/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/right.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1910914981568b79a70494e6-61213372%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ca0413c1af9d7a6a5a0bed102df06fcbefb36f45' => 
    array (
      0 => '/var/www/caahmro-mobile/app/templates/frontoffice/caahmro/right.tpl',
      1 => 1451925995,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1910914981568b79a70494e6-61213372',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'global_filter_label' => 0,
    'returnURI' => 0,
    'switch_user_logged_out' => 0,
    'site' => 0,
    'headings' => 0,
    'menuprincipal' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568b79a70c3434_55802445',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568b79a70c3434_55802445')) {function content_568b79a70c3434_55802445($_smarty_tpl) {?>	<?php if ((isset($_smarty_tpl->tpl_vars['global_filter_label']->value))) {?>
	<div class="mod content-zone-green">
		<article class="mod txtcenter mod-separator">
			<div class="pa1">
				<div id="global_filter_info" class="mw1280p m-auto txtwhite">
					Vous êtes actuellement dans l'espace "<?php echo $_smarty_tpl->tpl_vars['global_filter_label']->value;?>
"
					<a class="btn btn-primary" href="<?php echo $_smarty_tpl->tpl_vars['returnURI']->value;?>
">Retourner au site complet</a>
				</div>
			</div>
		</article>
	</div>
	<?php }?>

	<div class="mod content-zone">
		<article class="mod txtcenter mod-separator">
			<div class="pa1">
				<header>
					<h1 class="txtcenter line">
						<i class="icon-user title-icon"></i>
						Mon compte
					</h1>
				</header>
				<section>
					<div id="connexion">
						<div id="account" class="<?php if ((isset($_smarty_tpl->tpl_vars['switch_user_logged_out']->value))) {?>logged_out<?php } else { ?>logged_in<?php }?>">
							<?php if ((isset($_smarty_tpl->tpl_vars['switch_user_logged_out']->value))) {?>
								<form action="/index.php" method="post" class="navbar-search pull-right">
									<div class="collapse-group" style="float: left; margin-left: 40px;">
										<input style="width: 90% ! important;" type="text" name="dims_login" placeholder="Ex: 000999 votre n° client à 6 chiffres">
									</div>
									<div class="collapse-group" style="float: left; margin-left: 40px;">
										<input style="width: 78% ! important;" type="password" name="dims_password" placeholder="Mot de passe...">
										<input type="submit" value="" class="password-btn">
									</div>
									<!--a style="float:left;" class="password" href="/index.php?op=mdp_perdu">Mot de passe perdu <img style="padding-left: 5px;" border="0" src="/assets/images/frontoffice/<?php echo $_smarty_tpl->tpl_vars['site']->value['TEMPLATE_NAME'];?>
/design/icon-perdu.png"></a-->
								</form>
								<a style="float:left; margin: 8px 0 0 40px;" href="/index.php?op=connexion">Première visite ? Cliquez ici</a>
							<?php } else { ?>
								<div class="on" id="account_logged">
									<div class="user">
										<b style="color:#2E2E2E">Bienvenue,</b><font style="color:#2E2E2E"> <?php echo $_SESSION['dims']['user']['firstname'];?>
 <?php echo $_SESSION['dims']['user']['lastname'];?>
</font>
									</div>
									<div class="espace_client" style="float: left; width: 100%;">
										<span class="icon"><a class="btn btn-primary btn-small" href="/index.php?op=compte">Mon espace perso</a></span>
										<span style="float:right;" class="logout"><a class="btn btn-primary btn-small" title="Me déconnecter" href="/index.php?dims_logout=1">Me déconnecter</a></span>
									</div>

								</div>
							<?php }?>
						</div>
					</div>
				</section>
			</div>
		</article>
	</div>
	<div class="mod content-zone">
		<article class="mod txtcenter mod-separator">
			<div class="pa1">
				<header>
					<h1 class="txtcenter line">
						<i class="icon-cart title-icon"></i>
						Votre panier
					</h1>
				</header>
				<section>
					<div id="divpanier" class="right-box-middle"></div>
					<a class="btn btn-primary" href="/index.php?op=panier">Voir mon panier</a>
				</section>
			</div>
		</article>
	</div>
	<div class="mod content-zone">
		<article class="mod txtcenter mod-separator">
			<div class="pa1">
				<header>
					<h1 class="txtcenter line">
						Infos utiles
					</h1>
				</header>
				<section>
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
				</section>
			</div>
		</article>
	</div>
</section>
<?php }} ?>
