<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// tableau d'emails

if (isset($_SESSION['dims']['search']['result'][1][dims_const::_SYSTEM_OBJECT_CONTACT])) {

	$params = array();
	$sql ="select * from dims_mod_business_contact where id in (".
								$db->getParamsFromArray($_SESSION['dims']['search']['result'][1][dims_const::_SYSTEM_OBJECT_CONTACT], 'contact', $params)
																.")";

	$tabmails=array();
	$tablayermails=array();
	$taglist='';
	foreach ($dimsearch->expression as $k=>$elemword) {
		if (isset($dimsearch->tabfiltre[$k]['type']) && !empty($dimsearch->tabfiltre[$k]['type']) && $dimsearch->tabfiltre[$k]['type']==2) {
			if ($taglist!='') $taglist.=' ';

			$taglist.=$dimsearch->expression[$k]['word'];
		}
	}
	if (isset($sql)) {
		$rs = $db->query($sql, $params);

		while($fields = $db->fetchrow($rs)) {
			$tab_ct[$fields['id']] = $fields;
			$ismail=false;

			if (isset($fields['email']) && !$ismail && $fields['email']!='') {
				if (!isset($tabmails[$fields['email']])) {
					$tabmails[$fields['email']]=$fields['email'];
					$ismail=true;
				}
			}

			if (isset($fields['email2']) && !$ismail && $fields['email2']!='') {
				if (!isset($tabmails[$fields['email2']])) {
					$tabmails[$fields['email2']]=$fields['email2'];
					$ismail=true;
				}
			}

			if (isset($fields['email3']) && !$ismail && $fields['email3']!='') {
				if (!isset($tabmails[$fields['email3']])) {
					$tabmails[$fields['email3']]=$fields['email3'];
					$ismail=true;
				}
			}
			if ($ismail==false) {
				// on ajoute pour regarder dans les layers
				$tablayermails[$fields['id']]=$fields['id'];
			}
		}

		//on va chercher les champs métier dans les layers
		if (isset($tablayermails) && !empty($tablayermails)) {
			$params = array();
			$sql_l = 	"SELECT 	*
						FROM 		dims_mod_business_contact_layer
						WHERE 		id in (".$db->getParamsFromArray($tablayermails, 'id', $params).")
						AND 		type_layer = 1
						AND 		id_layer = {$_SESSION['dims']['workspaceid']}";
			//$blocklink.=$sql_l;
			$res_l = $db->query($sql_l, $params);
			if($db->numrows($res_l) > 0) {
				while($lay = $db->fetchrow($res_l)) {

					if($lay['email'] != '' && !$ismail) {
						$tabmails[$lay['email']]=$lay['email'];
						$tab_ct[$fields['id']]['email'] = $lay['email'];
					}
					if($lay['email2'] != '' && !$ismail) {
						$tabmails[$lay['email2']]=$lay['email2'];
						$tab_ct[$fields['id']]['email2'] = $lay['email2'];
					}
					if($lay['email3'] != '' && !$ismail) {
						$tabmails[$lay['email3']]=$lay['email3'];
						$tab_ct[$fields['id']]['email3'] = $lay['email3'];
					}

				}
			}
		}

		// construction de l'email
		$link='';
		foreach ($tabmails as  $mail) {
			if ($link=='') {
				$link='mailto:?subject='.$taglist.'&bcc='.$mail;
			}
			else {
				$link.=";".$mail;
			}
		}
		$blocklink.='
		<a href="javascript:void(0);" onclick="javascript:document.location.href=\''.$link.'\'"><img src="./common/img/mailsend.png"></a>';

	}
}
?>
