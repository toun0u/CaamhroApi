<?php
$filter_type = $this->getLightAttribute('filter_type');

if($filter_type=='opportunity' && $this->fields['datejour'] == '0000-00-00'){
	require_once DIMS_APP_PATH.'modules/system/class_search.php';
	$matrix = new search();
	$my_context = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], array(), array($this->fields['id_globalobject']), array(), array(),array(), array(), array(), array(), array());

	$distrib = $my_context['distribution'];
	if(isset($distrib)){
		if(isset($distrib['opportunities'][$this->fields['id_globalobject']]['ref']) && !empty($distrib['opportunities'][$this->fields['id_globalobject']]['ref'])){
			$ref = new action();
			$ref->openWithGB($distrib['opportunities'][$this->fields['id_globalobject']]['ref']);
			if(!$ref->isNew()){
				$this->fields['datejour'] = $ref->fields['datejour'];
				$this->save();//à priori c'est un hack pour corriger un truc improbable, autant qu'on n'y passe rarement en sauvegardant tout ça
			}
		}
	}
}

$dateJour = explode('-', $this->fields['datejour']);
if ($dateJour[2] > 0)
	$numDay = $dateJour[2];
else
	$numDay = '-';
if ($dateJour[1] > 0)
	$monthYear = date('M. Y', mktime(0, 0, 0, $dateJour[1], $dateJour[2], $dateJour[0]));
else
	$monthYear = $dateJour[0];

if ($filter_type == 'event') {
	$image = _DESKTOP_TPL_PATH.'/gfx/common/event40.png';
	$ico_mini = _DESKTOP_TPL_PATH.'/gfx/common/event_mini.png';
    $detach_title = $_SESSION['cste']['DETACH_THIS_EVENT'];
    $detach_confirm = $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DETACH_THIS_EVENT'];
    $detach_link_type = dims_const::_SYSTEM_OBJECT_EVENT;
}
else {
	$image = _DESKTOP_TPL_PATH.'/gfx/common/opportunity40.png';
	$ico_mini = _DESKTOP_TPL_PATH.'/gfx/common/opportunity_red_picto.png';
    $detach_title = $_SESSION['cste']['DETACH_THIS_OPPORTUNITY'];
    $detach_confirm = $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DETACH_THIS_OPPORTUNITY'];
    $detach_link_type = dims_const::_SYSTEM_OBJECT_OPPORTUNITY;
}

if ($this->fields['banner_path'] != '' && file_exists($this->fields['banner_path'])) {
    $image = $this->fields['banner_path'];
}
?>


<table <?php
	$state = $this->getLightAttribute('state');
	if(!empty($state)) {
		echo "class='";

		switch($state) {
			case action::FUTURE_ACTIVITY:
				echo "future";
				break;

			case action::PAST_CLOSED_ACTIVITY:
				echo "past_closed";
				break;

			case action::PAST_OPEN_ACTIVITY:
				echo "past";
				break;
		}

		echo "'";
	} ?> cellspacing="10" cellpadding="0">
    <tbody>
        <tr>
            <td style="width:61px">
                <img style="width:45px" src="<?php echo $image; ?>">
            </td>
            <td>
                <img src="<?php echo $ico_mini; ?>">
				<?php
				$url="";
					switch($this->getLightAttribute('filter_type')) {
						case "activity":
							$type = dims_const::_SYSTEM_OBJECT_ACTIVITY;
							$url="admin.php?submenu="._DESKTOP_V2_CONCEPTS;"&id=".$this->fields['id']."&type=".$type."&init_filters=1&from=desktop";
							break;
						case "event":
							$type = dims_const::_SYSTEM_OBJECT_EVENT;
							$url="admin.php?submenu="._DESKTOP_V2_CONCEPTS;"&id=".$this->fields['id']."&type=".$type."&init_filters=1&from=desktop";
						case "opportunity":
							$type = dims_const::_SYSTEM_OBJECT_OPPORTUNITY;
							$url="admin.php?mode=leads&action=view&lead_id=".$this->fields['id']."&init_filters=1&from=desktop";
							break;
					}
				?>
                <a href="<?php echo $url;?>"><span class="title_event_mission"><?php echo $this->fields['libelle']; ?></span></a>
            </td>
			<?php
				require_once DIMS_APP_PATH.'modules/system/class_search.php';
				$matrix = new search();
				$my_context = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], array(), array($this->fields['id_globalobject']), array(), array(),array(), array(), array(), array(), array(), array());
				//dims_print_r($my_context);
				$distrib = $my_context['distribution'];
				//dims_print_r($distrib);

				if(isset($distrib['tiers']) && !empty($distrib['tiers'])) {
					$total = count($distrib['tiers']);
					$tiers = array_keys($distrib['tiers']);

					if($total > 0) {
			?>
			<td>
				<a class="see_more" href="javascript:void(0);" onclick="javascript:toggle_menu('tiers_<?php echo $this->fields['id_globalobject'];?>', $(this).children().first());">
					<img border="0" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png">
					&nbsp;<?php echo $_SESSION['cste']['_DIMS_PARTICIP'];?>
					<div id="tiers_<?php echo $this->fields['id_globalobject']; ?>" <?php if (isset($_SESSION['desktopV2']['content_content']['zone_participants']) && $_SESSION['desktopV2']['content_content']['zone_participants'] == 0) echo 'style="display:none;'; ?>>
						<ul>
							<?php
								for($i=0; $i < $total; $i++) {
									if($tiers[$i] != $this->getLightAttribute("tiers_id_globalobject")) {
										$t = new tiers();
										$t->openWithGB($tiers[$i]);
										?>
										<li>
										<?php
											echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/company_picto.png"/><a href="/admin.php?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$t->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_TIERS.'&init_filters=1&from=desktop">'.$t->fields['intitule'].'</a> ';
										?>
										</li>
										<?php
									}
								}
							?>
						</ul>
					</div>
				</a>
			</td>
			<?php
					}
				}

				if(isset($distrib['contacts']) && !empty($distrib['contacts'])) {
					$total = count($distrib['contacts']);
					$contacts = array_keys($distrib['contacts']);

					if($total > 0) {
			?>
			<td>
				<a class="see_more" href="javascript:void(0);" onclick="javascript:toggle_menu('contact_<?php echo $this->fields['id_globalobject'];?>', $(this).children().first());">
					<img border="0" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png">
					&nbsp;<?php echo $_SESSION['cste']['_DIMS_LABEL_CONTACTS'];?>
					<div id="contact_<?php echo $this->fields['id_globalobject']; ?>" <?php if (isset($_SESSION['desktopV2']['content_content']['zone_participants']) && $_SESSION['desktopV2']['content_content']['zone_participants'] == 0) echo 'style="display:none;'; ?>>
						<ul>
							<?php
								for($i=0; $i < $total; $i++) {
									if($contacts[$i] != $this->getLightAttribute("tiers_id_globalobject")) {
										$c = new contact();
										$c->openWithGB($contacts[$i]);
										?>
										<li>
										<?php
											echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/company_picto.png"/><a href="/admin.php?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$c->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1&from=desktop">'.$c->fields['firstname'].' '.$c->fields['lastname'].'</a> ';
										?>
										</li>
										<?php
									}
								}
							?>
						</ul>
					</div>
				</a>
			</td>
			<?php
					}
				}

				if(isset($distrib['docs']) && !empty($distrib['docs'])) {
					$total = count($distrib['docs']);
					$docs = array_keys($distrib['docs']);

					if($total > 0) {
			?>
			<td>
				<a class="see_more" href="javascript:void(0);" onclick="javascript:toggle_menu('enclosure_<?php echo $this->fields['id_globalobject'];?>', $(this).children().first());">
					<img border="0" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png">
					&nbsp;<?php echo $_SESSION['cste']['ENCLOSURES'];?>
					<div id="enclosure_<?php echo $this->fields['id_globalobject']; ?>" <?php if (isset($_SESSION['desktopV2']['content_content']['zone_participants']) && $_SESSION['desktopV2']['content_content']['zone_participants'] == 0) echo 'style="display:none;'; ?>>
						<ul>
							<?php
								for($i=0; $i < $total; $i++) {
									if($docs[$i] != $this->getLightAttribute("tiers_id_globalobject")) {
										$d = new docfile();
										$d->openWithGB($docs[$i]);
										?>
										<li>
										<?php
											echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/company_picto.png"/><a href="admin.php?dims_op=doc_file_download&docfile_md5id='.$d->fields['md5id'].'">'.$d->fields['name'].'</a> ';
										?>
										</li>
										<?php
									}
								}
							?>
						</ul>
					</div>
				</a>
			</td>
			<?php
					}
				}
			?>
            <td class="filter" style="float:none;vertical-align:middle;">
                <img class="perform_cube" onclick="javascript:document.location.href='/admin.php?action=add_filter&filter_type=<?php echo $filter_type; ?>&filter_value=<?php echo $this->fields['id_globalobject']; ?>';" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_cube.png" style="float:left;cursor:pointer;" />
            </td>
            <?php
            if ($this->getLightAttribute('concept_not_event')) {
                ?>
                <td class="filter" style="float:none;vertical-align:middle;">
                    <a title="<?php echo $detach_title; ?>" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?action=del_concepts_link&link_type=<?php echo $detach_link_type; ?>&id=<?php echo $this->fields['id_globalobject']; ?>', '<?php echo $detach_confirm; ?>');">
                        <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/detach.png" />
                    </a>
                </td>
                <?php
            }
            ?>
            <td>
                <div class="bloc_ligne calendar">
                    <table class="ro_calendar">
                        <tbody>
                            <tr>
                                <td class="bloc_calendar">
                                    <table cellspacing="0" cellpadding="0" width="100%">
                                        <?
                                        $date = explode('-',$this->fields['datejour']);
                                        ?>
                                        <tbody>
                                            <tr>
                                                <td align="center" class="calendar_top"><?php echo $monthYear; ?></td>
                                            </tr>
                                            <tr>
                                                <td align="center" class="calendar_bot"><?php echo $numDay; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
			<td>
			<?php
				if($this->fields['close']) {
					echo "Fermé";
				} else {
					?>
						<div class="close">
							<a href="admin.php?action=close_activity&id=<?php echo $this->fields['id']; ?>&submenu=<?php echo _DESKTOP_V2_CONCEPTS; ?>">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png"/>
								<span><? echo $_SESSION['cste']['_DIMS_CLOSE']; ?></span>
							</a>
						</div>
					<?php
				}
			?>
			</td>
        </tr>
    </tbody>
</table>
