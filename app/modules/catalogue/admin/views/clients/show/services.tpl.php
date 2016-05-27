<?php
$view = view::getInstance();
$client = $view->get('client');
$parent = $view->get('parent');
$current = $view->get('current');
?>
<h3>
	<?= $current->fields['label']; ?>
</h3>
<div class="action">
	<a class="link_img" href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'add', 'grid' => $current->get('id'))); ?>">
		<img src="<?= $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
		<span><?= dims_constant::getVal('_ADD_SUB_SERVICE'); ?></span>
	</a>
	<a class="link_img" href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'edit', 'grid' => $current->get('id'))); ?>">
		<img src="<?= $view->getTemplateWebPath('gfx/edit16.png'); ?>" />
		<span><?= dims_constant::getVal('_EDIT_THIS_SERVICE'); ?></span>
	</a>
</div>
<div style="padding-top:10px;">
	<label>
		<?= dims_constant::getVal('_DELIVERY_ADDRESS'); ?> :
	</label>
	<?php
	$adr = $current->getAdr();
	if(!$adr->isNew())
		echo $adr->fields['adr1'].(($adr->fields['adr2'] != '')?" ".$adr->fields['adr2']:"").(($adr->fields['adr3'] != '')?" ".$adr->fields['adr3']:"")." - ".$adr->fields['cp']." ".$adr->getCity()->getLabel()." - ".$adr->getCountry()->getLabel();
	else
		echo dims_constant::getVal('_NEWS_LABEL_UNKNOWN');
	?>
	<a class="link_img" href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'addadr', 'grid' => $current->get('id'))); ?>">
		<img src="<?= $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
		<span><?= dims_constant::getVal('_ADD_DELIVERY_ADDRESS'); ?></span>
	</a>
</div>
<h4>
	<?= dims_constant::getVal('_USERS_LIST'); ?>
</h4>
<div class="action">
	<?php if($parent->get('id') != $current->get('id')){ ?>
	<a class="link_img" href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'attach', 'grid' => $current->get('id'))); ?>">
		<img src="<?= $view->getTemplateWebPath('gfx/trombone16.png'); ?>" />
		<span><?= dims_constant::getVal('_ATTACH_USER'); ?></span>
	</a>
	<?php } ?>
	<a class="link_img" href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'edituser', 'grid' => $current->get('id'))); ?>">
		<img src="<?= $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
		<span><?= dims_constant::getVal('_ADD_USER'); ?></span>
	</a>
</div>
<table class="tableau">
	<tr>
		<td class="w10 title_tableau">
			<?= dims_constant::getVal('_DIMS_LABEL_NAME'); ?>
		</td>
		<td class="w30 title_tableau">
			<?= dims_constant::getVal('_LOGIN'); ?>
		</td>
		<?php if($view->get('active_serv')){ ?>
		<td class="w25 title_tableau">
			<?= dims_constant::getVal('_SERVICE'); ?>(s)
		</td>
		<?php } ?>
		<td class="w25 title_tableau">
			<?= dims_constant::getVal('_DIMS_LABEL_LEVEL'); ?> (<?= $parent->fields['label']; ?>)
		</td>
		<td class="w25 title_tableau">
			<?= dims_constant::getVal('CRM_SHEET'); ?>
		</td>
		<td class="w10 title_tableau">
			<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
		</td>
	</tr>
	<?php
	$lstServicesDipo = cata_param::getSelectServicesDispo();
	foreach($view->get('users') as $user){
		// lien de connexion sur le compte utilisateur
		$useLink = '/index.php?dims_url='.urldecode(base64_encode('dims_login='.$user->fields['login'].'&dims_password='.$user->fields['password'].'&already_hashed=1'));

		?>
		<tr>
			<td>
				<?= $user->getFirstname()." ".$user->getLastname(); ?>
			</td>
			<td>
				<?= $user->fields['login']; ?>
			</td>
			<td>
				<?php
				$lstGr = array();
				foreach($user->getMyGroups() as $gr){
					if($parent->get('id') == $gr->get('id'))
						$lstGr[] = '<a href="'.get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')).'">'.$gr->getLabel().'<a/>';
					elseif(in_array($parent->get('id'),explode(';',$gr->fields['parents']))){
						$lstGr[] = '<a href="'.get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $gr->get('id'))).'">'.$gr->getLabel().'<a/>';
					}
				}
				echo implode('<br />',$lstGr);
				?>
			</td>
			<?php if($view->get('active_serv')){ ?>
			<td>
				<?php
				$group_user = new group_user();
				$group_user->open($parent->get('id'),$user->get('id'));
				$default_lvl = cata_param::GetLabelCorresp($group_user->fields['adminlevel']);
				echo (isset($lstServicesDipo[$default_lvl]))?$lstServicesDipo[$default_lvl]:dims_constant::getVal('_NEWS_LABEL_UNKNOWN');
				?>
			</td>
			<?php } ?>
			<td>
				<?php
				if(!$user->getContact()->isNew()) {
					?>
					<a href="/admin.php?dims_mainmenu=0&mode=contact&action=show&id=<?= $user->getContact()->getId(); ?>">
						<?= dims_constant::getVal('GO_TO_CRM_SHEET'); ?>
					</a>
					<?php
				}
				?>
			</td>
			<td class="center">
				<a style="text-decoration:none;" href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'edituser', 'grid' => $current->get('id'), 'uid' => $user->get('id'))); ?>">
					<img src="<?= $view->getTemplateWebPath('gfx/edit16.png'); ?>"  alt="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" />
				</a>
				<a style="text-decoration:none;" href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'switchuser', 'grid' => $current->get('id'), 'uid' => $user->get('id'))); ?>">
					<?php if($user->fields['status']){ ?>
						<img src="<?= $view->getTemplateWebPath('gfx/main.png'); ?>"  alt="<?= dims_constant::getVal('_DIMS_LABEL_DISABLED'); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_DISABLED'); ?>" />
					<?php }else{ ?>
						<img src="<?= $view->getTemplateWebPath('gfx/pouce16.png'); ?>"  alt="<?= dims_constant::getVal('_DIMS_ENABLED'); ?>" title="<?= dims_constant::getVal('_DIMS_ENABLED'); ?>" />
					<?php } ?>
				</a>
				<a style="text-decoration:none;" href="<?= $useLink; ?>">
					<img src="<?= $view->getTemplateWebPath('gfx/utiliser_compte.png'); ?>"  alt="" title="" />
				</a>
				<?php if($current->get('id') != $parent->get('id')){ ?>
				<a style="text-decoration:none;" href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'detachuser', 'grid' => $current->get('id'), 'uid' => $user->get('id'))); ?>">
					<img src="<?= $view->getTemplateWebPath('gfx/rompre_lien16.png'); ?>"  alt="<?= dims_constant::getVal('DETACH_THIS_CONTACT'); ?>" title="<?= dims_constant::getVal('DETACH_THIS_CONTACT'); ?>" />
				</a>
				<?php } ?>
			</td>
		</tr>
		<?
	}
	?>
</table>
