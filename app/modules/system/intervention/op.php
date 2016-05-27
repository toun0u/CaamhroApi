<?php
$intervention_op = dims_load_securvalue('intervention_op', dims_const::_DIMS_NUM_INPUT, true, true);
require_once(DIMS_APP_PATH.'modules/system/intervention/global.php');

switch($intervention_op){
	case dims_const_interv::_OP_EDIT_INTERVENTION:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$interv = new dims_intervention();
		if (!($id != '' && $id > 0 && $interv->open($id))){
			$interv->init_description();
			$interv->fields['id_type_intervention'] = $type;
		}
		if (isset($_SESSION['intervention']['templates']['popup_edit']) && file_exists($_SESSION['intervention']['templates']['popup_edit']))
			$interv->display($_SESSION['intervention']['templates']['popup_edit']);
		else
			$interv->display(DIMS_APP_PATH."modules/system/intervention/template/display_intervention_popup.tpl.php");
		break;
	case dims_const_interv::_OP_LST_INTERVENTIONS:
		$db = dims::getInstance()->db;
		$gb = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$sTable = 	dims_intervention::TABLE_NAME." i";

		$aColumns = array( 'i.tmstp_realized tmstp_realized', 'CONCAT(u.firstname," ",u.lastname) user', 'COALESCE(ct2.intitule, CONCAT(ct1.firstname," ",ct1.lastname)) contact', 'ty.php_value php_value', 'i.comment comment', ' ' );


		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "i.id";

		/*
		 * Paging
		 */
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".mysql_real_escape_string( dims_load_securvalue('iDisplayStart',dims_const::_DIMS_NUM_INPUT,true,true,true) ).", ".
				mysql_real_escape_string( dims_load_securvalue('iDisplayLength',dims_const::_DIMS_NUM_INPUT,true,true,true) );
		}

		/*
		 * Ordering
		 */
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( dims_load_securvalue('iSortingCols',dims_const::_DIMS_NUM_INPUT,true,true,true) ) ; $i++ )
			{
				if ( dims_load_securvalue('bSortable_'.dims_load_securvalue('iSortCol_'.$i,dims_const::_DIMS_NUM_INPUT,true,true,true),dims_const::_DIMS_CHAR_INPUT,true,true,true) == "true" )
				{
					$name = explode(' ',$aColumns[intval( dims_load_securvalue('iSortCol_'.$i,dims_const::_DIMS_NUM_INPUT,true,true,true))]);
					$sOrder .= $name[count($name)-1]."
						".mysql_real_escape_string( dims_load_securvalue('sSortDir_'.$i,dims_const::_DIMS_CHAR_INPUT,true,true,true) ) .", ";
				}
			}

			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}

		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$params = array();
		$sWhere = "	LEFT OUTER JOIN dims_mod_business_contact ct1
					ON 				ct1.id_globalobject = i.id_globalobject_ref

					LEFT OUTER JOIN dims_mod_business_tiers ct2
					ON 				ct2.id_globalobject = i.id_globalobject_ref

					INNER JOIN		dims_user u
					ON				u.id = i.id_user

					INNER JOIN		".dims_intervention_type::TABLE_NAME." ty
					ON				ty.id = i.id_type_intervention

					INNER JOIN  	dims_constant const
					ON				const.phpvalue = ty.php_value

					WHERE 			i.id_globalobject_ref = :gb ";
		$params[':gb'] = $gb;
		$search = dims_load_securvalue('sSearch',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if ( $_GET['sSearch'] != "" ){
			$sWhere .= "AND (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' ){
					$name = explode(' ',$aColumns[$i]);
					unset($name[count($name)-1]);
					$sWhere .= implode(' ',$name)." LIKE :search OR ";
				}
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= " OR const.value LIKE :search ) ";
		}
		$params[':search'] = "%".$search."%";

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS $sIndexColumn, ".str_replace(",  ", " ", implode(", ", $aColumns)).", ty.icon_1, i.inout
			FROM   $sTable
			$sWhere
			GROUP BY	i.id
			$sOrder
			$sLimit
		";
		$rResult = $db->query($sQuery, $params);

		/* Data set length after filtering */
		$sQuery = "
			SELECT FOUND_ROWS() as nb
		";
		$rResultFilterTotal = $db->query($sQuery);
		$aResultFilterTotal = $db->fetchrow($rResultFilterTotal);
		$iFilteredTotal = $aResultFilterTotal['nb'];

		/* Total data set length */
		$sQuery = "
			SELECT 			COUNT(".$sIndexColumn.") as nb
			FROM   			$sTable
			LEFT OUTER JOIN dims_mod_business_contact ct1
			ON 				ct1.id_globalobject = i.id_globalobject_ref

			LEFT OUTER JOIN dims_mod_business_tiers ct2
			ON 				ct2.id_globalobject = i.id_globalobject_ref

			INNER JOIN		dims_user u
			ON				u.id = i.id_user

			INNER JOIN		".dims_intervention_type::TABLE_NAME." ty
			ON				ty.id = i.id_type_intervention
			INNER JOIN  	dims_constant const
			ON				const.phpvalue = ty.php_value
			WHERE 			i.id_globalobject_ref = :gb
		";
		$rResultTotal = $db->query($sQuery, array(
			':gb' => $gb
		));
		$aResultTotal = $db->fetchrow($rResultTotal);
		$iTotal = $aResultTotal['nb'];
//echo $sQuery;
		/*
		 * Output
		 */
		$output = array(
			"sEcho" => intval(dims_load_securvalue('sEcho',dims_const::_DIMS_NUM_INPUT,true,true,true)),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array(),
			"aaSelected" => -1
		);

		while ( $aRow = $db->fetchrow( $rResult ) ){
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ ){
				if ( $aColumns[$i] != ' ' ){
					$name = explode(' ',$aColumns[$i]);
					switch($name[count($name)-1]){
						case 'tmstp_realized':
							if ($aRow[ $name[count($name)-1] ] == 0)
								$row[] = '-';
							else{
								$d = dims_timestamp2local($aRow[ $name[count($name)-1] ]);
								$row[] = $d['date'];
							}
							break;
						case 'php_value':
							switch($aRow['inout']){
								default:
								case dims_intervention::_INTERVENTION_IN:
									$labelType = '&nbsp;('.$_SESSION['cste']['_TYPE_IN'].')';
									break;
								case dims_intervention::_INTERVENTION_OUT:
									$labelType = '&nbsp;('.$_SESSION['cste']['_TYPE_OUT'].')';
									break;
							}
							if ($aRow['icon_1'] != '')
								$row[] = '<img src="'.$aRow['icon_1'].'" />&nbsp;'.$_SESSION['cste'][$aRow[ $name[1] ]].$labelType;
							else
								$row[] = $_SESSION['cste'][$aRow[ $name[count($name)-1] ]].$labelType;
							break;
						default:
							$row[] = trim($aRow[ $name[count($name)-1] ]);
							break;
					}
				}else{
					$row[] = '<img onclick="javascript:dims_confirmlink(\'?dims_op=intervention&intervention_op='.dims_const_interv::_OP_DELETE_INTERVENTION.'&id='.$aRow['id'].'\',\''.$_SESSION['cste']['_DIMS_LABEL_CONFIRM_ACTION'].'\');" src="./common/img/delete.gif" style="cursor:pointer;" />';
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
		break;
	case dims_const_interv::_OP_DELETE_INTERVENTION:
		$id_interv = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);

		$interv = new dims_intervention();
		if ($id_interv != '' && $id_interv > 0){
			$interv->open($id_interv);
			$interv->delete();
		}

		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case dims_const_interv::_OP_SAVE_INTERVENTION:
		require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
		$inter = new dims_intervention();
		$id_int = dims_load_securvalue('id_int', dims_const::_DIMS_NUM_INPUT, true, true);
		if ($id_int > 0 && $id_int != ''){
			$inter->open($id_int);
		}else{
			$inter->init_description();
			$inter->fields['tmstp_realized'] = dims_createtimestamp();
			$inter->fields['id_user'] = $_SESSION['dims']['userid'];
		}
		$inter->setvalues($_POST,'interv_');

		if(!empty($_FILES['intervention_file']) && !$_FILES['intervention_file']['error']) {
			$docfile = new docfile();
			if ($inter->fields['id_globalobject'] > 0 && $inter->fields['id_globalobject']){
				$docfile->openWithGB($inter->fields['id_globalobject']);
				$docfile->fields['version'] ++;
			}else{
				$docfile->setugm();
                                $docfile->init_description();
				$docfile->setvalues($_POST,'docfile_');
				$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$docfile->fields['id_folder'] = -1;
				$docfile->fields['version'] = 0;
			}
			$docfile->fields['timestp_modify'] = dims_createtimestamp();
			$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
			$docfile->tmpuploadedfile = $_FILES['intervention_file']['tmp_name'];
			$docfile->fields['name'] = $_FILES['intervention_file']['name'];
			$docfile->fields['size'] = filesize($docfile->tmpuploadedfile);
			$error = $docfile->save();

			$inter->fields['id_globalobject'] = $docfile->fields['id_globalobject'];
		}

		$inter->save();

		dims_intervention_counter::addIntervention($inter->fields['id_type'], $inter->fields['id_globalobject_ref']);
		dims_redirect($dims->getScriptEnv());
		break;
}
?>
