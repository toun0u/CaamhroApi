<?php
require_once(DIMS_APP_PATH . '/modules/system/class_module_type.php');

$todo		= new todo($db, $_SESSION["dims"]["userid"]);
$user		= new user($db);

// Affichage des taches
$todolist	= $todo->getTasks(0, true);
$modstemp = array();
$ch=array();
$ch[0]='selected="selected"';
$ch[1]='';
$ch[2]='';

	echo '<table width="100%"><tr><td colspan="4">'.$_DIMS['cste']['_TYPE'].'

			<select id="todo_type" name="todo_type" tabindex="1">
			<option value="-1" '.$ch[0].'>'.$_DIMS['cste']['_DIMS_ALL'].'</option>
			<option value="0" '.$ch[1].'>'.$_DIMS['cste']['_PERSO'].'</option>
			<option value="1" '.$ch[2].'>'.$_DIMS['cste']['_USERS'].'</option>';
	echo '			</select>
			</td>
			<td colspan="4" style="text-align:right;">';
		echo  dims_create_button($_DIMS['cste']['_DIMS_ADD'],"./common/img/add.gif","displayAddTodo(event,0,0,0);","","");
	echo '</td></tr>';

$priority=array();
$priority[0]=$_DIMS['cste']['_DIMS_LOW'];
$priority[1]=$_DIMS['cste']['_DIMS_LABEL_CONT_VIP_N'];
$priority[2]=$_DIMS['cste']['_DIMS_HIGH'];

$type=array();
$type[0]=$_DIMS['cste']['_PERSO'];
$type[1]=$_DIMS['cste']['_USERS'];
$type[2]=$_DIMS['cste']['_DIMS_LABEL_SYSTEM'];

// construction des personnes
$lsttodo='';
foreach ($todolist['result'] as $value) {
	if ($lsttodo!='') {
		$lsttodo.=",".$value['id'];
	}
	else {
		$lsttodo.=$value['id'];
	}
}
if ($lsttodo=="") $lsttodo=0;

$tabusers=array();
$todo_users=array();

$params = array();
$res=$db->query("SELECT distinct td.id_todo,u.id,firstname,lastname
				FROM dims_user as u
				INNER JOIN dims_todo_dest as td
				ON td.id_record=u.id
				AND td.id_todo IN (".$db->getParamsFromArray($lsttodo, 'lsttodo', $params).")", $params);
while ($fu = $db->fetchrow($res)) {
	if (!isset($tabusers[$fu['id']])) {
		$tabusers[$fu['id']]= $fu['firstname']." ".$fu['lastname'];
	}

	// association to
	if (!isset($todo_users[$fu['id_todo']])) {
		$todo_users[$fu['id_todo']]=array();
	}

	$todo_users[$fu['id_todo']][$fu['id']]=$fu['id'];
}

if ($todolist['count'] > 0) {
	echo '
		<tr>
		<td>'.$_DIMS['cste']['_FORM_TASK_PRIORITY'].'</td>
		<td>'.$_DIMS['cste']['_TYPE'].'</td>
		<td>Date</td>
		<td>'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</td>
		<td>'.$_DIMS['cste']['_SENDER'].'</td>
		<td>'.$_DIMS['cste']['_DIMS_DEST'].'</td>
		<td>'.$_DIMS['cste']['_DIMS_TASK'].'</td><td>'.$_DIMS['cste']['_INFOS_STATE'].'</td></tr></tdead><tbody>';

	foreach ($todolist['result'] as $value) {
		// Recuperation des infos des destinataires
		$pour			= '';
		$todo->fields	= array('id'		=> $value['id'],
								'id_parent' => $value['id'],
								'user_to'	=> $value['user_to']);

		/*if ($todo->fields['user_to'] == '' || $todo->fields['user_to'] == null) {
			$pour	= 'Tous';
		}
		else {*/
		/*
		$destinataires	= $todo->getDestinataires();

			if ($destinataires['count'] > 0) {
				foreach ($destinataires['result'] as $valueD) {
					$user->open($valueD);
					$pour .= (($pour == '')?'':', ').ucfirst($user->fields['firstname']);
					unset($user->fields);
				}
			} else {
				$pour	= 'Introuvable';
			}*/
		$c=0;
		if (isset($todo_users[$value['id']])) {
			foreach ($todo_users[$value['id']] as $valueD) {
				if ($c>0) $pour.= "<br>";
				$pour.= $tabusers[$valueD];
				$c++;
			}
		}

		//}
		unset($todo->fields);
		$ldate_modify =  dims_datetime2local($value['date']);
		$object_script = '';
		$label='';

		echo '<tr>';
		echo '<td align="center">';

		if ($value['priority']==2) {
			echo "<img src='./common/img/important_small.png' border='0'>";
		}
		else {
			echo $priority[$value['priority']];
		}
		echo '</td>';
		echo '<td align="center">'.$type[$value['type']].'</td>';
		echo '<td align="center">'.$ldate_modify['date']." ".$ldate_modify['time'].'</td>';
		echo '<td align="center">'.$value['content'].'</td>';
		echo '<td align="center">'.ucfirst($value['from_firstname']).'</td>';
		echo '<td align="center">'.$pour.'</td>';
		echo '<td align="center">';
		// on regarde si il y a un objet ou non
		if ($value['id_object']>0 && $value['id_record']>0 && $value['id_module']>0) {

			$extimg=$dims->getImageByObject($value['id_module_type'],$value['id_object']);

			echo "<a href=\"javascript:void(0);\" onclick=\"javascript:viewPropertiesObject(".$value['id_object'].",".$value['id_record'].",".$value['id_module'].",1);\">
			<img src='".$extimg."'>&nbsp;<img src=\"".$_SESSION['dims']['template_path']."./common/img/system/link.png\">&nbsp;".$label."</a>";
		}
		echo '</td><td>';

		// ajout du bouton de validation
		if ($value['state']==1) {
			echo "<img src='./common/img/publish.png' border='0'>";
		}
		elseif($value['state']==2) {
			echo "<img src='./common/img/close.png' border='0'>";
		}
		else {
			echo "<img src='./common/img/date.gif' border='0'>";
		}
		echo '</td></tr>';
	}

	echo '</tbody></table>';
}
?>
