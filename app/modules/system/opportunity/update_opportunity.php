<?php
$sel = "SELECT  *
        FROM    dims_matrix
        WHERE   id_contact > 0
        AND     id_opportunity > 0";
$db = dims::getInstance()->db;
$res = $db->query($sel);
$lstContacts = $lstUpdate = array();
while ($r = $db->fetchrow($res)){
	if(!isset($lstContacts[$r['id_contact']])){
		$lstContacts[$r['id_contact']] = new contact();
		$lstContacts[$r['id_contact']]->openWithGB($r['id_contact']);
	}

	if (!isset($lstUpdate[$r['id_opportunity']]))
		$lstUpdate[$r['id_opportunity']] = $r;
	$lstUpdate[$r['id_opportunity']]['lstContact'][] = $r['id_contact'];
	if ($r['id_contact2'] > 0)
		$lstUpdate[$r['id_opportunity']]['already'][$r['id_contact']] = $r['id_contact2'];
}

require_once DIMS_APP_PATH."modules/system/class_matrix.php";
$tmstp = dims_createtimestamp();
$nbUpdate = 0;
foreach($lstUpdate as $data){
	foreach($data['lstContact'] as $ct){
		foreach($data['lstContact'] as $ct2){
			if ($ct != $ct2 && $lstContacts[$ct]->hasAccount() && ((!isset($data['already'][$ct])) || (isset($data['already'][$ct]) && $data['already'][$ct] != $ct2))){
				$matrice = new matrix();
				$matrice->fields['id_contact'] = $ct;
				$matrice->fields['id_contact2'] = $ct2;
				$matrice->fields['id_country'] = $data['id_country'];
				$matrice->fields['year'] = $data['year'];
				$matrice->fields['month'] = $data['month'];
				$matrice->fields['id_opportunity'] = $data['id_opportunity'];
				$matrice->fields['timestp_modify'] = dims_createtimestamp();
				$matrice->fields['id_workspace'] = $data['id_workspace'];
				$matrice->fields['id_action'] = $data['id_action'];
				$matrice->save();
				$nbUpdate++;
			}
		}
	}
}
echo "Données mise à jour : $nbUpdate";
?>