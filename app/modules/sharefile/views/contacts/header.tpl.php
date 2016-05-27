<div class="elem">
	<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'contacts', 'action' => 'list'))); ?>">
		<img src="/common/modules/sharefile/img/gestion_contact.png" />
		<?= dims_constant::getVal('CONTACT_MANAGEMENT'); ?><span>Gestion de vos contacts</span>
	</a>
</div>
<ul style="list-style-type: none; overflow: hidden; height: auto;padding-top:7px;">
	<li style="float: left; width: 53%;">
		<a style="line-height:27px;color:#424242;float:left;" href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'contacts', 'action' => 'add'))); ?>">
			<?= dims_constant::getVal('_DIRECTORY_ADDNEWCONTACT'); ?><img src="/common/modules/sharefile/img/icon_ajouter.png" style="float:left;"/>
		</a>
	</li>
</ul>
