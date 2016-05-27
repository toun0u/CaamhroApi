<?php
$styleaddcontent_gauche='';
if (isset($mode) && in_array($mode, array('newsletters','contact','company','address'))) $styleaddcontent_gauche='style="width:100%;"';
?>
<div class="content_gauche" <?php echo $styleaddcontent_gauche;?>>
	<div class="zone_search">
		<span class="house">
			<a href="/admin.php?submenu=<? echo _DESKTOP_V2_DESKTOP; ?>&force_desktop=1&mode=default">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_house.png" />
			</a>
		</span>
		<span class="text-search">
			<?php echo $_SESSION['cste']['SEARCH_ON']; ?>
		</span>
		<?php
		if (file_exists(_DIMS_PATHDATA."logo/home_logo.png")){
				$logo_home=_DIMS_WEBPATHDATA."logo/home_logo.png";
				?><img class="home_logo" src="<?php echo $logo_home; ?>" /><?php
			}
		?>
		<div class="searchform">
			<form action="/admin.php?dims_op=desktopv2&action=search2" method="post" name="formsearch" id="desktop_formsearch">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("desktop_editbox_search");
					$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
					$token->field("button_search_y");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<span>
					<input onclick="javascript:if ($('#desktop_editbox_search').val() != '<?php echo $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS'];?>') document.formsearch.submit(); else return false;" type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left;" />
					<input type="text" name="desktop_editbox_search"
						   class="editbox_search <?php if (!empty($_SESSION['dims']['modsearch']['my_real_expression']))echo 'working'; ?>"
						   id="desktop_editbox_search"
						   maxlength="80"
						   value="<?php if(!empty($_SESSION['dims']['modsearch']['my_real_expression']))echo htmlspecialchars($_SESSION['dims']['modsearch']['my_real_expression']); else echo $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS']; ?>"
						   onfocus="Javascript:$(this).addClass('working');<?php if(empty($_SESSION['dims']['modsearch']['my_real_expression']))echo "this.value=''";?>"
						   onblur="Javascript:<?php if(empty($_SESSION['dims']['modsearch']['my_real_expression'])) echo "if($(this).hasClass('working') && $(this).val()=='')$(this).removeClass('working');";?> if (this.value=='')this.value='<?php echo $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS'];?>';">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;" />
					<?php if(!empty($_SESSION['dims']['modsearch']['my_real_expression'])){ ?>
						<a class="discard_seach" href="<?php echo $dims->getScriptEnv().'?force_desktop=1'; ?>" title="Discard the search">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
						</a>
					<?php } ?>
					<input onclick="javascript:if ($('#desktop_editbox_search').val() != '<?php echo $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS'];?>') document.formsearch.submit();" style="margin-top: 4px; margin-left: 5px;" type="button" value="<?php echo $_SESSION['cste']['_SEARCH']; ?>" />
					<?php if(isset($_SESSION['dims']['tag_search']) && count($_SESSION['dims']['tag_search'])){ ?>
					<input class="search-tag" type="button" value="<?= $_SESSION['cste']['_SEARCH_WITH_TAGS']; ?>" />
					<?php } ?>
				</span>
			</form>
			<div class="selected-tag" style="clear:both;">
				<?php
				if(isset($_SESSION['dims']['tag_search'])){
					$tags = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$_SESSION['dims']['tag_search']));
					foreach($tags as $t){
						echo '<span class="tag selected" dims-data-value="'.$t->get('id').'">'.$t->get('tag').'</span>';
					}
				}
				?>
			</div>
		</div>
	</div>

	<?php
	if ($mode != 'appointment_offer' && $mode != 'planning'){
		unset($_SESSION['desktopv2']['appointment_offer']);
	}

	switch($mode){
		default :
		case 'default' :
			?>
			<div class="cadre_advanced_search" style="display:<?php echo (!(isset($map) && $map) && ((!isset($_SESSION['dims']['advanced_search']['keep_opened']) || (isset($_SESSION['dims']['advanced_search']['keep_opened']) && $_SESSION['dims']['advanced_search']['keep_opened'])) && empty($_SESSION['dims']['modsearch']['my_real_expression'])))?'block':'none'; ?>;">
				<?php include _DESKTOP_TPL_LOCAL_PATH.'/advanced_search/advanced_search.tpl.php'; ?>
			</div>
			<?php
			if ($force_desktop || (empty($_SESSION['dims']['search']['current_search'] ) && (!isset($search) || empty($search))) ){
			?>
				<!--<div class="filters desktop">
				<?php
				if(!isset($_SESSION['dims']['desktopfilters']['expand_to_all_workspace'])) $_SESSION['dims']['desktopfilters']['expand_to_all_workspace'] = false;
				if($_SESSION['dims']['desktopfilters']['expand_to_all_workspace'])
					$checked='checked="checked"';
				else $checked = '';
				?>
					<input type="checkbox" onchange="javascript:changeExpandAllWorkspaces();" name="expand_to_all_workspace" id="expand_to_all_workspace" <?php echo $checked; ?>/>
					<label for="expand_to_all_workspace"><?php echo $_SESSION['cste']['EXPAND_TO_ALL_WORKSPACES']; ?></label>
				</div>-->
				<div class="recent_companies_block">
					<div class="bloc_gauche">
						<?php
						/*if (!defined('_ACTIVE_OPPORTUNITY') || _ACTIVE_OPPORTUNITY) {
							include _DESKTOP_TPL_LOCAL_PATH.'/recent_opportunities/recent_opportunities.tpl.php';
						}

						include _DESKTOP_TPL_LOCAL_PATH.'/recent_opportunities/future_events.tpl.php';
						*/
						include _DESKTOP_TPL_LOCAL_PATH.'/todos/todos_wall.tpl.php';

                        if (!defined('_ACTIVE_OPPORTUNITY') || _ACTIVE_OPPORTUNITY) {
                        //    include _DESKTOP_TPL_LOCAL_PATH.'/recent_opportunities/recent_opportunities.tpl.php';
                        }
						//include _DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/shared/missing_infos.tpl.php';

						if (!defined('_ACTIVE_ACTIVITY') || _ACTIVE_ACTIVITY) {
							include _DESKTOP_TPL_LOCAL_PATH.'/recent_activities/recent_activities.tpl.php';
						}
						?>
					</div>
					<div class="bloc_droite">
						<?php include _DESKTOP_TPL_LOCAL_PATH.'/contacts_recently/contacts_recently.tpl.php'; ?>
						<?php include _DESKTOP_TPL_LOCAL_PATH.'/companies_recently/companies_recently.tpl.php'; ?>
					</div>
				</div>
			<?php
			}
			else if($search == 1 || !empty($_SESSION['dims']['search']['current_search'] )){
			?>
				<div class="zone_result_category_content">
					<?php include _DESKTOP_TPL_LOCAL_PATH.'/result_search/result_search.tpl.php'; ?>
				</div>
			<?php
			}
			break;
		case 'activity' :
			?>
			<div class="zone_new_activity">
				<?php
				require_once DIMS_APP_PATH.'/modules/system/activity/public.php';
				if (isset($activity)) {
					switch($action) {
						case 'view':
							$activity->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/view_activity.tpl.php');
							break;
						case 'edit':
							$activity->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/edit_activity.tpl.php');
							break;
						case 'manage':
							$activity->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/manage_activities.tpl.php');
							break;
					}
				}
				else {
					dims_redirect("/admin.php?submenu="._DESKTOP_V2_DESKTOP."&mode=default");
				}
				?>
			</div>
			<?php
			break;
		case 'leads' :
			?>
			<div class="zone_new_lead">
				<?php
				require_once DIMS_APP_PATH.'/modules/system/leads/public.php';
				if (isset($lead)) {
					switch($action) {
						case 'view':
							$lead->display(_DESKTOP_TPL_LOCAL_PATH.'/leads/view_lead.tpl.php');
							break;
						case 'edit':
							$lead->display(_DESKTOP_TPL_LOCAL_PATH.'/leads/edit_lead.tpl.php');
							break;
						case 'manage':
							$lead->display(_DESKTOP_TPL_LOCAL_PATH.'/leads/manage_leads.tpl.php');
							break;
					}
				}
				else
					dims_redirect("/admin.php?submenu="._DESKTOP_V2_DESKTOP."&mode=default");
				?>
			</div>
			<?php
			break;
		case 'opportunity':
			?>
			<div class="zone_new_opportunity">
				<?php
				require_once DIMS_APP_PATH.'/modules/system/opportunity/public.php';
				if (isset($opp))
					$opp->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_opportunity.tpl.php');
				else
					dims_redirect("/admin.php?submenu="._DESKTOP_V2_DESKTOP."&mode=default");
				?>
			</div>
			<?php
			break;
		/*case 'address_book':
			?>
			<div class="zone_address_book">
				<?php include _DESKTOP_TPL_LOCAL_PATH.'/address_book/address_book.tpl.php'; ?>
			</div>
			<?php
			break;*/
		case 'planning':
			include DIMS_APP_PATH.'modules/system/class_planning.php';
			?>
			<div class="zone_planning">
				<?php include _DESKTOP_TPL_LOCAL_PATH.'/planning/public_planning.php'; ?>
			</div>
			<?php
			break;
		case 'refreshplanning':
			unset($_SESSION['desktopv2']['appointment_offer']);
			dims_redirect(dims::getInstance()->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&mode=planning');
			break;
		case 'business':
			?>
			<div class="zone_address_book">
				<?php include _DESKTOP_TPL_LOCAL_PATH.'/business/business.tpl.php'; ?>
			</div>
			<?php
			break;
		case 'import_data':
			?>
			<div class="zone_address_book">
				<?php include _DESKTOP_TPL_LOCAL_PATH.'/import_data/controller.php'; ?>
			</div>
			<?php
			break;
		case 'newsletters':
			?>
			<div class="zone_newsletters">
				<?php include _DESKTOP_TPL_LOCAL_PATH.'/newsletters/controller.php'; ?>
			</div>
			<?php
			break;
		case 'contact':
			?>
			<div class="zone_address_book">
			<?php
				require_once _DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/contact/controller.php';
			?>
			</div>
			<?php
			break;
		case 'company':
			?>
			<div class="zone_address_book">
			<?php
				require_once _DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/tiers/controller.php';
			?>
			</div>
			<?php
			break;
		case 'address':
			?>
			<div class="zone_address_book">
			<?php
				require_once _DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/controller.php';
			?>
			</div>
			<?php
			break;
		case 'doc':
			?>
			<div class="zone_address_book">
			<?php
				require_once _DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/doc/controller.php';
			?>
			</div>
			<?php
			break;
		case 'suivi':
			if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
				?>
				<div class="zone_address_book">
				<?php
					require_once _DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/suivi/controller.php';
				?>
				</div>
				<?php
			}
			break;
		case 'appointment_offer':
			?>
			<div class="zone_new_activity">
				<?php
				require_once DIMS_APP_PATH.'/modules/system/appointment_offer/public.php';
				if (isset($app_offer)) {
					switch($action) {
						case 'edit':
							$app_offer->display(_DESKTOP_TPL_LOCAL_PATH.'/appointment_offer/edit_appointment_offer.tpl.php');
							break;
						default:
						case 'manage':
							$app_offer->display(_DESKTOP_TPL_LOCAL_PATH.'/appointment_offer/manage_appointment_offer.tpl.php');
							break;
					}
				}
				else {
					dims_redirect("/admin.php?submenu="._DESKTOP_V2_DESKTOP."&mode=default");
				}
				?>
			</div>
			<?php
			break;
		case 'admin':
			if($dims->isAdmin() || $dims->isManager()){
				?>
				<div class="zone_address_book">
				<?php
					require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/controller.php';
				?>
				</div>
				<?php
			}else{
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1");
			}
			break;
	}
	?>
</div>

<script type="text/javascript">
	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
	function preload(arrayOfImages) {
		$(arrayOfImages).each(function(){
			$('<img/>')[0].src = this;
		});
	}
	$("select#map_years").change(function(){
		if($(this).val() != -1){
			renderGeographic('y'+$(this).val());
		}
		else renderGeographic('d15');
	});
	function changeExpandAllWorkspaces() {
		dims_xmlhttprequest('/admin.php', 'dims_op=desktopv2&action=expand_to_all_workspaces&expand_to_all_workspace='+$('#expand_to_all_workspace').is('checked')?'checked':'');
		document.location.href='admin.php';
	}
	$(document).ready(function(){
		preload([
			'./common/img/loading.gif'
		]);
		$(document).ajaxStart(function(){
			var div = '<div id="loading_ajax"><img src="./common/img/loading.gif" /></div>';
			if($('body div#loading_ajax').length)
				div = '<div id="loading_ajax" style="display:none;position:absolute;left:-1000px;top:-1000px"><img src="./common/img/loading.gif" /></div>';
			$("body").append(div);
		}).ajaxStop(function(){
			$('body div#loading_ajax:first').remove();
			if($('body div#loading_ajax').length > 1)
				$('body div#loading_ajax:first').attr("style","");
		}).ajaxError(function(){
			$('body div#loading_ajax:first').remove();
			if($('body div#loading_ajax').length > 1)
				$('body div#loading_ajax:first').attr("style","");
		});
		$(document).delegate('span.tag','click',function(){
			if($(this).attr('dims-data-value') != undefined){
				if($(this).hasClass('selected')){
					$('span.tag[dims-data-value="'+$(this).attr('dims-data-value')+'"]').removeClass('selected');
					$('div.zone_search div.searchform div.selected-tag span.tag[dims-data-value="'+$(this).attr('dims-data-value')+'"]').remove();
				}else{
					$('span.tag[dims-data-value="'+$(this).attr('dims-data-value')+'"]').addClass('selected');
					$(this).clone().appendTo($('div.zone_search div.searchform div.selected-tag'));
				}
				var elem = $(this);
				$.ajax({
					type: "POST",
					url: '<?= dims::getInstance()->getScriptEnv(); ?>',
					data: {
						dims_op: 'desktopv2',
						action: 'toogleTag',
						tag: $(elem).attr('dims-data-value'),
					},
					dataType: 'html',
					success: function(data){
						if(parseInt(data) > 0){
							if(!$('div.zone_search div.searchform form#desktop_formsearch span:first input.search-tag').length){
								var inp = '<input class="search-tag" value="<?= $_SESSION['cste']['_SEARCH_WITH_TAGS']; ?>" type="button" />';
								$('div.zone_search div.searchform form#desktop_formsearch span:first').append(inp);
							}
						}else{
							$('div.zone_search div.searchform form#desktop_formsearch span:first input.search-tag').remove();
						}
					},
				});
			}
		});
		<?php if(isset($_SESSION['dims']['tag_search']) && count($_SESSION['dims']['tag_search'])){
			foreach($_SESSION['dims']['tag_search'] as $id){
				?>
				$('span.tag[dims-data-value="<?= $id; ?>"]').addClass('selected');
				<?php
			}
		} ?>
		$('div.zone_search div.searchform form#desktop_formsearch span:first').delegate('input.search-tag','click',function(){
			if ($('#desktop_editbox_search').val() == '<?php echo $_SESSION['cste']['SEARCH_ON'].' '.$_SESSION['cste']['DIMS'];?>'){
				$('#desktop_editbox_search').val("");
			}
			$('div.zone_search div.searchform form#desktop_formsearch').attr('action',$('div.zone_search div.searchform form#desktop_formsearch').attr('action')+"&tag=1");
			document.formsearch.submit();
		});
	});
<?php
if(isset($map) && $map){
	?>
	initCurrentMap();
	<?php
}
?>
</script>
