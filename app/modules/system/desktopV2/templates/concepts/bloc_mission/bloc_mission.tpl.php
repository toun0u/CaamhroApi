<?php
$_SESSION['desktopv2']['concepts']['mission_search'] = dims_load_securvalue('mission_search',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['mission_search']);

// initialisation des filtres
$init_mission_search = dims_load_securvalue('init_mission_search', dims_const::_DIMS_NUM_INPUT, true, true);
if ($init_mission_search) {
	$_SESSION['desktopv2']['concepts']['mission_search'] = '';
}

// texte du champ de recherche
if ($_SESSION['desktopv2']['concepts']['mission_search'] != '') {
	$text_mission_search = $_SESSION['desktopv2']['concepts']['mission_search'];
	$button['class'] = 'searching';
	$button['href'] = '/admin.php?init_mission_search=1';
	$button['onclick'] = '';
}
else {
	$text_mission_search = $_SESSION['cste']['LOOKING_FOR_AN_EVENT_OR_AN_ACTIVITY']. ' ?';
	$button['class'] = '';
	$button['href'] = 'Javascript: void(0);';
	$button['onclick'] = 'Javascript: if($(\'input#bloc_editbox_search_mission\').val() != \''.$text_mission_search.'\') $(this).closest(\'form\').submit();';
}
?>

<div class="bloc_mission">
    <div class="title_bloc_mission"><h2><?php echo $_SESSION['cste']['EVENTS_ACTIVITIES']; ?></h2></div>
    <div class="bloc_zone_search_mission bloc_zone_search">
        <div class="bloc_searchform_mission">
            <form action="admin.php" method="post" name="formsearch" id="bloc_formsearch_mission">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
					$token->field("button_search_y");
					$token->field("mission_search");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
                <span>
                    <input type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left">
                    <input type="text" name="mission_search" id="bloc_editbox_search_mission" class="bloc_editbox_search editbox_search<? if ($button['class'] == 'searching') echo ' working'; ?>" maxlength="80" value="<?php echo htmlspecialchars($text_mission_search); ?>" <? if ($button['class'] != 'searching') echo 'onfocus="Javascript:this.value=\'\'; $(this).addClass(\'working\');"'; ?> onblur="Javascript:if (this.value==''){ $(this).removeClass('working'); this.value='<?php echo htmlspecialchars(addslashes($text_mission_search)); ?>'; }">
                    <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left">

					<a class="<?php echo $button['class']; ?>" href="<?php echo $button['href']; ?>" onclick="<?php echo $button['onclick']; ?>"></a>
                </span>
            </form>
        </div>
    </div>
    <div class="cadre_bloc_mission">
		<?php
		global $lstObj;
		if (isset($lstObj['event'])) {
			foreach ($lstObj['event'] as $event) {
				if ( $event->fields['id_globalobject'] != $this->fields['id_globalobject'] && ($_SESSION['desktopv2']['concepts']['mission_search'] == '' || stristr($event->fields['libelle'], $_SESSION['desktopv2']['concepts']['mission_search']))) {
					$event->setLightAttribute('tiers_id_globalobject', $this->fields['id_globalobject']);
					$event->setLightAttribute('filter_type', 'event');
					$event->setLightAttribute('concept_not_event', $this->getLightAttribute('concept_not_event'));
					$event->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_mission/fiche_bloc_mission.tpl.php');
				}
			}
		}

		if (isset($lstObj['activities'])) {
			$current_date = current(explode(" ", dims_getdatetime()));

			foreach ($lstObj['activities'] as $event) {
				if ( $event->fields['id_globalobject'] != $this->fields['id_globalobject'] && ($_SESSION['desktopv2']['concepts']['mission_search'] == '' || stristr($event->fields['libelle'], $_SESSION['desktopv2']['concepts']['mission_search']))) {
					$event->setLightAttribute('tiers_id_globalobject', $this->fields['id_globalobject']);
					$event->setLightAttribute('filter_type', 'activity');
					$event->setLightAttribute('concept_not_event', $this->getLightAttribute('concept_not_event'));

					if(strcmp($event->fields['datejour'], $current_date) >= 0)
						$event->setLightAttribute('state', action::FUTURE_ACTIVITY);
					else if($event->fields['close'])
						$event->setLightAttribute('state', action::PAST_CLOSED_ACTIVITY);
					else
						$event->setLightAttribute('state', action::PAST_OPEN_ACTIVITY);

					$event->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_mission/fiche_bloc_mission.tpl.php');
				}
			}
		}

		if (isset($lstObj['opportunities'])) {
			$current_date = current(explode(" ", dims_getdatetime()));

			foreach ($lstObj['opportunities'] as $event) {
				if ( $event->fields['id_globalobject'] != $this->fields['id_globalobject'] && ($_SESSION['desktopv2']['concepts']['mission_search'] == '' || stristr($event->fields['libelle'], $_SESSION['desktopv2']['concepts']['mission_search']))) {
					$event->setLightAttribute('tiers_id_globalobject', $this->fields['id_globalobject']);
					$event->setLightAttribute('filter_type', 'opportunity');
					$event->setLightAttribute('concept_not_event', $this->getLightAttribute('concept_not_event'));

					if(strcmp($event->fields['datejour'], $current_date) >= 0)
						$event->setLightAttribute('state', action::FUTURE_ACTIVITY);
					else if($event->fields['close'])
						$event->setLightAttribute('state', action::PAST_CLOSED_ACTIVITY);
					else
						$event->setLightAttribute('state', action::PAST_OPEN_ACTIVITY);

					$event->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_mission/fiche_bloc_mission.tpl.php');
				}
			}
		}
		?>
    </div>
    <div class="cadre_bloc_mission_bas"></div>
</div>
