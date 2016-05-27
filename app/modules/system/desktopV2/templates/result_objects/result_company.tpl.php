<div class="search_result">
	<div class="selection_form">
		<input type="checkbox" name="selection[]" value="<?php echo $this->fields['id_globalobject']; ?>" />
	</div>
	<div class="add_to_context">
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=company&val=<?php echo $this->fields['id_globalobject'];?>">
			<?php
			if(!isset($_SESSION['dims']['advanced_search']['filters']['companies'][$this->fields['id_globalobject']])){
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/fleche_result2.png">
				<?php
			}
			else{
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/already_in_search2.png">
				<?php
			}
			?>
		</a>
	</div>
	<div class="avatar">
		<?php
		global $_DIMS;
		$file = $this->getPhotoPath(60);//real_path
		if(file_exists($file)){
			?>
			<img class="picture" src="<?php echo $this->getPhotoWebPath(60); ?>">
			<?php
		}
		else{
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_default_search.png">
			<?php
		}

		?>
	</div>
	<div class="detail">
		<div class="title">
			<a href="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=company&action=show&id=<? echo $this->fields['id']; ?>" class="title_result">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png">
				<span><?php echo $this->getIntitule(); ?></span>
			</a>
		</div>
		<div class="context">
			<?php
			$field = $this->getLightAttribute('extra_field');
			$label = $this->getLightAttribute('extra_label');
			if(	isset($field)
			&&	isset($label)
			&&	isset($this->fields[$field])){
				$value = strip_tags($this->fields[$field]);
				$prewords = $this->getLightAttribute('prewords');
				if(isset($prewords['possible']))//à priori root est quoi qu'il arrive set sinon y'aurait pas de résultat
					$words = array_merge($prewords['root'], $prewords['possible']);
				else $words = $prewords['root'];
				echo '<strong>'. ucfirst(isset($_DIMS['cste'][$label])?$_DIMS['cste'][$label]:$label) .'</strong> : '.dims_getManifiedWords($value, $words, '<span class="founded_result">', '</span>');
			}
			?>
		</div>
		<?php
		$advanced_src = $this->getLightAttribute('advanced_src');
		if(isset($advanced_src) && !empty($advanced_src)){//si on a un lien avec une activité
			require_once DIMS_APP_PATH . '/modules/system/class_action.php';
			$action = new action();
			$action->openWithGB($advanced_src);
			if(!$action->isNew()){
				//traitement spécifique sur les dates qui sont stockée au format américain yyyy-mm-dd
				$deb = explode('-',$action->fields['datejour']);
				$one_day = true;
				$str_fin = '';
				$str_deb = '';
				$date_compare = '';
				if($deb[2] != 0){
					$str_deb .= $deb[2].'/';
					$date_compare = $deb[2];
				}
				if($deb[1] != 0){
					$str_deb .= $deb[1].'/';
					$date_compare = $deb[1] . $date_compare;
				}
				if($deb[0] != 0){
					$str_deb .= $deb[0];
					$date_compare = $deb[0] . $date_compare;
				}

				if($action->fields['datejour'] != $action->fields['datefin'] && $action->fields['datefin']!= '0000-00-00'){
					$one_day = false;
					$fin = explode('-',$action->fields['datefin']);
					$str_fin = ' '.$_SESSION['cste']['DATE_TO_THE'].' ';
					if($fin[2] != 0){
						$str_fin .= $fin[2].'/';
					}
					if($fin[1] != 0){
						$str_fin .= $fin[1].'/';
					}
					if($fin[0] != 0){
						$str_fin .= $fin[0];
					}
				}

				$past_verb = true;
				if(date('Ymd') < $date_compare) $past_verb = false;
				$type_action = $action->getSearchableType();
				?>
				<div class="advanced_src">
				<?php
				$ref_system_object = dims_const::_SYSTEM_OBJECT_ACTION;
				if($type_action==search::RESULT_TYPE_ACTIVITY){
					$ref_system_object = dims_const::_SYSTEM_OBJECT_ACTIVITY;
					$label = $_SESSION['cste']['THE_ACTIVITY'];
					?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_red_picto.png" title="<?php echo ucfirst($_SESSION['cste']['ACTIVITY']); ?>"/>
					<?php
				}
				else{
					if($type_action == search::RESULT_TYPE_MISSION){
						$title = $_SESSION['cste']['_DIMS_IMPORT_LABEL_MISSION'];
						$label = $_SESSION['cste']['THE_MISSION'];
					}
					else{
						$title = ucfirst($_SESSION['cste']['SIMPLE_FAIR']);
						$label = $_SESSION['cste']['THE_FAIR'];
					}
					?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/event_mini.png" title="<?php echo $title; ?>"/>
					<?php
				}
				if($past_verb){
					$verb = $_SESSION['cste']['MET_MINUSCULE'];
				}
				else $verb = $_SESSION['cste']['WILL_BE_MET'];
				?>
				<?php echo $verb.' '.$_SESSION['cste']['DURING'].' '.$label;?> <a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $action->fields['id']; ?>&type=<? echo $ref_system_object; ?>&init_filters=1&from=search"><?php echo $action->fields['libelle']; ?></a><?php echo ' '.(($one_day)?' '. $_SESSION['cste']['SINGLE_THE'].' ':' '.$_SESSION['cste']['DATE_FROM']).' '.$str_deb.$str_fin;?>
				</div>
			<?php
			}
		}
		?>
		<div class="tags_elem_search">
			<?php
			$tags = $this->getMyTags(tag::TYPE_DEFAULT);
			if(count($tags)) {
				$i = 0;
				foreach($tags as $tag) {
					$tag->display(_DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
					if($i < count($tags)-1) echo '<div class="tag_separator"> | </div>';
					$i++;
				}
			}
			?>
		</div>
		<?php
		if (dims::getInstance()->isModuleTypeEnabled('catalogue')) {
			include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';
			$catalogueclient = current(client::find_by(array('tiers_id' => $this->getId())));
			if(!empty($catalogueclient)) {
				?>
				<div class="customerCard">
					<a href="/admin.php?dims_mainmenu=catalogue&c=clients&a=show&id=<?= $catalogueclient->getId(); ?>">
						<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/fleche_result.png" alt="<?= dims_constant::getVal('CUSTOMER_CARD'); ?>" title="<?= dims_constant::getVal('CUSTOMER_CARD'); ?>" />
						<?= dims_constant::getVal('CUSTOMER_CARD'); ?>
					</a>
					<a href="/admin.php?dims_mainmenu=catalogue&c=clients&a=show&id=<?= $catalogueclient->getId(); ?>&sc=quotations">
						<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/quotation.png" alt="<?= dims_constant::getVal('QUOTATION'); ?>" title="<?= dims_constant::getVal('QUOTATION'); ?>" />
						<?= dims_constant::getVal('QUOTATION'); ?>
					</a>
					<a href="/admin.php?dims_mainmenu=catalogue&c=clients&a=show&id=<?= $catalogueclient->getId(); ?>&sc=quotations&sa=new">
						<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/quotation_add.png" alt="<?= dims_constant::getVal('ADD_QUOTATION'); ?>" title="<?= dims_constant::getVal('ADD_QUOTATION'); ?>" />
						<?= dims_constant::getVal('ADD_QUOTATION'); ?>
					</a>
				</div>
                <div id="listuseraccount" style="clear:both;">
                    Comptes rattachés :
				<?php

                // on construit la liste des comptes pouvant être utilisés
                $elem = $catalogueclient->getService();
                $users=$elem->getusers(true);
                if (sizeof($users)>0) {
                    echo "<ul>";
                    foreach ($users as $user) {
                        echo "<li>".$user->getFirstname()." ".$user->getLastname();

                        $useLink = '/index.php?dims_url=' . urldecode(base64_encode('dims_login=' . $user->fields['login'] . '&dims_password=' . $user->fields['password'] . '&already_hashed=1'));
                        ?>
                        <a style="text-decoration:none;" href="<?= $useLink; ?>">
                            <img src="/common/modules/catalogue/admin/views/gfx/utiliser_compte.png"  alt="" title="" />
                        </a>
                        </li>
                        <?php

                    }
                    echo "</ul>";
                }
                ?>
                </div>
                <?php
			}
		}
		?>
	</div>
	<div class="preview_selector" style="display: none;">
		<div class="pvw_top"></div>
		<div class="pvw_content">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/pvw_poignee_normale.png"/>
		</div>
		<div class="pvw_bottom"></div>
	</div>
	<div id="full_ti_<?php echo $this->fields['id_globalobject']; ?>" class="full_preview" style="display: none;">
		<h3>
		<?php
		$file = $this->getPhotoPath(40);
		if(file_exists($file)){
			?>
			<img class="picture" src="<?php echo $this->getPhotoWebPath(40); ?>">
			<?php
		}
		else{
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company40.png">
			<?php
		}
		echo $this->fields['intitule']; ?>
		</h3>
		<table cellspacing="0" cellpadding="3" class="contact_fiche_details">
			<tbody>
				<tr>
					<td class="title_contact_fiche_gras">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
					</td>
					<td class="title_desc_rouge">
						<?php
							if(isset($this->fields['mel']) && !empty($this->fields['mel']) && $this->fields['mel'] != 'NULL'){
							?>
								<a href="mailto:<?php echo $this->fields['mel'];?>"><? echo $this->fields['mel']; ?></a>
							<?php
							}
						?>
					</td>
				</tr>
				<tr>
					<td class="title_contact_fiche_gras">
						<?php echo $_SESSION['cste']['PHONE_NUMBER']; ?>
					</td>
					<td  style="vertical-align:top;">
						<? echo $this->fields['telephone']; ?>
					</td>
				</tr>
				<tr>
					<td class="title_contact_fiche_gras" style="vertical-align:top;">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?>
					</td>
					<td>
						<?php
						require_once DIMS_APP_PATH.'modules/system/class_address.php';
						$addresses = address::getAddressesFromGo($this->get('id_globalobject'));
						if(count($addresses)){
							$add = current($addresses);
							echo $add->get('address')."<br />";
							if(trim($add->get('address2')) != '')
								echo $add->get('address2')."<br />";
							echo $add->get('postalcode')." ";
							$city = $add->getCity();
							if(!empty($city) && !$city->isnew())
								echo " ".$city->get('label');
							if($add->get('bp') != ''){
								echo " ".$add->get('bp');
							}
							$country = $add->getCountry();
							echo " (".$country->get('printable_name').")";
						}
						?>
					</td>
				</tr>
				<?php
				//addCommentAB
				if ($this->fields['commentaire'] != '') {
					?>
					<tr>
						<td class="title_contact_fiche_gras">
							<?php $_SESSION['cste']['_DIMS_COMMENTS']; ?>
						</td>
						<td>
							<i><? echo $this->fields['commentaire']; ?></i>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<div class="pvw_footer_actions">
			<a href="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=company&action=show&id=<?= $this->get('id'); ?>" style="float:left;">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/focus_on_activity16.png" border="0" />
				<span><?php echo $_SESSION['cste']['_DIMS_FOCUS_ON_ACTIVITY']; ?></span>
			</a>

			<a href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo addslashes($_SESSION['cste']['SURE_DELETE_COMPANY']);?>')) document.location.href = '?dims_op=desktopv2&action=delete_concept&type=tiers&go=<?php echo $this->fields['id_globalobject']; ?>&desktop=0';" style="float: right;">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" border="0" />
				<span><?php echo $_SESSION['cste']['DELETE_COMPANY']; ?></span>
			</a>
			<div style="clear:both"></div>
		</div>
	</div>
</div>
