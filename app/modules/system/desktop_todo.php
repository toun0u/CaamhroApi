<?
if (!isset($_SESSION['dims']['desktop_view_todo'])) $_SESSION['dims']['desktop_view_todo']=0;
$desktop_view_todo=dims_load_securvalue('desktop_view_todo',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_view_todo'],0);
if (isset($_GET['desktop_view_todo'])) {
	dims_redirect('/admin.php');
}
$sizeofw=sizeof($dims->getWorkspaces());
?>
<div style="position:relative;display:block;">
			   <div class="rfloat fwb action" style="width:320px;">
				<?
				$arrayChoice = array();
				$arrayChoice[0]=$_DIMS['cste']['_DIMS_LABEL_TOP_NEWS'];
				$arrayChoice[1]=$_DIMS['cste']['_DIMS_ALL_WORKSPACES'];
				$arrayChoice[2]=$_DIMS['cste']['_FORM_TASK_TIME_TODO'];

				$taille=sizeof($arrayChoice);
				foreach($arrayChoice as $i=>$val) {
					if (($sizeofw>1 && $i==1) || $i!=1) {
						if ($i==$desktop_view_type) $sel=true;
						else $sel=false;

						if ($sel) echo $val;
						else {
							echo '<a href="/admin.php?desktop_view_type='.$i.'">'.$val.'</a>';
						}

						if ($i<$taille-1) {
							echo "&nbsp;/&nbsp;";
						}
					}
				}
				?>
			</div>
			<h2 style="padding-left:22px">
					<img class="icons fil_actu">
					<? echo $_DIMS['cste']['_FORM_TASK_TIME_TODO']; ?>
			</h2>
			   <div class="fwb action" style="width:320px;float:left;">
				<?
				echo "<img src=\"./common/img/arrow_ltr.png\"><font style=\"font-style:normal;\">".$_DIMS['cste']['_FORMS_FILTER']." : </font>";
				$arrayChoice = array();
				$arrayChoice[0]=$_DIMS['cste']['_DIMS_ALL'];
				$arrayChoice[1]=$_DIMS['cste']['_PERSO'];
				$arrayChoice[2]=$_DIMS['cste']['_USERS'];
				$arrayChoice[3]=$_DIMS['cste']['_DIMS_LABEL_SYSTEM'];

				$taille=sizeof($arrayChoice);
				foreach($arrayChoice as $i=>$val) {
					if ($i==$desktop_view_todo) $sel=true;
					else $sel=false;

					if ($sel) echo $val;
					else {
						echo '<a href="/admin.php?desktop_view_todo='.$i.'">'.$val.'</a>';
					}

					if ($i<$taille-1) {
						echo "&nbsp;/&nbsp;";
					}
				}
				?>
			</div>
			<div style="float:right;width:120px;">
				<?
				echo  dims_create_button($_DIMS['cste']['_DIMS_ADD'],"./common/img/add.gif","displayAddTodo(event,0,0,0);","","");
				?>
			</div>
</div>
<?php
require_once(DIMS_APP_PATH . '/modules/system/class_module_type.php');

$todo		= new todo($db, $_SESSION["dims"]["userid"]);
$user		= new user($db);

// Affichage des taches
$todolist	= $todo->getTasks(0, false, false, false, true);
$modstemp = array();
$ch=array();
$ch[0]='selected="selected"';
$ch[1]='';
$ch[2]='';

//echo '<table width="99%">';

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
	echo '<div class="listItemContent" style="clear:both;float:left;width:100%;"><ul>';
		$style="";
	foreach ($todolist['result'] as $k=> $value) {
			if ($k==0) {
					$style="ptb listItem firstElem";
			}
			else {
					$style="ptb listItem listItemBorder listItemColor";
			}

			// Recuperation des infos des destinataires
			$pour			= '';
			$todo->fields	= array('id'		=> $value['id'],
															'id_parent' => $value['id'],
															'user_to'	=> $value['user_to']);
			$c=0;
			if (isset($todo_users[$value['id']])) {
					foreach ($todo_users[$value['id']] as $valueD) {
							if ($c>0) $pour.= "<br>";
							$pour.= $tabusers[$valueD];
							$c++;
					}
			}

			unset($todo->fields);
			//$ldate_modify =  dims_datetime2local($value['date']);
			$object_script = '';
			$label='';
			$link="";
			?>
			<li class="<? echo $style; ?>">
				<div style='display:block;'>
					<a class="listItemImageLink">
						<?
						$filephoto = 'photo_cts/contact_'.$value['id_contact'].'/photo60'.$value['photo'].'.png';
						if (file_exists(DIMS_WEB_PATH."data/".$filephoto)) {
							$srcimg=_DIMS_WEBPATHDATA.$filephoto;
						}
						else {
							$srcimg="./common/img/contact.gif";
						}
						?>
						<img class="listItemImage" src="<? echo $srcimg;?>">
					</a>
					<div class="listItemContent">
						<h6 class="listItemMessage"><?
						switch ($value['type']) {
							case 0:
								echo $_DIMS['cste']['_PERSO'];
								break;
							case 1:
								echo $_DIMS['cste']['_DIMS_LABEL_FROM']." ".ucfirst($value['from_firstname'])." ";
								echo $_DIMS['cste']['_DIMS_LABEL_DESTS']." ".$pour;
								break;
							case 2:
								// system
								break;
						}
						?></h6>
						<span class="listItemSource" style="clear:both;width:100%;">
							<?
							echo $value['content'];

							?>
						</span>
						<span class="listItemSource" style="clear:both;width:100%;">
							<?
							echo dims_nicetime($value['date']);
							echo "&nbsp;&nbsp;";
							if ($value['type']==2) {
								echo $link."<img src=\"".$_SESSION['dims']['template_path']."./common/img/system/link.png\">&nbsp;".$label."</a>";
							}
							else {
								if ($value['id_object']>0 && $value['id_record']>0 && $value['id_module']>0) {
									$extimg=$dims->getImageByObject($value['id_module_type'],$value['id_object']);

									echo "<a href=\"javascript:void(0);\" onclick=\"javascript:viewPropertiesObject(".$value['id_object'].",".$value['id_record'].",".$value['id_module'].",1);\">
									<img src='".$extimg."'>&nbsp;<img src=\"".$_SESSION['dims']['template_path']."./common/img/system/link.png\">&nbsp;".$label."</a>";
								}

								if ($value['state']==0) {
										// ajout du bouton de validation
										$valid ="admin.php?dims_op=checkTag&id_todo=".$value['id']."&check=1";
										$delete = "admin.php?dims_op=checkTag&id_todo=".$value['id']."&&check=2";
										echo "<a href=\"javascript:dims_confirmlink('".$valid."','".$_DIMS['cste']['_DIMS_CONFIRM']."')\"><img border=\"0\" src=\"./common/img/check.png\"></a>";
										echo "&nbsp;&nbsp;";
										echo "<a href=\"javascript:dims_confirmlink('".$delete."','".$_DIMS['cste']['_DIMS_CONFIRM']."')\"><img src='./common/img/annul.gif' style='width:14px' border='0'></a>";
								}
								else {
										if ($value['state']==1) {
												echo "<img src='./common/img/publish.png' border='0'>";
										}
										elseif($value['state']==2) {
												echo "<img src='./common/img/close.png' border='0'>";
										}
								}
							}
							?>
						</span>
					</div>
				</div>
			</li>
			<?
			/*echo '<tr>';
			echo '<td align="center">'.$priority[$value['priority']].'</td>';
			echo '<td align="center">'.$type[$value['type']].'</td>';
			echo '<td align="center">'.$ldate_modify['date']." ".$ldate_modify['time'].'</td>';
			if ($value['type']==2) {
					$link="<a href=\"/admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&cat=0&action=700&part=700&op=3\">";
					echo '<td align="center">'.$link.$value['content'].'</a></td>';
			}
			else {
					echo '<td align="center">'.$value['content'].'</td>';
			}
			echo '<td align="center">'.ucfirst($value['from_firstname']).'</td>';
			echo '<td align="center">'.$pour.'</td>';

			// on regarde si il y a un objet ou non
			if ($value['type']==2) {
					echo '<td align="center" colspan="2">';
					echo $link."<img src=\"".$_SESSION['dims']['template_path']."./common/img/system/link.png\">&nbsp;".$label."</a>";
					echo '</td></tr>';
			}
			else {
					echo '<td align="center">';
							if ($value['id_object']>0 && $value['id_record']>0 && $value['id_module']>0) {

							$extimg=$dims->getImageByObject($value['id_module_type'],$value['id_object']);

							echo "<a href=\"javascript:void(0);\" onclick=\"javascript:viewPropertiesObject(".$value['id_object'].",".$value['id_record'].",".$value['id_module'].",1);\">
							<img src='".$extimg."'>&nbsp;<img src=\"".$_SESSION['dims']['template_path']."./common/img/system/link.png\">&nbsp;".$label."</a>";
					}
					echo '</td><td>';

					if ($value['state']==0) {
							// ajout du bouton de validation
							$valid ="admin.php?dims_op=checkTag&id_todo=".$value['id']."&check=1";
							$delete = "admin.php?dims_op=checkTag&id_todo=".$value['id']."&&check=2";
							echo "<a href=\"javascript:dims_confirmlink('".$valid."','".$_DIMS['cste']['_DIMS_CONFIRM']."')\"><img border=\"0\" src=\"./common/img/check.png\"></a>";
							echo "<a href=\"javascript:dims_confirmlink('".$delete."','".$_DIMS['cste']['_DIMS_CONFIRM']."')\"><img src='./common/img/annul.gif' style='width:14px' border='0'></a>";
					}
					else {
							if ($value['state']==1) {
									echo "<img src='./common/img/publish.png' border='0'>";
							}
							elseif($value['state']==2) {
									echo "<img src='./common/img/close.png' border='0'>";
							}
					}
					echo '</td></tr>';
			}
			*/
	}
		echo '</ul></div>';
//	echo '</tbody></table>';
}
?>
