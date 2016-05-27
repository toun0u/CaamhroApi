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

//echo $op;
//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
ini_set('max_execution_time',-1);
ini_set('memory_limit','512M');

//on verifie si le user n'a pas d'entreprises avec similitude a traiter
$sql_vsim = "	SELECT		   id
				FROM		   dims_mod_business_tiers_import
				WHERE		   id_user = :iduser
				AND			   id_workspace = :idworkspace ";
$res_vsim = $db->query($sql_vsim, array(
	':iduser' 		=> $_SESSION['dims']['userid'],
	':idworkspace' 	=> $_SESSION['dims']['workspaceid']
));
if($db->numrows($res_vsim) > 0 && ($op ==1 || $op == '')) {
	$nb_sim = $db->numrows($res_vsim);
	echo $skin->open_simplebloc('','width:50%;float:left;clear:none;','','');
	echo '	<table width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td align="right" width="20%">
						<img src="./common/img/important.png" style="border:none;"/>&nbsp;
					</td>
					<td align="left" style="vertical-align:middle;">
						<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_IMPORT_ENTREPRISES.'&part='._BUSINESS_TAB_IMPORT_ENTREPRISES.'&op=3">'.$nb_sim.'&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_IMPORT_WITH_SIM'].'</a>
					</td>
				</tr>
			</table>';
	echo $skin->close_simplebloc();
}

require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_entreprises_switch.php');

echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_IMPORT']." ".strtolower($_DIMS['cste']['_DIMS_LABEL_GROUP_LIST'])." : ".$_DIMS['cste']['_LABEL_IMPORT_STEPS'],'width:680px;float:left;clear:none;','','');
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
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_KNOWN_TIER'];?></span>
			</td>
			<?php
		}elseif($op>1){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">2</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_KNOWN_TIER'];?>
			</td>
			<?php
		}else{
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">2</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_KNOWN_TIER'];?>
			</td>
			<?php
		}

		if($op == 2){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_TIER_WITH_SIMILAR_PROFIL'];?></span>
			</td>
			<?php
		}elseif($op > 3){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_TIER_WITH_SIMILAR_PROFIL'];?>
			</td>
			<?php
		}else{
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_TIER_WITH_SIMILAR_PROFIL'];?>
			</td>
			<?php
		}

		if($op == 4){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_TIER'];?></span>
			</td>
			<?php
		}elseif($op > 4){
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_TIER'];?>
			</td>
			<?php
		}else{
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_TIER'];?>
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

if($op != "")
	$step = "STEP".$op;
else
	$step = "NOSTEP";


echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_IMPORT_ENTREPRISE_'.$step],'width:100%;float:left;clear:none;','','');

echo $content_contact_import;

echo $skin->close_simplebloc();
?>
