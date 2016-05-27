<script type="text/javascript">
	function selectAll(){
			var i = 0;
			while(dims_getelem(i) != false){
					var e = dims_getelem('ent_imp_'+i);
					e.checked = "checked";
					i++;
			}
	}

	function unselectAll(){
			var i = 0;
			while(dims_getelem(i) != false){
					var e = dims_getelem('ent_imp_'+i);
					e.checked = "";
					i++;
			}
	}
</script>
<?php
require_once(DIMS_APP_PATH . "/modules/system/class_tiers.php");
require_once(DIMS_APP_PATH . "/modules/system/class_tiers_import.php");
require_once(DIMS_APP_PATH . "/modules/system/class_contact_import.php");
require_once(DIMS_APP_PATH . "/modules/system/class_contact.php");
require_once(DIMS_APP_PATH . "/modules/system/class_tiers_contact.php");

$part = dims_load_securvalue('part', dims_const::_DIMS_CHAR_INPUT, true, false, true);
$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true,false);
if($op == false)
	$op = 1;

//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');

//on verifie si le user n'a pas de contact avec similitude a traiter
$sql_vsim = "	SELECT		   id
				FROM		   dims_mod_business_contact_import
				WHERE		   id_user_create = :iduser
				AND			   id_workspace = :idworkspace ";
$res_vsim = $db->query($sql_vsim, array(
	':iduser'		=> $_SESSION['dims']['userid'],
	':idworkspace'	=> $_SESSION['dims']['workspaceid']
));
$in = 0;

if($db->numrows($res_vsim) > 0) {
	while ($tab_in = $db->fetchrow($res_vsim)) {
		$in .= ', '.$tab_in['id'];
	}
}

//on verifie si le user n'a pas fait d'import pour quelqu'un d'autre
$sql_op = "		SELECT		   id
				FROM		   dims_mod_business_contact_import
				WHERE		   id_importer = :idimporter
				AND				id NOT IN ($in)";

$res_op = $db->query($sql_op, array(
	':idimporter' => $_SESSION['dims']['userid']
));


if(($db->numrows($res_vsim) > 0 || $db->numrows($res_op) > 0)&& ($op ==1 || $op == '')) {
	$nb_sim = $db->numrows($res_vsim);

	echo $skin->open_simplebloc('','width:auto;float:right;clear:none;','','');
	echo '	<table width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td align="right" width="20%">
						<img src="./common/img/important.png" style="border:none;"/>&nbsp;
					</td>';
	if($nb_sim > 0) {
		echo		'<td align="left" style="vertical-align:middle;">
						<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_IMPORT_OUTLOOK.'&part='._BUSINESS_TAB_IMPORT_OUTLOOK.'&op=3">'.$nb_sim.'&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_IMPORT_WITH_SIM'].'</a>
					</td>';
	}
	if($db->numrows($res_op) > 0) {
		echo '<td align="left" style="vertical-align:middle;">
						<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_IMPORT_OUTLOOK.'&part='._BUSINESS_TAB_IMPORT_OUTLOOK.'&op=3&from=other_user">Do similarities for other people</a>
					</td>';
	}
	echo		'</tr>
			</table>';
	echo $skin->close_simplebloc();
}
//echo "<div style=\"clear:both;float:left;width:100%;display:block;float:left;\">";
//echo $op;
echo $skin->open_simplebloc($_DIMS['cste']['_IMPORT_CONTACTS']." : ".$_DIMS['cste']['_LABEL_IMPORT_STEPS'],'width:680px;float:left;clear:bloth;','','');


?>
<table style="text-align:center;">
	<tr>
		<?php if($op == 1 && (!isset($_FILES['srcfilect']) || empty($_FILES['srcfilect']['name']))){
			?>
			<td>
				<span style="font-weight:bold;font-size:16px;">1</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_DOWNLOAD_FILE']; ?></span>
			</td>
			<?php
		}else{?>
			<td>
				<span style="font-weight:bold;font-size:16px;">1</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_DOWNLOAD_FILE']; ?>
			</td>
		<?php }

		if($op == 1 && isset($_FILES['srcfilect']) && !empty($_FILES['srcfilect']['name'])){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">2</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_KNOWN_CONTACTS'];?></span>
			</td>
			<?php
		}elseif($op>1){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">2</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_KNOWN_CONTACTS'];?>
			</td>
			<?php
		}else{
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">2</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_KNOWN_CONTACTS'];?>
			</td>
			<?php
		}

		if($op == 2){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_CONTACTS_WITH_SIMILAR_PROFIL'];?></span>
			</td>
			<?php
		}elseif($op > 3){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_CONTACTS_WITH_SIMILAR_PROFIL'];?>
			</td>
			<?php
		}else{
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_CONTACTS_WITH_SIMILAR_PROFIL'];?>
			</td>
			<?php
		}

		if($op == 4){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?></span>
			</td>
			<?php
		}elseif($op > 4){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?>
			</td>
			<?php
		}else{
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?>
			</td>
			<?php
		}

		if($op == 5){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">5</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_SUMMARY'];?></span>
			</td>
			<?php
		}else{
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">5</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_SUMMARY'];?>
			</td>
			<?php
		}
		?>
	</tr>
</table>
<?php



echo $skin->close_simplebloc();
//echo	"</div>";
if($op != "")
	$step = "STEP".$op;
else
	$step = "NOSTEP";


echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_IMPORT_'.$step],'width:100%;clear:both;','','');
require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_outlook_switch.php');
echo $content_contact_import;

echo $skin->close_simplebloc();

?>
