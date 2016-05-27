<ul>
	<li <?= ($this->get('a') == 'identity')?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'identity'); ?>">
			<?= dims_constant::getVal('CATA_YOUR_CORPORATE_IDENTITY'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($this->get('a') == 'identity')?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($this->get('a') == 'rib')?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'rib'); ?>">
			<?= dims_constant::getVal('CATA_BANK_INFORMATIONS'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($this->get('a') == 'rib')?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($this->get('a') == 'edit')?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'edit'); ?>">
			<?= dims_constant::getVal('CATA_CATALOG_CONFIGURATION'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($this->get('a') == 'edit')?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= (in_array($this->get('a'), array('tva_index', 'tva_edit')))?'class="selected"':''; ?>>
	<a href="<?= get_path('params', 'tva_index'); ?>">
			<?= dims_constant::getVal('GESTION_DE_LA_TVA'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".((in_array($this->get('a'), array('tva_index', 'tva_edit')))?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($this->get('a') == 'tarif')?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'tarif'); ?>">
			<?= dims_constant::getVal('CATA_PRICES_MANAGEMENT'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($this->get('a') == 'tarif')?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($this->get('a') == 'espace')?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'espace'); ?>">
			<?= dims_constant::getVal('CATA_CUSTOMER_SPACES'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($this->get('a') == 'espace')?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($this->get('a') == 'notif')?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'notif'); ?>">
			<?= dims_constant::getVal('CATA_EMAIL_NOTIFICATIONS'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($this->get('a') == 'notif')?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($this->get('a') == 'compte')?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'compte'); ?>">
			<?= dims_constant::getVal('CATA_CUSTOMER_ACCOUNTS'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($this->get('a') == 'compte')?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($this->get('a') == 'payment_means')?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'payment_means'); ?>">
			<?= dims_constant::getVal('CATA_PAYMENT_MEANS'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($this->get('a') == 'payment_means')?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= (in_array($this->get('a'), array('lg_index', 'lg_add')))?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'lg_index'); ?>">
			<?= dims_constant::getVal('CATA_LANGUAGES'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".((in_array($this->get('a'), array('lg_add', 'lg_index')))?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= (in_array($this->get('a'), array('champs','editchamp')))?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'champs'); ?>">
			<?= dims_constant::getVal('_FILTERS_&_FREE_FIELDS'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".((in_array($this->get('a'), array('champs','editchamp')))?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= (in_array($this->get('a'), array('selections_templates')))?'class="selected"':''; ?>>
		<a href="<?= get_path('params', 'selections_templates'); ?>">
			<?= dims_constant::getVal('CATA_FAMILIES_SELECTIONS_TEMPLATES'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".((in_array($this->get('a'), array('selections_templates')))?'':'n')."s.png"); ?>" />
		</a>
	</li>
</ul>
