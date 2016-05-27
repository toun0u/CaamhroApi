<div class="elem">
	<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'add', 'reset' => '1'))); ?>">
		<img src="./common/modules/sharefile/img/nouveau_partage.png" />
		<?= dims_constant::getVal('NEW_SHARE'); ?><span>Nouveau partage</span>
	</a>
</div>
<div class="elem">
	<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'contacts', 'action' => 'list'))); ?>">
		<img src="./common/modules/sharefile/img/gestion_contact.png" />
		<?= dims_constant::getVal('CONTACT_MANAGEMENT'); ?><span>Gestion de vos contacts</span>
	</a>
</div>
<?php if(dims::getInstance()->isAdmin()): ?>
<div class="elem">
	<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'sharefile', 'action' => 'admin_params'))); ?>">
		<img src="./common/modules/sharefile/img/contacts.png" />
		<?= dims_constant::getVal('_DIMS_LABEL_ADMIN'); ?>
	</a>
</div>
<?php endif; ?>
