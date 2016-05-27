<?php

require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
require_once DIMS_APP_PATH . '/modules/system/class_search_expression_tag.php' ;
require_once DIMS_APP_PATH . '/modules/system/class_search.php' ;

require_once DIMS_APP_PATH . '/modules/system/class_tiers.php';
require_once DIMS_APP_PATH . '/modules/system/class_contact.php';
require_once DIMS_APP_PATH . '/modules/system/class_action.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';

if(isset($_SESSION['dims']['search']['current_search'])){
	$search = new search_expression(true);
	$search->open($_SESSION['dims']['search']['current_search'] );
	//récuparation des totaux en une requête ---------------------------------------------------------------------------------------------------
	/*if(isset($_SESSION['desktop']['search']['tags']))
		$counts = $search->countResults(null, $_SESSION['desktop']['search']['tags']);
	else*/
		$counts = $search->countResults(null);
	$all = 0;
	foreach($counts as $subtotal){
		$all += $subtotal;
	}
	$companies		= isset($counts[search::RESULT_TYPE_COMPANY])?$counts[search::RESULT_TYPE_COMPANY]:0;
	$contacts		= isset($counts[search::RESULT_TYPE_CONTACT])?$counts[search::RESULT_TYPE_CONTACT]:0;
	$opportunities	= isset($counts[search::RESULT_TYPE_OPPORTUNITY])?$counts[search::RESULT_TYPE_OPPORTUNITY]:0;
	$activites		= isset($counts[search::RESULT_TYPE_ACTIVITY])?$counts[search::RESULT_TYPE_ACTIVITY]:0;
	$missions		= isset($counts[search::RESULT_TYPE_MISSION])?$counts[search::RESULT_TYPE_MISSION]:0;
	$fairs			= isset($counts[search::RESULT_TYPE_FAIR])?$counts[search::RESULT_TYPE_FAIR]:0;
	$documents		= isset($counts[search::RESULT_TYPE_DOCUMENT])?$counts[search::RESULT_TYPE_DOCUMENT]:0;
	$pictures		= isset($counts[search::RESULT_TYPE_PICTURE])?$counts[search::RESULT_TYPE_PICTURE]:0;
	$movies			= isset($counts[search::RESULT_TYPE_MOVIE])?$counts[search::RESULT_TYPE_MOVIE]:0;
	$suivis			= isset($counts[search::RESULT_TYPE_SUIVI])?$counts[search::RESULT_TYPE_SUIVI]:0;

	//$all -= ($pictures + $movies);// CYRIL - 24/01/2012 --> Hack demandé par André Hansen pour ne pas afficher les pictures ni les movies

	//gestion de la pagination interne ---------------------------------------------------------------------------------------------------------
	$page = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true, true, $currentvar, 0);
	if ((isset($page) && $page!='') || (isset($_SESSION['dims']['search']['page']))){
		if(isset($page) && $page!='') $_SESSION['dims']['search']['page'] = $page;
		else if(isset($_SESSION['dims']['search']['page'])) $page = $_SESSION['dims']['search']['page'];
	}
	else
	{
		$page = $_SESSION['dims']['search']['page'] = 0;
	}

	$category = dims_load_securvalue('cat', dims_const::_DIMS_NUM_INPUT, true, true, true, $currentvar, 0);

	if(isset($category) && $category >= 0){
		$_SESSION['dims']['search']['category'] = $category;
	}elseif(isset($_SESSION['dims']['search']['category'])){
		$category = $_SESSION['dims']['search']['category'];
	}else{
		$category = $_SESSION['dims']['search']['category'] = 0;
	}

//$_SESSION['cste']['NO_ELEMENT']
//$_SESSION['cste']['NO_ELEMENT_FEMININ']
	switch($category){
		default:
			$total = $all;
			$label_result = $_SESSION['cste']['RESULT_MINUSCULE'];
			if($total > 1) $label_result .= 's';
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_COMPANY:
			$total = $companies;
			$label_result = $_SESSION['cste']['COMPANY_MINUSCULE'];
			if($total > 1) $label_result = $_SESSION['cste']['COMPANIES_MINUSCULE'];
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT_FEMININ']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_CONTACT:
			$total = $contacts;
			$label_result = $_SESSION['cste']['CONTACT_MINUSCULE'];
			if($total > 1) $label_result .= 's';
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_OPPORTUNITY:
			$total = $opportunities;
			$label_result = $_SESSION['cste']['OPPORTUNITY'];
			if($total > 1) $label_result = $_SESSION['cste']['OPPORTUNITIES_MINUSCULE'];
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT_FEMININ']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_ACTIVITY:
			$total = $activites;
			$label_result = $_SESSION['cste']['ACTIVITIES'];
			if($total > 1) $label_result = ucfirst($_SESSION['cste']['ACTIVITIES']);
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT_FEMININ']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_MISSION:
			$total = $missions;
			$label_result = strtolower($_SESSION['cste']['_DIMS_IMPORT_LABEL_MISSION']);
			if($total > 1) $label_result .= 's';
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT_FEMININ']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_FAIR:
			$total = $fairs;
			$label_result = $_SESSION['cste']['SIMPLE_FAIR'];
			if($total > 1) $label_result .= 's';
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT_FEMININ']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_DOCUMENT:
			$total = $documents;
			$label_result = $_SESSION['cste']['DOCUMENT'];
			if($total > 1) $label_result .= 's';
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_SUIVI:
			$total = $suivis;
			$label_result = $_SESSION['cste']['_MONITORINGS'];
			//if($total > 1) $label_result .= 's';
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_PICTURE:
			$total = $pictures;
			$label_result = $_SESSION['cste']['PICTURE_SINGULIER'];
			if($total > 1) $label_result .= 's';
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT_FEMININ']) . ' ' . $label_result;
			break;
		case search::RESULT_TYPE_MOVIE:
			$total = $movies;
			$label_result = $_SESSION['cste']['MOVIE_SINGULIER'];
			if($total > 1) $label_result .= 's';
			$label_result_header = $label_result;
			if($total == 0) $label_result =  ucfirst($_SESSION['cste']['NO_ELEMENT']) . ' ' . $label_result;
			break;
	}

	$search->page_courant = $page;
	$search->setPaginationParams(10, 5, false, $_SESSION['cste']['PAGINATION_FIRST'], 'Last', 'Previous', 'Next');//10 éléments par pages, 3 pages max affichées en même temps

	?>
	<?php if(!empty($_SESSION['dims']['search']['current_search'])){
		?>

		<div class="resume_simple_search">
				<div class="filters">
					<div class="nb_results">
						<?php
						if($search->getType() == search::TYPE_SIMPLE_SEARCH){
							if(!empty($_SESSION['dims']['modsearch']['my_real_expression'])){
							?>
								<p><span class="nb"><?php echo $total; ?></span> <?php echo $label_result_header;?> <?php echo $_SESSION['cste']['FOR_YOUR_SEARCH_ON']; ?> <span class="cote">'</span><?php echo $_SESSION['dims']['modsearch']['my_real_expression']; ?><span class="cote">'</span></p>
							<?php
							}
							else{//par exemple une recherche par tag en partance du bureau
							?>
								<p><span class="nb"><?php echo $total; ?></span> <?php echo $label_result_header;?> <?php echo $_SESSION['cste']['FOR_YOUR_SEARCH']; ?></p>
								<?php
							}
						}
						else if($search->getType() == search::TYPE_ADVANCED_SEARCH){
							?>
							<p><span class="nb"><?php echo $total; ?></span> <?php echo $label_result_header;?> <?php echo $_SESSION['cste']['FOR_YOUR_ADVANCED_SEARCH']; ?></p>
							<?php
						}
						?>

					</div>
					<?php
					$object_mailto = '';
					if(isset($_SESSION['desktop']['search']['tags']) && count($_SESSION['desktop']['search']['tags']) >0){?>
						<div class="tags_used">
							<span class="label"><?php echo $_SESSION['cste']['FILTERED_WITH']; ?> :</span>
							<ul>
								<?php
								$nb_tags = count($_SESSION['desktop']['search']['tags']);
								$i = 0;
								foreach($_SESSION['desktop']['search']['tags'] as $tag){
									$t = new tag();
									$t->open($tag);
									$object_mailto .= $t->fields['tag'];
									if($i < $nb_tags - 1) $object_mailto .= ', ';
									$t->setLightAttribute('delete_button', true);
									?>
									<li><?php $t->display( _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php'); ?> </li>
									<?php
									$i++;
								}
								?>
							</ul>
						</div>
					<?php
					}
					?>
				</div>
				<?php
					if(isset($_SESSION['desktop']['search']['tags']))
						$contacts_list = $search->getLightContacts($_SESSION['desktop']['search']['tags'], true);
					else $contacts_list = $search->getLightContacts(null, true);
					$liste = '';
					$valide_cts = 0;
					foreach($contacts_list as $c){
						if(file_exists($c->getPhotoPath(60))){
							$valide_cts++;
							$liste .= '<li><a href="'.dims::getInstance()->getScriptEnv().'?submenu=1&mode=contact&action=show&id='.$c->getId().'" title="'.$c->getFirstname().' '.$c->getLastname().'"><img class="carrousel_picto" src="'.$c->getPhotoWebPath(60).'"  alt="'.$c->getFirstname().' '.$c->getLastname().'"/></a></li>';
						}
					}
					if($liste != ''){
						?>
						<div class="carrousel_contacts">
							<div class="real_carrousel">
								<ul id="mycarousel" class="jcarousel-skin-tango">
									<?php
									echo $liste;
									?>
								</ul>
							</div>
							<div class="label_carrousel"><?php echo $_SESSION['cste']['SOME_CONTACT_MATCHING_WITH_YOUR_SEARCH']; ?></div>
						</div>

						<div style="clear:both;"></div>
					<?php
					}
					?>
		</div>
	<?php
	}
	?>
	<div class="zone_result_category_content_gauche">
		<div class="subtitle"><span class="subtitle"><?php echo $_SESSION['cste']['_DIMS_LABEL_CATEGORY']; ?></span></div>
		<div class="result_category">
			<ul>
				<li <?php if($category == 0) echo 'class="selected"'; ?>>
					<a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat=0';?>"><?php echo $_SESSION['cste']['ALL_RESULTS']; ?> (<?php echo $all; ?>)</a>
				</li>
				<li <?php if($category == search::RESULT_TYPE_COMPANY) echo 'class="selected"'; ?>>
					<?php if($companies){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_COMPANY;?>"><?php } ?><?php echo $_SESSION['cste']['_DIMS_LABEL_ENTERPRISES']; ?> <?php if($companies)echo '('.$companies.')</a>'; ?>
				</li>
				<li <?php if($category == search::RESULT_TYPE_CONTACT) echo 'class="selected"'; ?>>
					<?php if($contacts){ ?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_CONTACT;?>"><?php }	?><?php echo $_SESSION['cste']['_DIMS_LABEL_CONTACTS']; ?> <?php if($contacts)echo '('.$contacts.')</a>'; ?>
				</li>
				<!--<li <?php if($category == search::RESULT_TYPE_OPPORTUNITY) echo 'class="selected"'; ?>>
					<?php if($opportunities){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_OPPORTUNITY;?>"><?php } ?><?php echo $_SESSION['cste']['OPPORTUNITIES']; ?> <?php if($opportunities)echo '('.$opportunities.')</a>'; ?>
				</li>
				<li <?php if($category == search::RESULT_TYPE_ACTIVITY) echo 'class="selected"'; ?>>
					<?php if($activites){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_ACTIVITY;?>"><?php } ?><?php echo $_SESSION['cste']['ACTIVITIES']; ?> <?php if($activites)echo '('.$activites.')</a>'; ?>
				</li>
				<li <?php if($category == search::RESULT_TYPE_MISSION) echo 'class="selected"'; ?>>
					<?php if($missions){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_MISSION;?>"><?php } ?><?php echo $_SESSION['cste']['MISSIONS']; ?> <?php if($missions)echo '('.$missions.')</a>'; ?>
				</li>
				<li <?php if($category == search::RESULT_TYPE_FAIR) echo 'class="selected"'; ?>>
					<?php if($fairs){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_FAIR;?>"><?php } ?><?php echo $_SESSION['cste']['FAIRS']; ?> <?php if($fairs)echo '('.$fairs.')</a>'; ?>
				</li>-->
				<li <?php if($category == search::RESULT_TYPE_DOCUMENT) echo 'class="selected"'; ?>>
					<?php if($documents){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_DOCUMENT;?>"><?php } ?><?php echo $_SESSION['cste']['_DOCS']; ?> <?php if($documents)echo '('.$documents.')</a>'; ?>
				</li>
				<!--<li <?php if($category == search::RESULT_TYPE_SUIVI) echo 'class="selected"'; ?>>
					<?php if($suivis){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_SUIVI;?>"><?php } ?><?php echo $_SESSION['cste']['_MONITORINGS']; ?> <?php if($suivis)echo '('.$suivis.')</a>'; ?>
				</li>
				<li <?php if($category == search::RESULT_TYPE_PICTURE) echo 'class="selected"'; ?>>
					<?php if($pictures){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_PICTURE;?>"><?php } ?><?php echo $_SESSION['cste']['PICTURES']; ?> <?php if($pictures)echo '('.$pictures.')</a>'; ?>
				</li>
				<li <?php if($category == search::RESULT_TYPE_MOVIE) echo 'class="selected"'; ?>>
					<?php if($movies){?><a href="<?php echo $dims->getScriptEnv().'?submenu=1&mode=default&page=0&cat='.search::RESULT_TYPE_MOVIE;?>"><?php } ?><?php echo $_SESSION['cste']['MOVIES']; ?> <?php if($movies)echo '('.$movies.')</a>'; ?>
				</li>-->
			</ul>
			<div style="clear:both;"></div>
		</div>
		<div class="matching_tags">
			<div class="subtitle">
				<span class="subtitle"><?php echo $_SESSION['cste']['MATCHING_TAGS']; ?></span>
				<?php
				//hack demandé par André Hansen, par défaut la pupuce rouge
				if(!isset($_SESSION['desktopV2']['content_gauche']['matching_tags_table_business'])){
					$_SESSION['desktopV2']['content_gauche']['matching_tags_table_business'] = 1;
				}
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_gauche']['zone_matching_tags']) && $_SESSION['desktopV2']['content_gauche']['zone_matching_tags'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.matching_tags_table_business').slideToggle('fast',flip_flop($('div.matching_tags_table_business'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));"/>
			</div>

			<div class="matching_tags_table_business" <?php if(isset($_SESSION['desktopV2']['content_gauche']['matching_tags_table_business']) && $_SESSION['desktopV2']['content_gauche']['matching_tags_table_business'] == 0) echo 'style="display:none;"'; ?>>
				<?php
					$search_tags = new search_expression_tag();
					if (isset($_SESSION['desktop']['search']['tags']))
						$tags = $search_tags->getAllTagsResults($search->getId(), $category, $_SESSION['desktop']['search']['tags']);
					else
						$tags = $search_tags->getAllTagsResults($search->getId(), $category);
					if(isset($tags) && count($tags)){
						?>
						<ul>
							<?php
							foreach($tags as $tag){
								?>
								<li>
								<?php
									$tag->display( _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
								?>
								</li>
								<?php
							}
							?>
						</ul>
						<div style="clear: both;"></div>
						<?php
					}
					else{
						?>
						<span class="no_result"><?php echo $_SESSION['cste']['NO_TAG_FOR_THIS_SEARCH']; ?></span>
						<?php
					}
					?>
			</div>
		</div>
	   <?php
	   if (false ) {/*($dims->isModuleTypeEnabled('events') && ($currentworkspace['activeevent'] || $currentworkspace['activeeventstep']))
			|| $contacts > 0
			|| $companies > 0){*/
	   ?>
			<div class="extras">
				<div class="subtitle"><span  class="subtitle"><?php echo $_SESSION['cste']['EXTRAS']; ?></span></div>
				<?php
				if($companies){
					?>
					<div style="margin-top:10px;"></div>
					<a href="admin.php?dims_op=desktopv2&action=exportCompaniesWithContacts">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_excel.png">
						<span><?php echo $_SESSION['cste']['EXPORT_ALL_DATA']; ?></span>
					</a>
					<?php
				}
				if ($contacts > 0){
				?>
				<div style="margin-top:12px;"></div>
					<a href="admin.php?dims_op=desktopv2&action=exportContacts">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_excel.png">
						<span><?php echo $_SESSION['cste']['EXPORT_CONTACTS_DATA']; ?></span>
					</a>
				<?php
				}
				if($companies){
					?>
					<div style="margin-top:10px;"></div>
					<a href="admin.php?dims_op=desktopv2&action=exportCompanies">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_excel.png">
						<span><?php echo $_SESSION['cste']['EXPORT_COMPANIES_DATA']; ?></span>
					</a>
					<?php
				}
				/*?>
				<div style="margin-top:12px;"></div>
					<a href="admin.php?dims_op=desktopv2&action=sendContactsVcf">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png">
						<span><?php echo $_SESSION['cste']['_INET_SEND_CONTACTS_EMAIL']; ?></span>
					</a>
				<?php*/
				/*if($contacts > 0){
					if(isset($_SESSION['desktop']['search']['tags']) && !empty($_SESSION['desktop']['search']['tags']))
						$recipients = $search->getLightContacts($_SESSION['desktop']['search']['tags'], false, true);
					else $recipients = $search->getLightContacts(null, false, true);
					$link = $search->getSearchContactMailData($object_mailto, $recipients);
					if(!empty($link)){
					?>
						<a href="<?php echo $link; ?>">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/send_email.png">
							<span>Send an email to contacts</span>
						</a>
				<?php
					}
					else{//cela signifie que dans la liste des contacts récupérés par la recherche, il n'y en a aucun qui
						?>
						<a href="javascript:void(0);" onclick="javascript:alert('None of the contacts has a valide email address');">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/send_email.png">
							<span>Send an email to contacts</span>
						</a>
						<?php
					}
				}*/


				/*if ($dims->isModuleTypeEnabled('events') && ($currentworkspace['activeevent'] || $currentworkspace['activeeventstep'])){
					$mod = current($dims->getModuleByType('events'));
					?>
					<div style="margin-top:12px;"></div>
					<a href="/admin.php?dims_moduleid=<?php echo $mod['instanceid'];?>&dims_desktop=block&admin.php?dims_mainmenu=events&dims_action=public&action=add_evt&ssubmenu=11&type=<?php echo dims_const::_PLANNING_ACTION_EVT;?>&id=0&dims_desktop=block&type_action=_DIMS_EVENT_OPPORTUNITIES">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png">
						<span><?php echo $_SESSION['cste']['ADD_NEW_MISSION']; ?></span>
					</a>

					<a href="/admin.php?dims_moduleid=<?php echo $mod['instanceid'];?>&dims_desktop=block&admin.php?dims_mainmenu=events&dims_action=public&action=add_evt&ssubmenu=11&type=<?php echo dims_const::_PLANNING_ACTION_EVT;?>&id=0&dims_desktop=block&type_action=_DIMS_PLANNING_FAIR">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png">
						<span><?php echo $_SESSION['cste']['ADD_NEW_FAIR']; ?></span>
					</a>

				<?php
				}*/
				?>
			</div>
		<?php
	   }
	   ?>
	</div>
	<div class="zone_result_category_content_droite">
		<?php
		if (isset($_SESSION['desktop']['search']['tags']))
			$results = $search->getResults($category, $_SESSION['desktop']['search']['tags']);//récupération de la liste des résultats
		else
			$results = $search->getResults($category);//récupération de la liste des résultats

		if(count($results)) {
			$pages = $search->getPagination();//récuparation des pages
			if(count($pages)){
				?>
				<div class="paginationsearch">
					<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span>
					<?php
					foreach($pages as $k=>$p){
						if(!empty($p['url'])){
							echo '<a href="'.$p['url'].'" title="'.$p['title'].'">'.$p['label'].'</a>';
						}
						else echo '<span class="current">'.$p['label'].'</span>';
					}
					?>
				</div>
				<div style="clear:both;"></div>
			<?php
			}
			else{
				?>
				<div class="paginationsearch">
					<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span><span class="current">1</span> / 1
				</div>
				<div style="clear:both;"></div>
				<?php
			}
			?>

			<?php
			if(($search->getType() == search::TYPE_SIMPLE_SEARCH || $search->getType() == search::TYPE_ADVANCED_SEARCH) && !empty($_SESSION['dims']['modsearch']['expression'])){
				$pre_words =  array();
				foreach($_SESSION['dims']['modsearch']['expression'] as $word){
					$pre_words['root'][] = $word['word'];
				}
				if(isset($_SESSION['dims']['modsearch']['tabpossible'])){
					foreach($_SESSION['dims']['modsearch']['tabpossible'] as $k => $word){
						foreach($word as $w){
							$pre_words['possible'][] = $w;
						}
					}
				}
			}
			foreach($results as $res){
				switch($res['type']){
					case search::RESULT_TYPE_COMPANY:
						$obj = new tiers();
						$obj->openWithGB($res['record']);
						$tpl = _DESKTOP_TPL_LOCAL_PATH.'/result_objects/result_company.tpl.php';
						break;
					case search::RESULT_TYPE_CONTACT:
						$obj = new contact();
						$obj->openWithGB($res['record']);
						$tpl = _DESKTOP_TPL_LOCAL_PATH.'/result_objects/result_contact.tpl.php';
						break;
					case search::RESULT_TYPE_ACTIVITY:
					case search::RESULT_TYPE_OPPORTUNITY:
					case search::RESULT_TYPE_MISSION:
					case search::RESULT_TYPE_FAIR:
						$obj = new action();
						$obj->openWithGB($res['record']);
						$obj->setLightAttribute('type', $res['type']);
						$tpl = _DESKTOP_TPL_LOCAL_PATH.'/result_objects/result_event.tpl.php';
						break;
					case search::RESULT_TYPE_DOCUMENT:
					case search::RESULT_TYPE_PICTURE:
					case search::RESULT_TYPE_MOVIE:
						$obj = new docfile();
						$obj->openWithGB($res['record']);
						$obj->setLightAttribute('type', $res['type']);
						if(!is_null($res['sentence'] && !empty($res['sentence']))) $obj->setLightAttribute('sentence', $res['sentence']);
						$tpl = _DESKTOP_TPL_LOCAL_PATH.'/result_objects/result_document.tpl.php';
						break;
					case search::RESULT_TYPE_SUIVI:
						$obj = new suivi();
						$obj->openWithGB($res['record']);
						$obj->setLightAttribute('type', $res['type']);
						if(!is_null($res['sentence'] && !empty($res['sentence']))) $obj->setLightAttribute('sentence', $res['sentence']);
						$tpl = _DESKTOP_TPL_LOCAL_PATH.'/result_objects/result_suivi.tpl.php';
						break;
					default:
						break(2);
				}
				if(isset($res['mb_label']) && !empty($res['mb_label'])) $obj->setLightAttribute('extra_label', $res['mb_label']);
				if(isset($res['mb_field']) && !empty($res['mb_field'])) $obj->setLightAttribute('extra_field', $res['mb_field']);
				if(isset($res['advanced_src']) && !empty($res['advanced_src'])) $obj->setLightAttribute('advanced_src', $res['advanced_src']);

				if(($search->getType() == search::TYPE_SIMPLE_SEARCH || $search->getType() == search::TYPE_ADVANCED_SEARCH)&&!empty($pre_words)) $obj->setLightAttribute('prewords', $pre_words);
				$obj->display($tpl);
			}
			if(count($pages)){
			?>
				<div class="paginationsearch">
					<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span>
					<?php
					foreach($pages as $k=>$p){
						if(!empty($p['url'])){
							echo '<a href="'.$p['url'].'" title="'.$p['title'].'">'.$p['label'].'</a>';
						}
						else echo '<span class="current">'.$p['label'].'</span>';
					}
					?>
				</div>
				<div style="clear:both;"></div>
			<?php
			}
			?>
			<div class="selection_actions">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/arrow_ltr.png" alt="<?php echo $_SESSION['cste']['_FOR_SELECTED_ITEM']; ?>" />
				<a href="Javascript: void(0);" onclick="Javascript: if($('.zone_result_category_content_droite .selection_form input:checked').length > 0) selectionFormToSelection($('.zone_result_category_content_droite .selection_form input:checked'));">
					<?php echo $_SESSION['cste']['_ADD_TO_THE_SELECTION']; ?>
				</a>
			</div>
			<div style="clear:both;"></div>
			<?php
		}
		else{
			if($search->getType() == search::TYPE_SIMPLE_SEARCH && isset($_SESSION['dims']['modsearch']['my_real_expression'])){
				?>
				<span class="no_result"><?php echo $label_result; ?> <?php echo $_SESSION['cste']['FOR_YOUR_SEARCH_ON']; ?> <strong><?php echo $_SESSION['dims']['modsearch']['my_real_expression']; ?></strong>
				<?php
				if ($category > 0) {
					?>
					<a href="admin.php?cat=0" title="<?php echo $_SESSION['cste']['SEE_ALL_RESULTS']; ?>"><?php echo $_SESSION['cste']['SEE_ALL_RESULTS']; ?></a>
					<?php
				}
				?>
				</span>
				<br/>
				<?php
				if(count($_SESSION['dims']['modsearch']['tabpossible'])){
					?>
				<div class="possibilities">
					<span class="annonce">
					<?php
						echo $_SESSION['cste']['WOULD_YOU_MEAN'].' ? : ';
					?>
					</span>
					<div class="columns">
						<?php
						foreach($_SESSION['dims']['modsearch']['tabpossible'] as $k => $words){
							?>
							<div class="sub_words">
								<ul>
								<?php
								foreach($words as $word){
									?>
									<li>
										<a href="/admin.php?dims_op=desktopv2&action=search2&replace_position=<?php echo $k;?>&replace_by=<?php echo urlencode($word); ?>"><?php echo $word; ?></a>
									</li>
									<?php
								}
								?>
								</ul>
							</div>
							<?php
						}
						?>
							<div style="clear:both;"></div>
						</div>
				</div>
				<?php
				}
			}
			else {
				?>
				<span class="no_result"><?php echo $label_result; ?> <?php echo $_SESSION['cste']['FOR_YOUR_SEARCH']; ?></span>
				<?php
			}
		}
		?>
	</div>

	<?php
	//ce script permet de redimensionner les avatars des documents de type picture pour être sûr que ça rentre dans du 60x60px
	?>
	<script type="text/javascript">
		function mycarousel_initCallback(carousel)
		{

			// Disable autoscrolling if the user clicks the prev or next button.
			carousel.buttonNext.bind('click', function() {
				carousel.startAuto(0);
			});

			carousel.buttonPrev.bind('click', function() {
				carousel.startAuto(0);
			});

			// Pause autoscrolling if the user moves with the cursor over the clip.
			carousel.clip.hover(function() {
				carousel.stopAuto();
			}, function() {
				carousel.startAuto();
			});

			<?php if($valide_cts == 1) { ?>
				carousel.stopAuto();
			<?php } ?>

		};
		$(document).ready(function() {
				$('#mycarousel').jcarousel({
					auto: 3,
					wrap: 'last',
					initCallback: mycarousel_initCallback

				});


			$('div.carrousel_contacts').fadeIn();
			<?php if($valide_cts == 1) { ?>
				$('#mycarousel').css({'left' : '0px'});
			<?php } ?>
		});


		$(document).ready(function(){
			if($(this).find('div.possibilities')){
				var max = 0;
				$('div.possibilities div.sub_words').each(function(){
					if($(this).height() > max){
						max = $(this).height();
					}
				});
				$('div.possibilities div.sub_words').css('height', max+'px');
			}
		});

		//preloading des images
		adapteImage("img.to_resize", true, 60);//cf include/functions.js


		into_full = false;
		into_selector = false;
		into_divsearch = false;
		from_full = false;
		my_select = '';
		my_full = '';
		my_divsearch = '';
		var full_timeout;

		$('document').ready(function(){

			//adaptation du bloc de prévisu à la hauteur du champ de recherche
			$('div.search_result').each(function(){
				adaptePreviewSelector($(this));
			});



			$('div.search_result').hover(
				function() {
					from_full = false;
					into_divsearch = true;
					my_divsearch = $(this);
					setTimeout(manageMouseInDivSearch, 200);
				},
				function(){
					into_divsearch = false;
					if(!from_full){
						$(this).find('div.preview_selector').fadeOut();
					}
				}
			);


			//gestion du hover sur le bloc de prévisu lui-même
			$('div.preview_selector').hover(
				function (){
					into_selector = true;
					into_divsearch = true;
					from_full = false;
					$(this).find('div.pvw_content img').attr('src','<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/pvw_poignee_hover.png');
					//on masque tous les preview full qui pourraient avoir été ouvert et rapidement ressortis
					var father = $(this).parent();
					var full = father.find('div.full_preview');
					var selector = $(this);
					$('div.full_preview').each(function(){
						if(full.attr('id') != $(this).attr('id')){
							$(this).fadeOut();
						}
					});
					$('div.preview_selector').each(function(){
						if(full.attr('id') != $(this).parent().find('div.full_preview').attr('id')){
							$(this).fadeOut();
						}
					});


					adaptePositionFullPreview(father,full);
					full.fadeIn();
				},
				function(){
					into_selector = false;
					my_select = $(this);//je ne sais pas faire autrement pour passer à setTimeout this en param ...
					setTimeout(manageMouseOutSelector, 200);

				}
			);

			//gestion du hover sur la preview complète
			$('div.full_preview').hover(
				function(){
					from_full = false;
					into_full = true;
					into_divsearch = true;
					clearTimeout(full_timeout);

				},
				function(){
					into_full = false;
					from_full = true;
					into_divsearch = false;
					my_full = $(this);
					full_timeout = setTimeout(manageMouseOutFullPreview, 600);//on met longtemps pour que si le mec par erreur il sort du div et qu'il y revient ça passe
				}
			);

			//gestion de l'affiche de la poignée si on vient tout juste de faire un F5 et qu'on est déjà (souris) dans un résultat de recherche
			$('div.search_result').mousemove(function(){
				from_full = false;
				$(this).trigger('mouseenter');
			});

		});

		$(window).resize(function(){
			$('div.search_result').each(function(){
					adaptePreviewSelector($(this));
					adaptePositionFullPreview($(this), $(this).find('div.full_preview'));
			});
		});

		function manageMouseInDivSearch(){
			if(into_divsearch){
				my_divsearch.find('div.preview_selector').fadeIn();
			}
		}

		function manageMouseOutSelector(){
			if(!into_full){
				my_select.find('div.pvw_content img').attr('src','<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/pvw_poignee_normale.png');
				my_select.parent().find('div.full_preview').fadeOut();
			}
		}

		function manageMouseOutFullPreview(){
			if(!into_selector && !into_full){
				my_full.parent().find('div.preview_selector').fadeOut();
				my_full.fadeOut();
			}
		}

		function adaptePositionFullPreview(father,full){
			var offset = father.offset();
			var position_ref = $(document).scrollTop() + $(window).height() - 20;
			if(offset.top + full.height() > position_ref){
				full.css('top', offset.top - (offset.top+full.height()-position_ref)+'px');
			}
			else full.css('top', (offset.top+10)+'px');
			full.css('left', (offset.left + father.width())+'px');
		}

		function adaptePreviewSelector(search_object){
			var h = search_object.height();
			var offset = search_object.offset();
			search_object.find('div.preview_selector div.pvw_content').css('height', (h-8)+'px');
			search_object.find('div.preview_selector').css('top', (offset.top + 10)+'px');
			search_object.find('div.preview_selector').css('left', (offset.left + search_object.width() - 32)+'px');
			search_object.find('div.preview_selector div.pvw_content img').css('margin-top', ((h-28)/2)+'px');

		}

		function selectionFormToSelection(elemsSet) {
			var elemArray = elemsSet.map(function(i,n) {
				return $(n).val();
			}).get();

			chooseCategSelection(elemArray);
		}
	</script>
<?php
}
else{
	dims_redirect("/admin.php?submenu=". _DESKTOP_V2_DESKTOP ."&force_desktop=1&mode=default");
}
?>
