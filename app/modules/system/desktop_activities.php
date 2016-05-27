<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!isset($_SESSION['dims']['desktop_filter_type'])) $_SESSION['dims']['desktop_filter_type']=0;
$desktop_filter_type=dims_load_securvalue('desktop_filter_type',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_filter_type'],0);
if (isset($_GET['desktop_filter_type'])) {
	unset($_SESSION['dims']['desktop_sublink']);

	if (!isset($_SESSION['dims']['desktop_sublink'])) $_SESSION['dims']['desktop_sublink']=0;
	$desktop_sublink=dims_load_securvalue('desktop_sublink',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_sublink'],0);

	dims_redirect('/admin.php');
}

if (!isset($_SESSION['dims']['desktop_tag_type'])) $_SESSION['dims']['desktop_tag_type']=1;
$desktop_tag_type=dims_load_securvalue('desktop_tag_type',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_tag_type']);
if (isset($_GET['desktop_tag_type'])) {
	dims_redirect('/admin.php');
}

if (!isset($_SESSION['dims']['desktop_filter_tag_categ'])) $_SESSION['dims']['desktop_filter_tag_categ']=0;
$desktop_filter_tag_categ=dims_load_securvalue('desktop_filter_tag_categ',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_filter_tag_categ'],0);

if (!isset($_SESSION['dims']['desktop_more_actions'])) $_SESSION['dims']['desktop_more_actions']=array();

$desktopMoreAction=dims_load_securvalue('desktopMoreAction',dims_const::_DIMS_CHAR_INPUT,true,true);
if ($desktopMoreAction!='') {
	if (isset($_SESSION['dims']['desktop_more_actions'][$desktopMoreAction])) {
		unset($_SESSION['dims']['desktop_more_actions'][$desktopMoreAction]); // on ne veut plus voir
		dims_redirect('/admin.php');
	}
	else {
		$_SESSION['dims']['desktop_more_actions'][$desktopMoreAction]=1; // on veut voir le detail
		dims_redirect('/admin.php');
	}
}
// init de la session pour gerer la date, 15 jours, 1 mois, 3 mois
if (!isset($_SESSION['dims']['desktop_view_date'])) {
	$_SESSION['dims']['desktop_view_date']=1;
}

$desktop_view_date=dims_load_securvalue('desktop_view_date',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_view_date']);

$reset_user=dims_load_securvalue('reset_user',dims_const::_DIMS_NUM_INPUT,true,true);

if ($reset_user || !isset($_SESSION['dims']['view_user_id'])) {
	$_SESSION['dims']['view_user_id']=0;
	unset($_SESSION['dims']['current_object']['id_record']);
	unset($_SESSION['dims']['current_object']['id_object']);
	unset($_SESSION['dims']['current_object']['id_module']);

	if ($reset_user) {
	 dims_redirect('/admin.php');
	}
}

$view_user_id=dims_load_securvalue('view_user_id',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['view_user_id'],0);
$usrcur= new user();

if (!isset($_SESSION['dims']['desktop_tag_filter'])) {
	$_SESSION['dims']['desktop_tag_filter']=array();
}

if (!isset($_SESSION['dims']['desktop_view_type'])) $_SESSION['dims']['desktop_view_type']=0;
$desktop_view_type=dims_load_securvalue('desktop_view_type',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_view_type'],0);
if (isset($_GET['desktop_view_type'])) {
	dims_redirect('/admin.php');
}

if (!isset($_SESSION['dims']['desktop_sublink'])) $_SESSION['dims']['desktop_sublink']=0;
$desktop_sublink=dims_load_securvalue('desktop_sublink',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_sublink'],0);
if (isset($_GET['desktop_sublink'])) {
	dims_redirect('/admin.php');
}

if ($desktop_view_type==2) {
	require_once(DIMS_APP_PATH . '/modules/system/desktop_todo.php');
}
else {
	$reset_tag=dims_load_securvalue('reset_tag',dims_const::_DIMS_NUM_INPUT,true,true);

	if ($reset_tag>0 && isset($_SESSION['dims']['desktop_tag_filter'][$reset_tag])) {// || !isset($_SESSION['dims']['desktop_tag_filter'])) {
			//$_SESSION['dims']['desktop_tag_filter']='';

			unset($_SESSION['dims']['desktop_tag_filter'][$reset_tag]);
			unset($_SESSION['dims']['current_object']['id_record']);
			unset($_SESSION['dims']['current_object']['id_object']);
			unset($_SESSION['dims']['current_object']['id_module']);

			if ($reset_tag) {
			 dims_redirect('/admin.php');
			}
	}

	$tag_filter=dims_load_securvalue('tagfilter',dims_const::_DIMS_NUM_INPUT,true,true,false);

	if (isset($_GET['tagfilter']) && $desktop_filter_type!=0) {
			$desktop_filter_type=0;
			$_SESSION['dims']['desktop_filter_type']=0;
			$updatelist=true;
	}

	// recherche des activites liées à l'utilisateur
	$user = new user();
	$listTags=array();
	$lstUsers=array();
	$countByType=array();

	if ($tag_filter>0) {
		$_SESSION['dims']['desktop_tag_filter'][$tag_filter]=$tag_filter;
	}
	else {
		//
	}
	$listTags=$_SESSION['dims']['desktop_tag_filter'];

	$user->open($_SESSION['dims']['userid']);
	if ($desktop_view_type==0) {
			$listact=$user->getActivities($_SESSION['dims']['workspaceid'],$view_user_id,$desktop_filter_type,$listTags,$lstUsers,$countByType);
	}
	elseif ($desktop_view_type==1) {
			$listact=$user->getActivities($dims->getListWorkspaces(),$view_user_id,$desktop_filter_type,$listTags,$lstUsers,$countByType);
	}

	$updatelist=false;

	if (empty($listact) && isset($desktop_tag_filter) && $desktop_tag_filter!=0) {
			//$desktop_tag_filter=array();
			//$_SESSION['dims']['desktop_tag_filter']=0;
			$updatelist=true;
	}

	if ($updatelist) {
			$listact=$user->getActivities($dims->getListWorkspaces(),$view_user_id,$desktop_filter_type,$listTags,$lstUsers,$countByType);
	}
	// construction de la liste des reférences aux modules de Docs
	$listdocmodules=array();
	$isadmindoc=$dims->isModuleTypeEnabled('doc');
	if ($isadmindoc) {
			foreach($dims->getModuleByType('doc') as $i =>$mod) {
					$listdocmodules[$mod['instanceid']]=$mod['instanceid'];
			}
	}

	 $sizeofw=sizeof($dims->getWorkspaces());
	?>
	<div style="position:relative;display:block;">
		  <div class="ui-widget-header ui-helper-clearfix">
			<span class="title action" style="float:right;">

				<?
				$arrayChoice = array();
				$arrayChoice[0]=$_DIMS['cste']['_DIMS_LABEL_TOP_NEWS'];
				$arrayChoice[1]=$_DIMS['cste']['_DIMS_ALL_WORKSPACES'];
				//$arrayChoice[2]=$_DIMS['cste']['_FORM_TASK_TIME_TODO'];
				// a reactiver après
				$taille=sizeof($arrayChoice)-1;
				foreach($arrayChoice as $i=>$val) {
					$corner = ""; $select = "";
					if($i==$desktop_view_type) $select="ui-state-active ui-state-hover";
					if($i==0) $corner="ui-corner-left";
					else if($i==$taille) $corner="ui-corner-right";
					echo '<a class="ui-button ui-widget ui-state-default ui-button-text-only '.$corner.' '.$select.'" href="admin.php?desktop_view_type='.$i.'">
								<span class="ui-button-text">'.$val.'</span>
							</a> ';
					/*echo '<input id="radio'.$i.'" class="ui-helper-hidden-accessible" type="radio" name="desktop_view_date"  value="'.$i.'" onclick="javascript:document.location.href=\"admin.php?desktop_view_type='.$i.'\"">
							<label class="ui-button ui-widget ui-state-default ui-button-text-only '.$corner.' '.$select.'" for="radio'.$i.'" >
								<span class="ui-button-text">'.$val.'</span>
							</label> ';*/
				}
				?>
				 </span>
			<span class="ui-icon" style="float:left;"></span>
			<div>

					<? echo $_DIMS['cste']['_DIMS_LABEL_NEWS_FEED']; ?>
			</div>
		  </div>
			<div class="options">
				<form name="form_view_date" action="" method="POST">
				<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("desktop_view_date");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
				echo "<img src=\"./common/img/arrow_ltr.png\"><font style=\"font-style:normal;font-weight:bold;\">".$_DIMS['cste']['_DIMS_DATE']." : </font>";
				?>
				<span class="ui-buttonset">
				<?php
				// mode de vue cette semaine
				$tab=array();
				$tab[]=ucfirst($_DIMS['cste']['_DIMS_LABEL_DAY']);
				$tab[]=ucfirst($_DIMS['cste']['_DIMS_LABEL_THIS_WEEK']);
				$tab[]=ucfirst($_DIMS['cste']['_DIMS_LABEL_15_DAYS']);
				$tab[]=ucfirst($_DIMS['cste']['_DIMS_LABEL_THIS_MONTH']);
				$tab[]=ucfirst($_DIMS['cste']['_DIMS_LABEL_3_MONTHS']);

				foreach($tab as $i=>$choiceview) {
					$select = "";$corner = "";
					if ($i==$_SESSION['dims']['desktop_view_date']) $select="ui-state-active ui-state-hover";
					if($i==0) $corner="ui-corner-left";
					else if($i==(count($tab)-1)) $corner="ui-corner-right";
					echo '<input id="radio'.$i.'" class="ui-helper-hidden-accessible" type="radio" name="desktop_view_date"  value="'.$i.'" onclick="javascript:document.form_view_date.submit();">
							<label class="ui-button ui-widget ui-state-default ui-button-text-only '.$corner.' '.$select.'" for="radio'.$i.'" >
								<span class="ui-button-text">'.$choiceview.'</span>
							</label> ';
				}
				?>
				</span>
				</form>
			</div>
			<div class="options">
				<span class="ui-buttonset">
					<?
					echo "<img src=\"./common/img/arrow_ltr.png\"><font style=\"font-style:normal;font-weight:bold;\">".$_DIMS['cste']['_FORMS_FILTER']." : </font>";
					$arrayChoice = array();
					$arrayChoice[0]=$_DIMS['cste']['_DIMS_LABEL_NONE'];
					$arrayChoice[1]=$_DIMS['cste']['_DIMS_LABEL_CONTACTS'];
					$arrayChoice[2]=$_DIMS['cste']['_DIMS_LABEL_EVENTS'];
					$arrayChoice[3]=$_DIMS['cste']['_DOCS'];
					$arrayChoice[4]=$_DIMS['cste']['_DIMS_LABEL_OTHER']."s";

					$taille=sizeof($arrayChoice)-1;
					foreach($arrayChoice as $i=>$val) {
						$select = ""; $corner = "";
						if (isset($countByType[$i])) {
							if ($i>0) {
								$val.=" (".$countByType[$i].")";
							}
						}
						if($i==$desktop_filter_type) $select="ui-state-active ui-state-hover";
						if($i==0) $corner="ui-corner-left";
						else if($i==$taille) $corner="ui-corner-right";
						echo '<a class="ui-button ui-widget ui-state-default ui-button-text-only '.$corner.' '.$select.'" href="admin.php?desktop_filter_type='.$i.'">
									<span class="ui-button-text">'.$val.'</span>
								</a> ';

					}
					?>
				</span>
			</div>
			<?
			if (isset($_SESSION['dims']['sub_activities']) && sizeof($_SESSION['dims']['sub_activities'])>0) {
			?>
			<div class="options fwb">
				<?
				// on regarde l'affichage des éléments

				switch ($desktop_filter_type) {
					case 1:
						echo "<img src=\"./common/img/empty.png\">";
						// on filtre sur le type de l'élément
						if (isset($_SESSION['dims']['sub_activities'][$desktop_filter_type])) {
							// boucle sur les activites
							$tot=sizeof($_SESSION['dims']['sub_activities'][$desktop_filter_type]);
							foreach($_SESSION['dims']['sub_activities'][$desktop_filter_type] as $i=>$act) {
								 if ($i==$desktop_sublink) $sel=true;
								else $sel=false;

								if ($act['cpte']==0) {
									$stylelink='class="fontgray";';
								}
								else {
									 $stylelink='';
								}

								// check selection
								if ($sel) echo $act['cpte'].' '.$act['title'];
								else {
									echo '<a href="'.$act['link'].'"><font '.$stylelink.'>'.$act['cpte'].' '.$act['title'].'</font></a>&nbsp;';
								}

								if ($i<$tot) {
									echo "- ";
								}
							}
						}
						break;

					case 3:
						// on traite les docss

						break;
				}
			   ?>
			</div>
			<?
			}

			if ($view_user_id>0) {
				$usrcur->open($view_user_id);
				echo '<div class="options fwb"><img src="./common/img/arrow_ltr.png">'.$_DIMS['cste']['_FORMS_FILTER'].' '.strtolower($_DIMS['cste']['_DIMS_LABEL_BY_USER']).' : <a href="/admin.php?reset_user=1">'.$usrcur->fields['firstname']." ".$usrcur->fields['lastname'].'&nbsp;<img src="./common/img/delete.png" style="border:0px;"></a>';

				// Lien vers la fiche
				echo "<span style=\"margin-left:20px;padding:2px;\"><a href='javascript:void(0);' onclick='javascript:viewPropertiesObject(7,".$usrcur->fields['id_contact'].",1,1);'><img src='./common/img/view.png' style='border:0px;width:16px;height:16px;'> ".$_DIMS['cste']['_DIMS_LABEL_VIEW']." ".$_DIMS['cste']['_DIMS_LABEL_CT_FICHE']." <img src='/modules/system/img/contacts.png' style='border:0px;width:16px;height:16px;'></a></span></div>";
			}

			if (!empty($_SESSION['dims']['desktop_tag_filter'])) {
					require_once DIMS_APP_PATH . '/modules/system/class_tag.php';
					$tag = new tag();
					echo '<div class="options fwb">';
					echo '<img src="./common/img/arrow_ltr.png">'.$_DIMS['cste']['_FORMS_FILTER'].' tag :';

					foreach ($_SESSION['dims']['desktop_tag_filter'] as $idtag) {
						$tag->open($idtag);

						if (isset($_DIMS['cste'][$tag->fields['tag']])) {
							$labeltag = $_DIMS['cste'][$tag->fields['tag']];
						}
						else {
							$labeltag=$tag->fields['tag'];
						}
						echo '<a href="/admin.php?reset_tag='.$idtag.'">'.$labeltag.'&nbsp;<img src="./common/img/delete.png" style="border:0px;"></a>';
					}
					echo '</div>';
			}
			?>
	</div>
	<?
	$newlistact=array();
	$invertlistact=array(); // permet de faire la correspondance entre une action et sa position dans la liste finale
	$couraction=array();
	$couraction['id_user']=0;
	$couraction['type']=0;
	$history=array();
	$c=0;

	$idmoduledoc=0;
	$isadmindoc=$dims->isModuleTypeEnabled('doc');
	if ($isadmindoc) {
	foreach($dims->getModuleByType('doc') as $i =>$mod) {
			if ($idmoduledoc==0 && $dims->isModuleEnabled($mod['instanceid'])) {
				$idmoduledoc=$mod['instanceid'];
			}
		}
	}

	$_SESSION['dims']['desktop_tagused']=array();
	unset($_SESSION['dims']['desktopactions']);

	$tab_displayboject=array();

	?>
	<div>
			<ul>
			<?
			//dims_print_r($listact);
			$style="";
			foreach ($listact as $k=>$action) {
				if ($action['ref']==-1) {
					if ($k==0) {
							$style="ptb listItem firstElem";
					}
					else {
							$style="ptb listItem listItemBorder listItemColor";
					}
			?>
			<li class="<? echo $style; ?>">
					<div style='display:block;'>
							<a class="listItemImageLink">
									<?
									if($lstUsers[$action['id_user']]['photo'] != "") {
											$srcimg=$lstUsers[$action['id_user']]['photo'];
									}
									else {
											$srcimg="./common/img/contact.gif";
									}
									?>
									<img class="listItemImage" src="<? echo $srcimg;?>">
							</a>
							<div class="listItemContent">
									<h6 class="listItemMessage">
											<?

				if (isset($_DIMS['cste'][$action['comment']])) {
						$comment=$_DIMS['cste'][$action['comment']];
				}
				else {
						$comment=$action['comment'];
				}

				if (isset($lstUsers[$action['id_user']])) {
						$usr=$lstUsers[$action['id_user']];
						echo '<a href="/admin.php?view_user_id='.$usr['id'].'">'.$usr['firstname']." ".$usr['lastname'].'</a>';
				}

				echo " ".$comment;

				if (!empty($action['objects'])) {
						$cc=0;
						// verification si pas propre fiche
						foreach ($action['objects'] as $obj) {
								//if ($obj['id_module'] != 1 || $obj['id_object'] != dims_const::_SYSTEM_OBJECT_CONTACT || ($obj['id_record'] != $_SESSION['dims']['user']['id_contact'] && $obj['id_record'] != $lstUsers[$action['id_user']]['id_contact'])) {
										if ($cc>0) echo " & ";
										// to modify !!!!!!!
										if ($action['comment']=='_DIMS_LABEL_FILE_CREATED') {
											$obj['id_module']=$idmoduledoc;
											//echo $idmoduledoc;
										}
										if ($obj['id_module']==317) $obj['id_module']=1;
										$link='javascript:viewPropertiesObject('.$obj['id_object'].','.$obj['id_record'].','.$obj['id_module'].',1);';
										echo " <a href=\"".$link."\">".$obj['title']."</a>";
										$cc++;

										// check si objet courant toujours utilise
										if (isset($_SESSION['dims']['current_object']['id_module']) && $_SESSION['dims']['current_object']['id_module']==$obj['id_module'] && $_SESSION['dims']['current_object']['id_object']==$obj['id_object'] &&  $_SESSION['dims']['current_object']['id_record']==$obj['id_record']) {
											$_SESSION['dims']['current_object']['mustview']=true;
										}
								//}
						}
				}
				if (isset($action['more']) && $action['more']>0) {
						// on ajout le 'and X more'
						if ($action['more']==1) {
								$other=$_DIMS['cste']['_DIMS_LABEL_OTHER'];
						}
						else {
								$other=$_DIMS['cste']['_DIMS_LABEL_OTHERS'];
						}

						//<a href=\"javascript:void(0);\" onclick=\"displayMoreActions('".$action['key']."');\">
						//echo "<font style=\"font-style:italic;\"> ".$_DIMS['cste']['_DIMS_LABEL_AND']." <a href=\"".dims_urlencode("/admin.php?desktopMoreAction=".$action['key'])."\">".$action['more']." ".$other;
						echo "<font style=\"font-style:italic;\"> ".$_DIMS['cste']['_DIMS_LABEL_AND']." <a href=\"javascript:void(0);\" onclick=\"displayMoreActions('".$action['key']."');\">".$action['more']." ".$other;

						echo "<span id=\"moreimg_".$action['key']."\">";
						if (isset($_SESSION['dims']['desktop_more_actions'][$action['key']])) {
							echo " <img src=\"./common/img/go-up.png\">";
						}
						else {
							echo " <img src=\"./common/img/go-down.png\">";
						}

						echo "<span></font></a>";
						echo "</span>";
				}

											?>
									</h6>
									<?
									/*if ($action['id_workspace']!=$_SESSION['dims']['workspaceid']) {
											$work=$dims->getWorkspaces($action['id_workspace']);
											echo "<span style=\"clear:left;display:block;\">".$work['label']."</span>";
									}*/
									// affichage des tags eventuels
									if (!empty($action['tags'])) {
											echo '<span="listItemSource"><img src="./common/img/tags-icon.png" alt="">&nbsp;Tags:';
											$tot=sizeof($action['tags']);
											$c=0;
											foreach ($action['tags'] as $k=>$tag) {
												if (!isset($_SESSION['dims']['desktop_tagused'][$k])) {
													$_SESSION['dims']['desktop_tagused'][$k]=1;
												}
												else {
													$_SESSION['dims']['desktop_tagused'][$k]++;
												}

												if (isset($desktop_tag_filter) && isset($desktop_tag_filter[$k])) {
														echo "<a href='".$dims->getUrlPath()."?tagfilter=".urlencode($k)."'><font style=\"font-weight:bold;\">".$listTags[$k]."</font></a>";
												}
												else {
														echo "<a href='".$dims->getUrlPath()."?tagfilter=".urlencode($k)."'>".$listTags[$k]."</a>";
												}
												$c++;
												if ($c<$tot) echo ", ";
											}
											echo '</span>';
									}
									?>
									<span class="listItemSource" style="clear:both;width:100%;">
											<?
											echo dims_nicetime($action['timestp_modify']);
											?>

											- <a href="javascript:void(0);" onclick="displayAction(event,<? echo $_SESSION['dims']['workspaceid'];?>,<? echo $action['id_module'];?>,<? echo $action['id'];?>,<? echo dims_const::_ACTION_COMMENT;?>)"><? echo $_DIMS['cste']['_DIMS_LABEL_ANNOTATION']; ?></a>
											<? /*- <a href="#">Point d'interet </a>*/ ?>
									</span>
									<?

									if (isset($action['moreobjects'])) {
					$chmore='';
											echo "<ul>";
											foreach ($action['moreobjects'] as $k => $elem) {
						$chmore.= "<div class='listItemComment' style='display:block;clear:both;width:100%;'>";
						$chmore.= '<a class="listItemImageLink"><img class="listItemImageMedium" src="'.$srcimg.'"></a>';
						$chmore.= '<div class="listItemContent">';
						$chmore.= '<a>'.$usr['firstname']." ".$usr['lastname'].'</a> ';

						if (isset($_DIMS['cste'][$elem['comment']])) {
							$comment=$_DIMS['cste'][$elem['comment']];
						}
						else {
							$comment=$elem['comment'];
						}

						$chmore.= " ".$comment;

						if (!empty($elem['objects'])) {
							$cc=0;
							// verification si pas propre fiche
							foreach ($elem['objects'] as $obj) {
								if ($obj['id_module'] != 1 || $obj['id_object'] != dims_const::_SYSTEM_OBJECT_CONTACT || ($obj['id_record'] != $_SESSION['dims']['user']['id_contact'] && $obj['id_record'] != $lstUsers[$elem['id_user']]['id_contact'])) {
									if ($cc>0) $chmore.= " & ";
									// to modify !!!!!!!
									if ($elem['comment']=='_DIMS_LABEL_FILE_CREATED') {
										$obj['id_module']=$idmoduledoc;
									}
									if ($obj['id_module']==317) $obj['id_module']=1;
									$link='javascript:viewPropertiesObject('.$obj['id_object'].','.$obj['id_record'].','.$obj['id_module'].',1);';
									$chmore.= " <a href=\"".$link."\">".dims_strcut(html_entity_decode(strip_tags($obj['title'])),70)."</a>";
									$cc++;

									// check si objet courant toujours utilise
									if (isset($_SESSION['dims']['current_object']['id_module']) && $_SESSION['dims']['current_object']['id_module']==$obj['id_module'] && $_SESSION['dims']['current_object']['id_object']==$obj['id_object'] &&  $_SESSION['dims']['current_object']['id_record']==$obj['id_record']) {
										$_SESSION['dims']['current_object']['mustview']=true;
									}
								}
							}
						}

						$chmore.= '</div>';
						$chmore.= '<span class="listItemSource" >';
						$chmore.= dims_nicetime($elem['timestp_modify']);
						$chmore.= '</a></span>';

						$chmore.= "</div>";
						$chmore.= "</li>";
					}

					if (isset($action['more']) && $action['more']>0) {

						$stylemore='';
						$displaymore=false;
						if (isset($_SESSION['dims']['desktop_more_actions'][$action['key']])) {
							$displaymore=true;
						}
						$stylemore="display:block;visibility:visible;";

						echo "<div id=\"more_".$action['key']."\" style=\"clear:both;".$stylemore.";\">";

						if ($displaymore) {
							echo $chmore;
						}

						// on sauvegarde en session
						if (!isset($_SESSION['dims']['desktopactions'])) {
							$_SESSION['dims']['desktopactions']=array();
						}
						$_SESSION['dims']['desktopactions']["more_".$action['key']]=$chmore;
						echo "</div>";
					}
				}
									?>
							</div>
					</div>
			</li>
			<?
				}
			}
			?>
			</ul>
	</div>

	<div id="content_add_action">

	</div>
<?
}
// on peut afficher
/*if ($view_user_id>0) {

echo "<script type=\"text/javascript\">window.onload= function () {
   viewPropertiesObject(7,".$usrcur->fields['id_contact'].",1,1);}</script>";
}*/
?>
