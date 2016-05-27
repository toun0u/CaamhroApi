<?php
$news_act = dims_load_securvalue('news_act', dims_const::_DIMS_CHAR_INPUT, true, true, false);
$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);

switch($news_act) {
	default:
		$sql_a = '	SELECT		c.*, c.id as id_env
					FROM		dims_mod_newsletter_content c
					WHERE		id_newsletter = :idnewsletter
						';

			if(isset($upname) && $upname == 1 ) {
				$sql_a .= " ORDER BY		c.label DESC";
				$opt_trip = -1;
				$opt_trit = -2;
				$opt_tric = -3;
			}
			elseif(isset($upname) && $upname == -1) {
				$sql_a .= " ORDER BY		c.label ASC";
				$opt_trip = 1;
				$opt_trit = -2;
				$opt_tric = -3;
			}
			elseif(isset($upname) && $upname == 2) {
				$sql_a .= " ORDER BY		c.date_create DESC ";
				$opt_trip = -1;
				$opt_trit = -2;
				$opt_tric = -3;
			}
			elseif(isset($upname) && $upname == -2) {
				$sql_a .= " ORDER BY		c.date_create ASC ";
				$opt_trip = -1;
				$opt_trit = 2;
				$opt_tric = -3;
			}
			elseif(isset($upname) && $upname == 3) {
				$sql_a .= " ORDER BY		c.date_envoi DESC ";
				$opt_trip = -1;
				$opt_trit = -2;
				$opt_tric = -3;
			}
			elseif(isset($upname) && $upname == -3) {
				$sql_a .= " ORDER BY		c.date_envoi ASC ";
				$opt_trip = -1;
				$opt_trit = -2;
				$opt_tric = 3;
			}
			else {
				$sql_a .= " ORDER BY	c.date_create";
				$opt_trip = -1;
				$opt_trit = -2;
				$opt_tric = -3;
			}

			$res_a = $db->query($sql_a, array(
				':idnewsletter' => $id_news
			));

			$tab_env = array();
			while($tab_res = $db->fetchrow($res_a)) {
				$tab_env[$tab_res['id_env']] = $tab_res;
			}

		//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_LIST_ARTICLE']);
		?>
			<table width="100%" cellpadding="0" cellspacing="0" style="border:#3B567E 1px solid;background-color:white;">
				<tr>
					<td align="right" width="100%" style="padding-right:10px;">
					<?php echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_ADD_ARTICLE'],'./common/img/add.gif','javascript:document.location.href=\''.$scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER.'&news_act=add_article\''); ?>
					</td>
				</tr>
				<tr>
					<td>
					<?php
						if($db->numrows($res_a) > 0) {
							$class = "trl1";
							echo '	<table width="100%" cellpadding="0" cellspacing="0">
										<tr class="trl1" style="font-size:11px;font-weight:bold;height:20px;">
											<td><a href="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER.'&upname='.$opt_trip.'">'.$_DIMS['cste']['_DIMS_LABEL_TITLE'].'</a>
											</td>
											<td>'.$_DIMS['cste']['_DIMS_LABEL_LINKED_DOCS_EVT'].'
											</td>
											<td width="12%"><a href="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER.'&upname='.$opt_trit.'">'.$_DIMS['cste']['_DIMS_LABEL_ENT_DATEC'].'</a>
											</td>
											<td width="12%"><a href="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER.'&upname='.$opt_tric.'">'.$_DIMS['cste']['_DIMS_LABEL_SEND_DATE'].'</a>
											</td>
											<td width="15%">'.$_DIMS['cste']['_DIMS_ACTIONS'].'
											</td>
										</tr>
									';
							$id_module = $_SESSION['dims']['moduleid'];
							$id_object = dims_const::_SYSTEM_OBJECT_NEWSLETTER;
							foreach($tab_env as $id_env => $tab) {
								if($class == "trl1") $class = "trl2";
								else $class = "trl1";

								$date_cre = dims_timestamp2local($tab['date_create']);
								if(isset($tab['date_envoi']) && $tab['date_envoi'] != '') $date_env = dims_timestamp2local($tab['date_envoi']);
								else $date_env['date'] = '';

								$id_record = $id_env;

								$doc = '';
								require_once DIMS_APP_PATH.'include/functions/files.php';
								$lstfiles = dims_getFiles($dims,$id_module,$id_object,$id_record);
								if(isset($lstfiles) && $lstfiles != '') {
									foreach($lstfiles as $key => $file) {
										if($doc != '') $doc .= '<br/>';
										$doc .= '<a href='.$file['downloadlink'].' title="'.$file['name'].' - Voir le document.">'.$file['name'].'</a>';
									}
								}

								if ($date_env['date']=='') {
									$imgsend='icon_tickets.gif';
								}
								else {
									$imgsend='mail_tovalid.png';
								}

								echo '	<tr class="'.$class.'">
											<td><a href="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER.'&news_act=add_article&id_env='.$id_env.'">'.$tab['label'].'</a>
											</td>
											<td>'.$doc.'
											</td>
											<td>'.$date_cre['date'].'
											</td>
											<td>'.$date_env['date'].'
											</td>
											<td>
												<a href="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER.'&news_act=add_article&id_env='.$id_env.'"><img src="./common/img/edit.gif" title="More details"/></a>&nbsp;&nbsp;
												<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.dims_urlencode('admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_SUPPR_ARTICLE.'&id_news='.$id_news.'&id_env='.$id_env.'').'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')"><img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DELETE'].'"/></a>&nbsp;&nbsp;
												<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.dims_urlencode('admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action=test_sending&id_news='.$id_news.'&id_env='.$id_env.'').'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')"><img src="./common/img/publish.png" title="Make a test"/></a>&nbsp;&nbsp;
												<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.dims_urlencode('admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_SEND_ARTICLE.'&id_news='.$id_news.'&id_env='.$id_env.'').'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')"><img src="./common/img/'.$imgsend.'" title="Send newsletter"/></a>
											</td>
										</tr>';

							}
							echo '</table>';
						}
						else {
							echo $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NO_ARTICLE'];
						}
					?>
					</td>
				</tr>
			</table>
<?php
	break;
case 'add_article':
	require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_add_article.php');
	break;
}
?>
