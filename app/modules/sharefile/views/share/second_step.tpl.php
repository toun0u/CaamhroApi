<?php
$nomsearch='';
if (isset($_SESSION['share']['currentsearch'])) $nomsearch=$_SESSION['share']['currentsearch'];

$entity = $this->get('entity');
$entityContacts = $this->get('entityContacts');
?>
<script src="js/jquery.form.js"></script>
<div class="dims_form" style="float:left; width:100%;padding-top:10px;">
	<div id="shareblock1">
		<?php if(!empty($entity)) : ?>
			<div>
				<h3><?= $entity['intitule']; ?></h3>
				<div>
					<ul id="entityuser">
					<?php foreach($entityContacts as $contact) : ?>
						<li>
							<?= $contact['firstname']; ?> <?= $contact['lastname']; ?>
							<a class="action" href="Javascript: void(0);" onclick="Javascript: addContact('<?= $contact['id']; ?>');" >
								<img style="float:right" src="/common/modules/sharefile/img/icon_ajouter.png" />
							</a>
						</li>
					<?php endforeach ; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>
		<div style="float:left;width:100%;display:block;">
			<span style="display: block; float: left; width: 20%;">
				<img src="/common/modules/sharefile/img/gestion_contact.png">
			</span>
			<span style="display: block; float: left; font-size: 16px; color: #424242; font-weight: bold; line-height: 64px; width: 65%;">
				<?= dims_constant::getVal('_DIRECTORY_MYCONTACTS'); ?>
			</span><br />
			<div style="float: left; clear: left; width: 40%; line-height: 27px;">
				<a class="action" href="Javascript: void(0);" onclick="Javascript: $('#lst_tempuser, #add_contact').toggle();">
					<?= dims_constant::getVal('_DIRECTORY_ADDNEWCONTACT'); ?>
					<img src="/common/modules/sharefile/img/icon_ajouter.png" style="float:right;"/>
				</a>
			</div>
			<div style="float: left; clear: left; width: 100%; margin-top: 20px;">
				<input value="<?= $nomsearch;?>" type="text" onkeyup="javascript:searchUserShare();" id="nomsearch" name="nomsearch" size="16" style="width: 85%;">
				<img style="cursor: pointer; float: right; margin-right: 4px;" onclick="javascript:searchFileInitSearch();" src="/common/modules/sharefile/img/icon_mini_supp.png" border="0">
				<img style="cursor: pointer; float: right; margin-right: 10px;" onclick="" src="/common/modules/sharefile/img/icon_mini_loupe.png" border="0">
			</div>
			<div id="lst_tempuser" style="width:100%;display:block;float:left;"></div>

			<form action="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'save_add_contact'))); ?>" id="add_contact" style="display:none;float:left;margin-top:30px;width:86%;">
				<div style="padding-bottom: 10px; overflow: hidden;">
					<span style="float: left; width: 20%; line-height: 21px;"><?= dims_constant::getVal('_DIMS_LABEL_NAME'); ?></span>
					<input class="text" type="text" style="width:300px;float:left;" id="ct_lastname" name="ct_lastname" value="" tabindex="2" />
				</div>
				<div style="padding-bottom: 10px; overflow: hidden;">
					<span style="float: left; width: 20%; line-height: 21px;"><?= dims_constant::getVal('_DIMS_LABEL_FIRSTNAME'); ?></span>
					<input class="text" type="text" style="width:300px;float:left;" id="ct_firstname" name="ct_firstname" value="" tabindex="2" />
				</div>
				<div>
					<span style="float: left; width: 20%; line-height: 21px;"><?= dims_constant::getVal('_DIMS_LABEL_EMAIL'); ?></span>
					<input class="text" type="text" style="width:300px;float:left;" id="ct_lastname" name="ct_email" value="" tabindex="3" />
				</div>
				<input style="float:right;margin-top:10px;"type="submit" value="<?= dims_constant::getVal('_DIMS_SAVE'); ?>" />
			</form>

		</div>
	</div>
	<div id="shareblock2">
		<div style="float:left;width:100%;display:block;">
			<span style="display: block; float: left; width: 20%;">
				<img src="/common/modules/sharefile/img/gestion_contact.png">
			</span>
			<span style="display: block; float: left; font-size: 16px; color: #424242; font-weight: bold; line-height: 64px; width: 65%;">
				<?= dims_constant::getVal('MES_CONTACTS_AJOUTES'); ?>
			</span><br />
			<div style="float: left; clear: left; width: 100%; margin-top: 20px; overflow: hidden;">
				<div id="lstselectedusers" style="float:left;width:100%;display:block;"></div>
			</div>
		</div>
	</div>
</div>
<div style="padding-top:20px;clear:both;float:left;width:100%;">
	<span style="width:50%;display:block;float:left;text-align:left;">
		<a style="text-decoration:none;padding-right:50px;" href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'first_step'))); ?>">
			<img style="border:0px;" src="./common/modules/sharefile/img/retour_etape1.png" alt="<?= dims_constant::getVal('_DIMS_PREVIOUS'); ?>"><span style="float:left;margin-left:10px;line-height:63px;margin-right:10px">Cliquez pour retourner a l'étape précédente</span>
		</a>
	</span>
	<span id="sharefile_button" style="width:39%;display:block;float:right;display:none;">
		<a style="text-decoration:none;" href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'third_step'))); ?>">
			<img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/puce_etape3.png" alt="<?= dims_constant::getVal('_DIMS_NEXT'); ?>"><span style="float:right;margin-left:10px;line-height:63px;">Cliquez pour passer a l'étape suivante</span>
		</a>
	</span>
</div>

<script language="JavaScript" type="text/JavaScript">
	function searchUserShareExec() {
		var nomsearch = $('#nomsearch').val();
		if(nomsearch.length > 2) {
			$.ajax({
				type: "POST",
				url: "<?= $this->get('urlbase'); ?>",
				data: {
					'op' : 'share',
					'action' : 'search_user',
					'nomsearch': nomsearch
				},
				dataType: "json",
				success: function(data){
					$('#lst_tempuser').empty();
					if(data.users.length != 0) {
						$('#lst_tempuser').append('<ul id="lst_tmpuser"></ul>')
						for(var user_id in data.users) {
							$('#lst_tmpuser').append(
								'<li id="tmpuser'+data.users[user_id].id+'">'+
									'<div>'+
										data.users[user_id].firstname+' '+data.users[user_id].lastname+
										'<a class="action" href="Javascript: void(0);" onclick="Javascript: addUser('+data.users[user_id].id+');" >'+
											'<img style="float:right" src="/common/modules/sharefile/img/icon_ajouter.png" />'+
										'</a>'+
									'</div>'+
								'</li>'
							);
						}
					}

					if(data.contacts.length != 0) {
						$('#lst_tempuser').append('<ul id="lst_tmpcontact"></ul>')
						for(var contact_id in data.contacts) {
							$('#lst_tmpcontact').append(
								'<li id="tmpcontact'+data.contacts[contact_id].id+'">'+
									'<div>'+
										data.contacts[contact_id].firstname+' '+data.contacts[contact_id].lastname+
										'<a class="action" href="Javascript: void(0);" onclick="Javascript: addContact('+data.contacts[contact_id].id+');" >'+
											'<img style="float:right" src="/common/modules/sharefile/img/icon_ajouter.png" />'+
										'</a>'+
									'</div>'+
								'</li>'
							);
						}
					}
				},
				error: function(data){}
			});
		}
	}

	function refreshParticipant() {
		$.ajax({
			type: "POST",
			url: "<?= $this->get('urlbase'); ?>",
			data: {
				'op' : 'share',
				'action' : 'get_participants'
			},
			dataType: "json",
			success: function(data){
				$('#lstselectedusers').empty();
				if(data.users.length != 0) {
					$('#lstselectedusers').append('<ul id="lst_user"></ul>')
					for(var user_id in data.users) {
						$('#lst_user').append(
							'<li id="tmpuser'+data.users[user_id].id+'">'+
								'<div style="float: left; width: 100%;line-height:32px">'+
									'<img src="/common/modules/sharefile/img/icon_avatar.png" />'+ data.users[user_id].firstname+' '+data.users[user_id].lastname+
									'<a class="action" href="Javascript: void(0);" onclick="Javascript: removeUser('+data.users[user_id].id+');" >'+
										'<img style="float: right; padding-top: 5px;"src="/common/modules/sharefile/img/icon_mini_supp.png" />'+
									'</a>'+
								'</div>'+
							'</li>'
						);
					}
					$('#sharefile_button').fadeIn();
				}

				if(data.contacts.length != 0) {
					$('#lstselectedusers').append('<ul id="lst_contact"></ul>')
					for(var contact_id in data.contacts) {
						$('#lst_contact').append(
							'<li id="tmpcontact'+data.contacts[contact_id].id+'">'+
								'<div style="float: left; width: 100%;line-height:32px">'+
									'<img src="/common/modules/sharefile/img/icon_avatar.png" />'+ data.contacts[contact_id].firstname+' '+data.contacts[contact_id].lastname+
									'<a class="action" href="Javascript: void(0);" onclick="Javascript: removeContact('+data.contacts[contact_id].id+');" >'+
										'<img style="float: right; padding-top: 5px;" src="/common/modules/sharefile/img/icon_mini_supp.png" />'+
									'</a>'+
								'</div>'+
							'</li>'
						);
					}
					$('#sharefile_button').fadeIn();
				}
			}
		});
	}

	function refreshEntityContact() {
		$.ajax({
			type: "POST",
			url: "<?= $this->get('urlbase'); ?>",
			data: {
				'op' : 'share',
				'action' : 'refresh_entity_contact'
			},
			dataType: "json",
			success: function(data){
				$('#entityuser').empty();

				if(data.contacts.length != 0) {
					for(var contact_id in data.contacts) {
						$('#entityuser').append(
							'<li id="tmpcontact'+data.contacts[contact_id].id+'">'+
								'<div>'+
									data.contacts[contact_id].firstname+' '+data.contacts[contact_id].lastname+
									'<a class="action" href="Javascript: void(0);" onclick="Javascript: addContact('+data.contacts[contact_id].id+');" >'+
										'<img style="float:right" src="/common/modules/sharefile/img/icon_ajouter.png" />'+
									'</a>'+
								'</div>'+
							'</li>'
						);
					}
				}
			},
			error: function(data){}
		});
	}

	function addUser(id_user) {
		$.ajax({
			type: "POST",
			url: "<?= $this->get('urlbase'); ?>",
			data: {
				'op' : 'share',
				'action' : 'add_user',
				'id_user': id_user
			},
			success: function(data){
				searchUserShareExec();
				refreshParticipant();
				<?php if(!empty($entity)) : ?>
					refreshEntityContact();
				<?php endif; ?>
			}
		});
	}

	function addContact(id_contact) {
		$.ajax({
			type: "POST",
			url: "<?= $this->get('urlbase'); ?>",
			data: {
				'op' : 'share',
				'action' : 'add_contact',
				'id_contact': id_contact
			},
			success: function(data){
				searchUserShareExec();
				refreshParticipant();
				<?php if(!empty($entity)) : ?>
					refreshEntityContact();
				<?php endif; ?>
			}
		});
	}

	function removeUser(id_user) {
		$.ajax({
			type: "POST",
			url: "<?= $this->get('urlbase'); ?>",
			data: {
				'op' : 'share',
				'action' : 'remove_user',
				'id_user': id_user
			},
			success: function(data){
				searchUserShareExec();
				refreshParticipant();
				<?php if(!empty($entity)) : ?>
					refreshEntityContact();
				<?php endif; ?>
			}
		});
	}

	function removeContact(id_contact) {
		$.ajax({
			type: "POST",
			url: "<?= $this->get('urlbase'); ?>",
			data: {
				'op' : 'share',
				'action' : 'remove_contact',
				'id_contact': id_contact
			},
			success: function(data){
				searchUserShareExec();
				refreshParticipant();
				<?php if(!empty($entity)) : ?>
					refreshEntityContact();
				<?php endif; ?>
			}
		});
	}

	$('#add_contact').ajaxForm(function() {
		$('#add_contact').resetForm();
		$('#lst_tempuser, #add_contact').toggle();

		searchUserShareExec();
		refreshParticipant();
		<?php if(!empty($entity)) : ?>
			refreshEntityContact();
		<?php endif; ?>
	});

	$(document).ready(function() {
		$('#nomsearch').focus();
		searchUserShareExec();
		refreshParticipant();
	});
</script>
