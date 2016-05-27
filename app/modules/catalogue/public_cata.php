<link type="text/css" href="./common/modules/catalogue/include/styles.css" rel="stylesheet" />

<?php
// Get the selected family
$famId = dims_load_securvalue('famId', dims_const::_DIMS_NUM_INPUT, true, true);
$tabid = dims_load_securvalue('tabid', dims_const::_DIMS_NUM_INPUT, true, true);

if(!empty($famId)) $_SESSION['catalogue']['familyId'] = $famId;
if (empty($_SESSION['catalogue']['familyId'])) {
	$db->query("SELECT MIN(id_famille) AS familyId FROM `dims_mod_cata_famille` WHERE id_parent = 0 AND	id_module = {$_SESSION['dims']['moduleid']}");
	$rowFam = $db->fetchrow();
	$_SESSION['catalogue']['familyId'] = $rowFam['familyId'];
}
$famId = $_SESSION['catalogue']['familyId'];

// Inclusion de la classe cata_family
include_once './common/modules/catalogue/include/class_famille.php';

$toolbar = array();

if (empty($_SESSION['dims']['moduletabid'])) $_SESSION['dims']['moduletabid'] = _ADMIN_TAB_CATA_FAMILIES;

$toolbar[_ADMIN_TAB_CATA_FAMILIES]['title']		= _ADMIN_TAB_CATA_FAMILIES_LABEL;
$toolbar[_ADMIN_TAB_CATA_FAMILIES]['url']		= $dims->getScriptEnv().'?part=cata&dims_moduletabid='._ADMIN_TAB_CATA_FAMILIES;
$toolbar[_ADMIN_TAB_CATA_FAMILIES]['width']		= '130';

$toolbar[_ADMIN_TAB_CATA_SELCHPS]['title']		= _ADMIN_TAB_CATA_SELCHPS_LABEL;
$toolbar[_ADMIN_TAB_CATA_SELCHPS]['url']		= $dims->getScriptEnv().'?part=cata&dims_moduletabid='._ADMIN_TAB_CATA_SELCHPS;
$toolbar[_ADMIN_TAB_CATA_SELCHPS]['width']		= '130';

$toolbar[_ADMIN_TAB_CATA_GESTCHPS]['title']		= _ADMIN_TAB_CATA_GESTCHPS_LABEL;
$toolbar[_ADMIN_TAB_CATA_GESTCHPS]['url']		= $dims->getScriptEnv().'?part=cata&dims_moduletabid='._ADMIN_TAB_CATA_GESTCHPS;
$toolbar[_ADMIN_TAB_CATA_GESTCHPS]['width']		= '130';

$toolbar[_ADMIN_TAB_CATA_ARTEDIT]['title']		= _ADMIN_TAB_CATA_ARTEDIT_LABEL;
$toolbar[_ADMIN_TAB_CATA_ARTEDIT]['url']		= $dims->getScriptEnv().'?part=cata&dims_moduletabid='._ADMIN_TAB_CATA_ARTEDIT;
$toolbar[_ADMIN_TAB_CATA_ARTEDIT]['width']		= '130';

$toolbar[_ADMIN_TAB_CATA_ARTRECH]['title']		= _ADMIN_TAB_CATA_ARTRECH_LABEL;
$toolbar[_ADMIN_TAB_CATA_ARTRECH]['url']		= $dims->getScriptEnv().'?part=cata&dims_moduletabid='._ADMIN_TAB_CATA_ARTRECH;
$toolbar[_ADMIN_TAB_CATA_ARTRECH]['width']		= '130';

$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true);
$fid = dims_load_securvalue('fid', dims_const::_DIMS_CHAR_INPUT, true, false);
$str = dims_load_securvalue('str', dims_const::_DIMS_CHAR_INPUT, true, false);
if(empty($op)) $op = '';

if ($op == 'xml_detail_famille') {
	ob_end_clean();
	$familys = cata_getfamilys();
	echo cata_build_tree($familys, $fid, $str);
	die();
}

if ($op != 'add_photo' && $op != 'edit' && $op != 'cms_galery_popup') {
	?>
	<script type="text/javascript">
		function cata_showfamily(fid,str) {
			elt = document.getElementById(fid+'_plus');
			if (elt.innerHTML.indexOf('plusbottom') != -1) elt.innerHTML = elt.innerHTML.replace('plusbottom', 'minusbottom');
			else  if (elt.innerHTML.indexOf('minusbottom')  != -1) elt.innerHTML = elt.innerHTML.replace('minusbottom', 'plusbottom');
			else  if (elt.innerHTML.indexOf('plus')  != -1) elt.innerHTML = elt.innerHTML.replace('plus', 'minus');
			else  if (elt.innerHTML.indexOf('minus')  != -1) elt.innerHTML = elt.innerHTML.replace('minus', 'plus');

			if (elt = document.getElementById(fid)) {
				if (elt.style.display == 'none') {
					if (elt.innerHTML.length < 20) dims_xmlhttprequest_todiv('<?php echo $dims->getScriptEnv(); ?>','part=cata&op=xml_detail_famille&fid='+fid+'&str='+str,'|',fid);
					document.getElementById(fid).style.display='block';
				} else {
					document.getElementById(fid).style.display='none';
				}
			}
		}

		function markRow(tr, i) {
			checkbox = tr.getElementsByTagName( 'input' )[0];

			if (tr.className=='marked') {
				if (i % 2 == 0) {
					tr.className='odd';
				} else {
					tr.className='even';
				}
				if (checkbox && checkbox.checked) {
					checkbox.checked = false;
				}
			} else {
				tr.className='marked';
				if (checkbox && !checkbox.checked) {
					checkbox.checked = true;
				}
			}
		}

		function markAllRows( container_id ) {
			var rows = document.getElementById(container_id).getElementsByTagName('tr');

			for ( var i = 0; i < rows.length; i++ ) {
				if (rows[i].className != 'nomark') {
					if (rows[i].className=='marked') {
						if (i % 2 == 0) {
							rows[i].className='odd';
						} else {
							rows[i].className='even';
						}
					} else {
						rows[i].className='marked';
					}
					checkbox = rows[i].getElementsByTagName( 'input' )[0];
					if (checkbox) {
						checkbox.checked = ! checkbox.checked;
					}
				}
			}
		}
	</script>

	<TABLE WIDTH="100%" HEIGHT="100%">
		<TR ALIGN="LEFT" VALIGN="TOP">
			<!-- tree -->
			<TD WIDTH="25%" VALIGN="TOP">
				<?php
				if ($op != 'export') {
					echo $skin->open_simplebloc('','100%');
					?>
					<!-- the tree itself -->
					<TABLE CELLPADDING="4" width="100%">
						<TR ALIGN="LEFT">
							<td valign="top" style="width:100%">
								<?php
								$familys = cata_getfamilys_adm();
								echo cata_build_tree($familys);
								?>
							</td>
						</TR>
					</TABLE>
					<?php
					echo $skin->close_simplebloc();
				}
				?>
			</TD>
			<!-- management -->
			<TD width="70%">
				<TABLE WIDTH="100%" HEIGHT="100%">
					<!-- toolbar -->
					<TR ALIGN="LEFT" VALIGN="TOP">
						<TD ALIGN="LEFT" VALIGN="TOP">
							<!-- here -->
							<?php
							if(!strstr($dims->getScriptEnv(), '/index-light.php') && $op != 'export')
								echo $skin->create_tabs('', $toolbar, $_SESSION['dims']['moduletabid']);
							?>
						</TD>
					</TR>
					<!-- content -->
					<TR ALIGN="LEFT" VALIGN="TOP">
						<TD HEIGHT="100%">
							<?php
}

switch ($_SESSION['dims']['moduletabid']) {
	case _ADMIN_TAB_CATA_FAMILIES:
		include_once './common/modules/catalogue/public_famille.php';
		break;
	case _ADMIN_TAB_CATA_ARTEDIT:
		include_once './common/modules/catalogue/public_article.php';
		break;
	case _ADMIN_TAB_CATA_ARTRECH:
		switch ($op) {
			case 'edit':
				include_once './common/modules/catalogue/public_article_edit.php';
				break;
			case 'reporting':
			case 'export':
				include './common/modules/catalogue/admin_article_reporting.php';
				break;
			case 'rattach_articles_sel':
				if (!empty($_POST['articles'])) {
					include_once './common/modules/catalogue/include/class_article.php';
					include_once './common/modules/catalogue/include/class_article_famille.php';

					foreach ($_POST['articles'] as $id_article) {
						if (is_numeric($id_article)) {
							$art = new article();
							if ($art->findById($id_article)) {
								$artfam = new cata_article_famille();
								$artfam->open($id_article, $art->fields['id_adh'], $_SESSION['catalogue']['familyId']);
								$artfam->save();
							}
						}
					}
					unset ($_SESSION['catalogue']['selArticles']);
				}
				dims_redirect($dims->getScriptEnv());
				break;
			case 'save_publish':
				if (!empty($_POST['articles'])) {
					$ts = dims_createtimestamp();

					$db->query('
						UPDATE	dims_mod_cata_article
						SET		published = !published,
								date_modify = '.$ts.'
						WHERE	id_article IN ('.implode(',', $_POST['articles']).')
						AND		id_module = '.$_SESSION['dims']['moduleid'].'
						AND		id_group = '.dims_viewworkspaces($_SESSION['dims']['moduleid']).'
						');
				}
				dims_redirect($dims->getScriptEnv());
				break;
			default:
				include_once './common/modules/catalogue/public_article_recherche.php';
				break;
		}
		break;
	case _ADMIN_TAB_CATA_SELCHPS:
		include_once './common/modules/catalogue/public_famille_selchps.php';
		break;
	case _ADMIN_TAB_CATA_GESTCHPS:
		include_once './common/modules/catalogue/public_admin_chpdyn.php';
		break;

}

if ($op != 'add_photo') {
							?>
						</TD>
					</TR>
				</TABLE>
			</TD>
		</TR>
	</TABLE>
<?php
}
