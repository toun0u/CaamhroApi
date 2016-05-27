<?php

require_once DIMS_APP_PATH.'modules/system/class_news_subscribed.php';

$contact = new contact();
$contact->open($_SESSION['dims']['user']['id_contact']);

if(!empty($_POST)) {
	$contact->setvalues($_POST, 'contact_');
	$contact->save();

	$nl = dims_load_securvalue('nl', dims_const::_DIMS_NUM_INPUT, true, true, true);
	foreach($nl as $nl_id => $state) {
		$nl_id = dims_sql_filter($nl_id);
		$newsLetterSubscription = new news_subscribed();
		$newsLetterSubscription->init_description();
		$newsLetterSubscription->open($nl_id, $contact->getId());

		if($state == 0 && $newsLetterSubscription->fields['etat'] == 1) {
			//if(!empty($newsLetterSubscription->fields['date_inscription']) && empty($newsLetterSubscription->fields['date_desinscription']))
			$newsLetterSubscription->fields['date_desinscription'] = date('YmdHis');
			$newsLetterSubscription->fields['etat'] = 0;
		}
		elseif($state == 1 && $newsLetterSubscription->fields['etat'] == 0) {
			//if(empty($newsLetterSubscription->fields['date_inscription']))
			$newsLetterSubscription->fields['date_inscription'] = date('YmdHis');

			$newsLetterSubscription->fields['etat'] = 1;
		}

		$newsLetterSubscription->save();
	}
}

?>
<div id="contener2">
	<div id="content2_2">
		<div class="title"><? echo $_DIMS['cste']['_DIMS_LABEL_MYPROFILE']; ?></div>
	</div>
	<div id="content2_3">
		<form method="post" action="">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td class="my_profile_separation1" style="border-right:1px solid #acacac">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td width="100%" colspan="2">
									<div class="photo_profil">
										<img src="/common/modules/events/img/photo_not_available.png" border="0" style="margin-left: 15px; margin-top: 20px;">
									</div>
									<div class="bloc_profil_text">
										<span style="float: left; font-size: 20px;font-family:trebuchet MS"><b><?php echo htmlspecialchars($contact->fields['lastname']); ?> </b> <?php echo htmlspecialchars($contact->fields['firstname']); ?></span>
										<span style="color: #62829F; font-size: 13px; text-decoration: underline;"><img src="/common/modules/events/img/add_picture.png" border="0"><?= dims_constant::getVal('ADD_PICTURE'); ?></span>
									</div>
								</td>
							</tr>
						</table>

					</td>
					<td class="my_profile_separation2" style="border-right:1px solid #acacac">
						<span style="float: left; width: 100%; color: #424242; font-weight: bold; margin-top: 20px; font-size: 20px;font-family:trebuchet MS"><?= dims_constant::getVal('_DIMS_PERS_COORD'); ?></span>
						<div id="step_contact">
							<div><a><?= dims_constant::getVal('EMAIL_ADDRESS'); ?> <span style="red">*</span></a><input type="text" name="contact_email" value="<?php echo htmlspecialchars($contact->fields['email']); ?>"></div>
							<div><a><?= dims_constant::getVal('PHONE_NUMBER'); ?> <span style="red">*</span></a><input type="text" name="contact_phone" value="<?php echo htmlspecialchars($contact->fields['phone']); ?>"></div>
							<div><a><?= dims_constant::getVal('_MOBILE'); ?> <span style="red">*</span></a><input type="text" name="contact_mobile" value="<?php echo htmlspecialchars($contact->fields['mobile']); ?>"></div>
							<div><a><?= dims_constant::getVal('_DIMS_LABEL_ADDRESS'); ?> <span style="red">*</span></a><input type="text" name="contact_address" value="<?php echo htmlspecialchars($contact->fields['address']); ?>"></div>
							<div><a><?= dims_constant::getVal('_DIMS_LABEL_CITY'); ?> <span style="red">*</span></a><input type="text" name="contact_city" value="<?php echo htmlspecialchars($contact->fields['city']); ?>"></div>
							<div><a><?= dims_constant::getVal('_DIMS_LABEL_CP'); ?> <span style="red">*</span></a><input type="text" name="contact_postalcode" value="<?php echo htmlspecialchars($contact->fields['postalcode']); ?>"></div>
							<div><a><?= dims_constant::getVal('_DIMS_LABEL_COUNTRY'); ?> <span style="red">*</span></a><input type="text" name="contact_country" value="<?php echo htmlspecialchars($contact->fields['country']); ?>"></div>
						</div>
						<a class="mandatory"><span style="red">*</span> <?= dims_constant::getVal('_DIMS_LABEL_MANDATORY_FIELDS'); ?></a>
					</td>
					<td class="my_profile_separation3">
						<span style="float: left; width: 100%; color: #424242; font-weight: bold; margin-top: 20px; font-size: 20px;font-family:trebuchet MS"><?= dims_constant::getVal('NEWSLETTERS'); ?></span>
						<span style="float: left; width: 100%; color: #424242; font-style: italic; font-size: 13px; margin-top: 10px; margin-bottom: 10px;"><?= dims_constant::getVal('KEEP_INFORMED_BY_NEWSLETTER'); ?></span>
						<?php
						$sql_in = "	SELECT 	id_to
											FROM 	dims_workspace_share
											WHERE 	id_from = :idfrom
											AND 	active = 1
											AND 	id_object = :idobject";
						$res_in = $db->query($sql_in, array(':idfrom'=> $_SESSION['dims']['workspaceid'], ':idobject' => dims_const::_SYSTEM_OBJECT_NEWSLETTER) );

						$listworkspace_nl = "";
						$params = array();
						if($db->numrows($res_in) >= 1) {
								$_i = 0;
								while($tabw = $db->fetchrow($res_in)) {
										$listworkspace_nl .= " , :idworkspace".$_i;
										$params[':idworkspace'.$_i] = $tabw['id_to'];
										$_i++;
								}
								$params[':idworkspaceworkspacecourant'] = $_SESSION['dims']['workspaceid'];
								$listworkspace_nl .= " , :idworkspacecourant "; //on ajoute le workspace courant sinon il sera exclu des recherches
						}
						else {
								$listworkspace_nl = $_SESSION['dims']['workspaceid'];
						}
						$sql = 'SELECT		distinct n.*,
											s.id_contact as id_inscr,
											s.etat AS state_subscription
								FROM        dims_mod_newsletter n
								LEFT JOIN   dims_mod_newsletter_subscribed s
								ON          s.id_newsletter = n.id
								AND			s.id_contact = :idcontact
								WHERE       n.id_workspace in ( '.$listworkspace_nl.' )
								GROUP by    n.id
								ORDER BY	n.label ASC';
						$params[':idcontact'] = $contact->getId();
						$res = $db->query($sql, $params);

						$tab_news = array();
						while($tab_res = $db->fetchrow($res)){
							?>
							<input type="hidden" name="nl[<?= $tab_res['id']; ?>]" value="0" />
							<input type="checkbox" name="nl[<?= $tab_res['id']; ?>]" value="1" <?= ($tab_res['state_subscription']) ? 'checked="checked"' : ''; ?>/>
							<span style="width:315px; color: #424242; font-size: 13px;"><?= $tab_res['label']; ?></span>
							<br/>
							<?php
						}
						?>
					</td>
				</tr>
			</table>
			<div class="lien_bas">
				<div class="bas">
					<input type="submit" name="Save" value="<?= dims_constant::getVal('_DIMS_SAVE'); ?>"><span><?= dims_constant::getVal('_DIMS_OR'); ?></span><a style="float:right" href="<?php echo $dims->getScriptEnv().'?submenu=menu'; ?>"><?= dims_constant::getVal('_DIMS_CANCEL'); ?></a>
				</div>
			</div>
		</form>
	</div>
</div>
