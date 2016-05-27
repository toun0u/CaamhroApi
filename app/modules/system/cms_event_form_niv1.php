<?php
echo '<div id="descript_evt" style="font-size:16px;">';
echo '<br>'.$_DIMS['cste']['_DIMS_FRONT_TEXT_SUBSCRIPTION'].'<br>';
echo '</div>';
if (!isset($_SESSION['dims']['tmp_nb_insc'])) $_SESSION['dims']['tmp_nb_insc']=1;
$nb_form = 1;
require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
$ct = new contact();

//on recherche le type d'action
$act = new action();
$act->open($id_action);

if(!$_SESSION['dims']['connected'] ) {
	if($act->fields['typeaction'] != '_DIMS_PLANNING_FAIR') {
$nb_form = dims_load_securvalue('nb_form', dims_const::_DIMS_NUM_INPUT, false, true, false, $_SESSION['dims']['tmp_nb_insc'],$nb_form);
?>
<div>
	<form action="" method="POST" id='form_multi_inscrip'>
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("nb_form");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<p>
			<?php echo $_DIMS['cste']['_DIMS_EVT_INSCRIPT_MULTI']; ?>
			<select name="nb_form">
				<?
				for($i=1;$i<=5;$i++) {
					if ($i==$nb_form) $sel="selected";
					else $sel="";

					echo "<option $sel value=\"".$i."\">".$i."</option>";
				}
				?>
			</select>
			<input class="submit" type="submit" size="3" maxlength="3" value="go >" />
		</p>
	</form>
</div>
<?php
	}
}
else {
	$ct->open($_SESSION['dims']['user']['id_contact']);
}
?>
<div id="form_1">
	<!--h2>
		<?php
		//Formulaire niv.1 * nb_inscrip (Pour les personnes s'inscrivant a plusieurs)
		echo  $_DIMS['cste']['_DIMS_EVT_INSCRIPT'];
		global $dims;
		?>
	</h2-->
	<form action="<? echo dims_urlencode($dims->getUrlPath().'?action=save_niv1&id_event='.$id_action,false); ?>" method="POST" id='form_inscrip_niv1'>
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("nb_inscrip", $nb_form);
		?>
		<input type="hidden" name="nb_inscrip" value="<?php echo $nb_form ?>" />

		<?php
		//Verification nombre d'inscription positif
		if($nb_form < 1)
			$nb_form = 1;
		//Limitation nombre d'inscription (50 ?)
		if($nb_form > 50)
			$nb_form = 50;

		$control=false;
		for($i = 0; $i < $nb_form; $i++) {
		?>
		<div class="inscriptions">
			<div class="inscription">
				<?php
				if (!$_SESSION['dims']['connected'] && isset($_SESSION['dims']['tmpevent'])) {
					$ct = new contact();
					$ct->setvalues($_SESSION['dims']['tmpevent'],$i."_");
					$control=true;
				}
				else {
					$ct->init_description();
					$ct->fields['function']="";
					$ct->fields['company']="";
				}
				if($act->fields['typeaction'] != '_DIMS_PLANNING_FAIR') {
					echo $_DIMS['cste']['_DIMS_LABEL_CONTACT'].' '.($i+1);
				}
				else {
					echo $_DIMS['cste']['_DIMS_LABEL_COMPANY'];
				}
				?>
			</div>
			<div class="info_oblig">
				<table>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_lastname"><?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['lastname']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_lastname" id="<?php echo $i; ?>_lastname" value="<?php if(isset($ct)) echo $ct->fields['lastname']; ?>" class="content"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_firstname"><?php echo $_DIMS['cste']['_FIRSTNAME']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['firstname']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_firstname" id="<?php echo $i; ?>_firstname"	value="<?php if(isset($ct)) echo $ct->fields['firstname']; ?>" class="content"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_function"><?php echo $_DIMS['cste']['_DIMS_LABEL_FUNCTION']; ?><span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['function']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_function" id="<?php echo $i; ?>_function" class="content" value="<?php if(isset($ct)) echo $ct->fields['function']; ?>"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_company"><?php echo $_DIMS['cste']['_DIMS_LABEL_COMPANY']; ?><span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['company']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_company" id="<?php echo $i; ?>_company" class="content" value="<?php if(isset($ct)) echo $ct->fields['company']; ?>"/>
						</td>
					</tr>

					<tr>
						<td>
							<label for="<?php echo $i; ?>_email"><?php echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['email']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_email" id="<?php echo $i; ?>_email" value="<?php if(isset($ct)) echo $ct->fields['email']; ?>" class="content"/>
						</td>
					</tr>
				</table>
			</div>
			<div class="info_compl">
				<table>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_phone"><?php echo $_DIMS['cste']['_PHONE']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['phone']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_phone" id="<?php echo $i; ?>_phone" value="<?php if(isset($ct)) echo $ct->fields['phone']; ?>" class="content"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_adresse1"><?php echo $_DIMS['cste']['_DIMS_LABEL_ADDRESS']; ?><span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['address']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_address" id="<?php echo $i; ?>_address" value="<?php if(isset($ct)) echo $ct->fields['address']; ?>" class="adresse"/><br />
						</td>
					</tr>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_city"><?php echo $_DIMS['cste']['_DIMS_LABEL_CITY']; ?><span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['city']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_city" id="<?php echo $i; ?>_city" value="<?php if(isset($ct)) echo $ct->fields['city']; ?>" class="content"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_postalcode"><?php echo $_DIMS['cste']['_DIMS_LABEL_CP']; ?><span style="color:#FF0000">*</span></label>

						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['postalcode']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_postalcode" id="<?php echo $i; ?>_postalcode" value="<?php if(isset($ct)) echo $ct->fields['postalcode']; ?>" class="content"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="<?php echo $i; ?>_country"><?php echo $_DIMS['cste']['_DIMS_LABEL_COUNTRY']; ?><span style="color:#FF0000">*</span></label>

						</td>
					</tr>
					<tr>
						<td>
							<? $redcode=($ct->fields['country']=="" && isset($_SESSION['dims']['tmpevent'])) ? "style='background-color:#F7D3D3';" : ''; ?>
							<input type="text" <?php echo $redcode; ?> name="<?php echo $i; ?>_country" id="<?php echo $i; ?>_country" value="<?php if(isset($ct)) echo $ct->fields['country']; ?>" class="content"/>
						</td>
					</tr>
					<?
						$token->field("nb_inscrip");
						$token->field($i."_lastname");
						$token->field($i."_firstname");
						$token->field($i."_function");
						$token->field($i."_company");
						$token->field($i."_email");
						$token->field($i."_phone");
						$token->field($i."_address");
						$token->field($i."_city");
						$token->field($i."_postalcode");
						$token->field($i."_country");
					?>
				</table>
			</div>
			<p style="clear: both;">
				<span style="color:#FF0000">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span>
			</p>
		</div>
			<?php
			}
			?>
			<div class="captcha">
				<p>
					<?php echo $_SESSION['cste']['_DIMS_TEXT_CAPTCHA']; ?><br />
					<img src="./common/modules/system/lfb_captcha.php" /><br />
					<input type="text" name="captcha" />
				</p>
			</div>

			<div class="save">
			<?php
				//echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_SAVE'],'./common/img/save.gif','javascript: dims_getelem(\'form_inscrip_niv1\').submit();');

				if($evt->fields['timestp_open'] <= date('YmdHis')) {
					echo '<input type="submit" value="Submit >" class="submit" />';
				}
				else {
					echo $_DIMS['cste']['_DIMS_FRONT_NOT_OPEN'];
				}
			?>
		</div>
		<?
			$token->field("captcha");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
	</form>
</div>
