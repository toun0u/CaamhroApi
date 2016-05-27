<?php
require_once DIMS_APP_PATH.'modules/system/class_workspace.php';
require_once DIMS_APP_PATH.'modules/system/class_newsletter_inscription.php';

$dateday = date("Ymd");
$old_lang=array();
$old_id_lang=0;

if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']) &&
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']!=$_SESSION['dims']['currentlang']) {

	$old_lang=$_SESSION['cste'];
	$old_id_lang=$_SESSION['dims']['currentlang'];

	$dims->loadLanguage($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'],$_SESSION['dims']['currentlang']);
}

//recherche des news disponibles
$sql = 'SELECT * FROM dims_mod_newsletter where etat = 1 order by label';
$res = $db->query($sql);
$tabnl =array();

while($tab_news = $db->fetchrow($res)) {
	$tabnl[$tab_news['id']]=$tab_news['label'];
}
$res = $db->query($sql);

//'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_AVAILABLE'].'
echo	'<div id="newsletter_front"><form method="post" name="newsletter_subscribe">';

// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("nl_nom");
$token->field("nl_prenom");
$token->field("nl_fonction");
$token->field("nl_entreprise");
$token->field("nl_email");
$token->field("nl_tel");
$token->field("nl_adresse");
$token->field("nl_ville");
$token->field("nl_cp");
$token->field("nl_pays");
$token->field("captcha");

echo 	'<table width="100%" style="border:0px;font-size:13px;font-family:Trebuchet MS,Arial,Helvetica,sans-serif;">
				<tr  style="border:0px;">
					<td  style="border:0px;">';

//enregistrement du formulaire
$style_name = '';
$style_fname = '';
$style_email = '';
$style_address = '';
$style_captcha = '';
$style_country = '';
$style_company = '';
$style_phone = '';
$style_city = '';
$style_cp = '';
$style_title = '';
$validate = 0;
if(isset($_POST) && !empty($_POST) &&
	   isset($_SESSION['dims']['captcha']) && !empty($_SESSION['dims']['captcha'])) {

		$captcha = $_SESSION['dims']['captcha'];
		$nb_news = 0;
		//on calcul le nombre de news selectionnees
		$fields = dims_load_securvalue($_POST, dims_const::_DIMS_CHAR_INPUT, true, true, true);
		foreach($_POST as $field => $val) {
			$test = substr($field, 0, 5);
			if($test == 'news_') {
				$nb_news++;
			}
		}

		if($captcha == $_POST['captcha']) {

			if(!empty($_POST['nl_nom']) &&
			   !empty($_POST['nl_prenom']) &&
			   !empty($_POST['nl_email']) &&
			   !empty($_POST['nl_fonction']) &&
			   !empty($_POST['nl_entreprise']) &&
			   !empty($_POST['nl_pays']) &&
			   !empty($_POST['nl_tel']) &&
			   !empty($_POST['nl_adresse']) &&
			   !empty($_POST['nl_ville']) &&
			   !empty($_POST['nl_cp']) &&
			   $nb_news != 0) {

				$work = new workspace();
				$work->open($_SESSION['dims']['workspaceid']);

				//on va maintenant faire l'enregistrement pour chaques news selectionnees
				for($cpt=1; $cpt<= $nb_news; $cpt++) {
					$inscription = new newsletter_inscription();

					$inscription->setvalues($_POST,'nl_');
					$p_key = 'news_'.$cpt;
					$inscription->fields['id_newsletter'] = dims_load_securvalue($p_key, dims_const::_DIMS_NUM_INPUT, true, true, true);
					$inscription->fields['date_inscription'] = date('YmdHis');
					$id_inscr = $inscription->save();

					// envoi d'un email pour la personne qui demande
					$from	= array();
					$to		= array();
					$subject= '';
					$message= '';

					$to[0]['name']	   = dims_load_securvalue('nl_nom', dims_const::_DIMS_CHAR_INPUT, true, true, true).' '.dims_load_securvalue('nl_prenom', dims_const::_DIMS_CHAR_INPUT, true, true, true);
					$to[0]['address']  = dims_load_securvalue('nl_email', dims_const::_DIMS_CHAR_INPUT, true, true, true);

					$from[0]['name']   = $work->fields['newsletter_sender_email'];
					$from[0]['address']= $work->fields['newsletter_sender_email'];

					//creation du lien de désinscription
					/*$sql_r =	 "SELECT d.domain
												FROM dims_domain d
												INNER JOIN dims_workspace w
												ON w.newsletter_id_domain = d.id
												AND w.id = ".$_SESSION['dims']['workspaceid'];

							$res_r = $db->query($sql_r);
							$dom = $db->fetchrow($res_r);*/
					require_once(DIMS_APP_PATH . "/modules/wce/include/classes/class_wce_site.php");
										$wcesite = new wce_site($db);
										$urlarticle=$wcesite->getArticleByObject('system','newsletter',$extraparams); // name of moduletype, name of label wce object
					$link=$urlarticle.'/index.php?op=newsletter&action=news_unsubscribe&id_news='.dims_load_securevalue($p_key, dims_cont::_DIMS_NUM_INPUT, true, true, true).'&id_subscription='.$id_inscr;
										if ($extraparams!='') $link.=$extraparams;
										$link = dims_urlencode($link);
					$link_unsub = '<a href="'.$link.'">To cancel your subscription please click here.</a>';

					$subject = $work->getMessage('newsletter_accepted_subject','','',$tabnl[dims_load_securevalue($p_key, dims_cont::_DIMS_CHAR_INPUT, true, true, true)]); // 'Newsletter registration : '.$tabnl[$_POST[$p_key]];
					$content = dims_nl2br($work->getMessage('newsletter_accepted_content',dims_load_securevalue('nl_nom', dims_cont::_DIMS_CHAR_INPUT, true, true, true),dims_load_securevalue('nl_prenom', dims_cont::_DIMS_CHAR_INPUT, true, true, true),$tabnl[dims_load_securevalue($p_key, dims_cont::_DIMS_CHAR_INPUT, true, true, true)], $link_unsub));
					/*$content = 'Dear '.$_POST['nl_nom'].' '.$_POST['nl_prenom'].",";
					$content .= '<br /><br />Thank you for your registration. Your email has now been added to our distribution list.';
					$content .= '<br /><br />Best regards,';
					*/
					//$content.= '<br /><br />'.$work->fields['signature'];
					$content.= '<br /><br />'.str_replace("\n","<br>",$work->fields['signature']);

					dims_send_mail($from, $to, $subject, $content);
					$content="";
				}
				$validate = 1;
				echo '<font  style="font-size:16px;margin-top:10px;">';
				// echo $_SESSION['cste']['_DIMS_TEXT_REGISTRATION_WAIT_EMAIL'];
				echo dims_nl2br($work->getMessage('newsletter_message_registration'));
				echo '</font>';
			}
			else {
				if(empty($_POST['nl_nom'])) {
					$erreur['fname'] = $_SESSION['cste']['_DIMS_FORM_MISSING_FIRSTNAME'];
					$style_name = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_prenom'])) {
					$erreur['lname'] = $_SESSION['cste']['_DIMS_FORM_MISSING_LASTNAME'];
					$style_fname = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_email'])) {
					$erreur['email'] = $_SESSION['cste']['_DIMS_FORM_MISSING_EMAIL'];
					$style_email = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_fonction'])) {
					$erreur['title'] = $_SESSION['cste']['_DIMS_FORM_MISSING_TITLE'];
					$style_title = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_entreprise'])) {
					$erreur['company'] = $_SESSION['cste']['_DIMS_FORM_MISSING_COMPANY'];
					$style_company = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_pays'])) {
					$erreur['country'] = $_SESSION['cste']['_DIMS_FORM_MISSING_COUNTRY'];
					$style_country = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_tel'])) {
					$erreur['phone'] = $_SESSION['cste']['_DIMS_FORM_MISSING_TEL'];
					$style_phone = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_adresse'])) {
					$erreur['address'] = $_SESSION['cste']['_DIMS_FORM_MISSING_ADDRESS'];
					$style_address = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_ville'])) {
					$erreur['city'] = $_SESSION['cste']['_DIMS_FORM_MISSING_CITY'];
					$style_city = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_cp'])) {
					$erreur['cp'] = $_SESSION['cste']['_DIMS_FORM_MISSING_CP'];
					$style_cp = 'background-color:#F7D3D3;';
				}
				if($nb_news == 0) {
					$erreur['news'] = $_SESSION['cste']['_DIMS_FORM_MISSING_NEWS'];
				}
			}
		}
		else {
			$erreur['captcha'] = $_SESSION['cste']['_DIMS_FORM_BAD_CAPTCHA'];
			$style_captcha = 'background-color:#F7D3D3;';

			if(empty($_POST['nl_nom'])) {
					$erreur['fname'] = $_SESSION['cste']['_DIMS_FORM_MISSING_FIRSTNAME'];
					$style_name = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_prenom'])) {
					$erreur['lname'] = $_SESSION['cste']['_DIMS_FORM_MISSING_LASTNAME'];
					$style_fname = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_email'])) {
					$erreur['email'] = $_SESSION['cste']['_DIMS_FORM_MISSING_EMAIL'];
					$style_email = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_fonction'])) {
					$erreur['title'] = $_SESSION['cste']['_DIMS_FORM_MISSING_TITLE'];
					$style_title = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_entreprise'])) {
					$erreur['company'] = $_SESSION['cste']['_DIMS_FORM_MISSING_COMPANY'];
					$style_company = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_pays'])) {
					$erreur['country'] = $_SESSION['cste']['_DIMS_FORM_MISSING_COUNTRY'];
					$style_country = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_tel'])) {
					$erreur['phone'] = $_SESSION['cste']['_DIMS_FORM_MISSING_TEL'];
					$style_phone = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_adresse'])) {
					$erreur['address'] = $_SESSION['cste']['_DIMS_FORM_MISSING_ADDRESS'];
					$style_address = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_ville'])) {
					$erreur['city'] = $_SESSION['cste']['_DIMS_FORM_MISSING_CITY'];
					$style_city = 'background-color:#F7D3D3;';
				}
				if(empty($_POST['nl_cp'])) {
					$erreur['cp'] = $_SESSION['cste']['_DIMS_FORM_MISSING_CP'];
					$style_cp = 'background-color:#F7D3D3;';
				}
				if($nb_news == 0) {
					$erreur['news'] = $_SESSION['cste']['_DIMS_FORM_MISSING_NEWS'];
				}
		}
	}

	if(!isset($_POST) || empty($_POST) || !empty($erreur)) {


		//echo $_SESSION['cste']['_DIMS_FRONT_NL_TEXT_INSCRIPTION'].'<br/>';
		echo '<font  style="font-size:14px;font-weight:bold;color:#FF0000;">';
		if(!empty($erreur)) {
			foreach($erreur as $value) {
				echo $value.'<br/>';
			}
		}
		echo '</font>';
	}
	//ne se trouve pas au bon endroit ??...
	/*else {
		echo '<div class="nonews">';
		echo $_SESSION['cste']['_DIMS_FRONT_NL_TEXT_NO_NEWSLETTER'];
		echo '</div>';
	}*/

echo				'</td>
				</tr>';
if($validate != 1) {
echo			'<tr>
					<td  style="border:0px;">
						<table style="border:0px;">';
$i = 0;

$work = new workspace();
if (isset($_SESSION['dims']['webworkspaceid']) && $_SESSION['dims']['webworkspaceid']>0) {
	$work->open($_SESSION['dims']['webworkspaceid']);
}
else {
	$work->open($_SESSION['dims']['workspaceid']);
}

echo					'	<tr><td colspan="3">'.dims_nl2br($work->fields['newsletter_header_registration']).'</td></tr>';

while($tab_news = $db->fetchrow($res)) {
	/*
	$sql = "SELECT DISTINCT		d.domain
			FROM				dims_domain d
			INNER JOIN			dims_workspace_domain wd
			ON					wd.id_domain = d.id
			AND					wd.access =1
			INNER JOIN			dims_workspace w
			ON					w.id = wd.id_workspace
			AND					w.id = ".$work->fields['id'].")";

	echo $sql
	$res_w = $db->query($sql);
	if($db->numrows($res_w) > 0) {
		$OK = 0;
		while($tab_d = $db->fetchrow($res_w)) {
			if($dims->getHttpHost() == $tab_d['domain']) $OK = 1;
		}
		if($OK == 1) {
			$i++;
			if (isset($_POST['news_'.$i])) $sele="checked";
			else $sele="";
			echo			'		<tr>
									<td width="25%" align="left" style="vertical-align:top;color:#6699CC;font-style:italic;font-weight:bold;font-size:15px;">'.$tab_news['label'].'</td>
									<td width="5%"><input type="checkbox" id="news_'.$i.'" name="news_'.$i.'" '.$sele.' value="'.$tab_news['id'].'"/></td>
									<td style="padding-left:15px;font-size:13px;">'.substr($tab_news['descriptif'],0,100).'</td>
								</tr>';
				//<td style="padding-left:15px;font-size:13px;">'.substr($tab_news['descriptif'],0,100).'</td>
		}

	}*/
	if ($tab_news['id_workspace']==$work->fields['id']) {
		$i++;
			if (isset($_POST['news_'.$i])) $sele="checked";
			else $sele="";
			echo	'<tr  style="border:0px;">
					<td width="25%" align="left" style="vertical-align:top;color:#6699CC;font-style:italic;font-weight:bold;font-size:15px;">'.$tab_news['label'].'</td>
					<td width="5%"><input type="checkbox" id="news_'.$i.'" name="news_'.$i.'" '.$sele.' value="'.$tab_news['id'].'"/></td>
					<td style="padding-left:15px;font-size:13px;">'.$tab_news['descriptif'].'</td>
				</tr>';
			$token->field('news_'.$i);
	}

}
$lfb = '';

echo '
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>'; ?>
					<div class="inscription">
						<div class="bloc_info1" id="form_1">
							<p>
								<label for="nl_nom">
								<?php echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_nom" name="nl_nom" class="content" value="<?php if(isset($_POST['nl_nom'])) echo dims_load_securevalue('nl_nom', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_name; ?>"/>
							</p>
							<p>
								<label for="nl_prenom">
								<?php echo $_SESSION['cste']['_FIRSTNAME']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_prenom" name="nl_prenom" value="<?php if(isset($_POST['nl_prenom'])) echo dims_load_securevalue('nl_prenom', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_fname; ?>"/>
							</p>
							  <p>
								<label for="nl_fonction">
								<?php echo $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?>
								</label>
								<span class="red">*</span>
							</p>
							 <p>
								<input type="text" id="nl_fonction" name="nl_fonction" value="<?php if(isset($_POST['nl_fonction'])) echo dims_load_securevalue('nl_fonction', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_title; ?>"/>
							</p>
							<p>
								<label for="nl_entreprise">
								<?php echo $_SESSION['cste']['_DIMS_LABEL_COMPANY']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_entreprise" name="nl_entreprise" value="<?php if(isset($_POST['nl_entreprise'])) echo dims_load_securevalue('nl_entreprise', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_company; ?>"/>
							</p>
							   <p>
								<label for="nl_email">
								<?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_email" name="nl_email" value="<?php if(isset($_POST['nl_email'])) echo dims_load_securevalue('nl_email', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_email; ?>"/>
							</p>
						</div>
						<div class="bloc_info2" id="form_1">
							<p>
								<label for="nl_tel">
								<?php echo $_SESSION['cste']['_PHONE']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_tel" name="nl_tel" value="<?php if(isset($_POST['nl_tel'])) echo dims_load_securevalue('nl_tel', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_phone; ?>"/>
							</p>
							<p>
								<label for="nl_adresse">
								<?php echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_adresse" name="nl_adresse" value="<?php if(isset($_POST['nl_adresse'])) echo dims_load_securevalue('nl_adresse', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_address; ?>"/>
							</p>
							<p>
								<label for="nl_ville">
								<?php echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_ville" name="nl_ville" value="<?php if(isset($_POST['nl_ville'])) echo dims_load_securevalue('nl_ville', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_city; ?>"/>
							</p>
							<p>
								<label for="nl_cp">
								<?php echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_cp" name="nl_cp" value="<?php if(isset($_POST['nl_cp'])) echo dims_load_securevalue('nl_cp', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_cp; ?>"/>
							</p>
							<p>
								<label for="nl_pays">
								<?php echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?>
								</label>
								<span class="red">*</span>
							</p>
							<p>
								<input type="text" id="nl_pays" name="nl_pays" value="<?php if(isset($_POST['nl_pays'])) echo dims_load_securevalue('nl_pays', dims_cont::_DIMS_CHAR_INPUT, true, true, true); ?>" style="<?php echo $style_country; ?>"/>
							</p>

						</div>
						<p style="clear: both;margin-bottom:0px;">
								<span style="color:#FF0000">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span>
						</p>
						<div class="captcha" id="form_1" style="margin-top:0px;">
							<p>
								<?php echo $_SESSION['cste']['_DIMS_TEXT_CAPTCHA']; ?><br />
								<img src="./common/modules/system/cms_captcha.php?<?= mktime(); ?>" /><br />
								<input type="text" name="captcha" style="<?php echo $style_captcha; ?>"/>
							</p>
						</div>
						<p style="clear: both;">
								<span style="color:#FF0000">
							<?
								/*if($dims->getHttpHost() == 'newsletters.luxembourgforbusiness.lu')
									echo 'Important : Kindly note that the Foreign Trade Newsletters will now be sent from
									Luxembourg for Business. To insure that you will still receive them in the future, please adjust your spam filter.';
								*/
								echo dims_nl2br($work->fields['newsletter_footer_registration']);
							?>
								</span>
						</p>
						<div class="save" style="clear:both;wdith:100%;text-align:right;">
							<input type="submit" value="<?php echo $_SESSION['cste']['_SUBMIT']; ?> >" class="submit" /><br />
						</div>
				</div>
<?php
echo				'</td>
				</tr>';
} //condition sur validate (pour afficher ou non le form si l'inscrioption est ok ou non)
echo		'</table>';

$tokenHTML = $token->generate();
echo $tokenHTML;

echo 	'</form></div></div>';

if (!empty($old_lang)) {
	$_SESSION['cste']=$old_lang;
	$dims->loadLanguage($old_id_lang);
}
?>
