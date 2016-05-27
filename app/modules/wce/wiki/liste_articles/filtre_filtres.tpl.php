<?php
	$display = 'none';
	if((!empty($_SESSION['wiki']['lst_article']['filters']['status']) && $_SESSION['wiki']['lst_article']['filters']['status'] != -1) ||
	   (!empty($_SESSION['wiki']['lst_article']['filters']['creator']) && $_SESSION['wiki']['lst_article']['filters']['creator'] != -1) ||
	   (!empty($_SESSION['wiki']['lst_article']['filters']['date_from']) && $_SESSION['wiki']['lst_article']['filters']['date_from'] != -1) ||
	   (!empty($_SESSION['wiki']['lst_article']['filters']['date_to']) && $_SESSION['wiki']['lst_article']['filters']['date_to'] != -1) ||
	   (!empty($_SESSION['wiki']['lst_article']['filters']['select_lang']) && $_SESSION['wiki']['lst_article']['filters']['select_lang'] != -1) ||
	   (!empty($_SESSION['wiki']['lst_article']['filters']['keywords']) && $_SESSION['wiki']['lst_article']['filters']['keywords'] != -1) ||
	   (!empty($_SESSION['wiki']['lst_article']['filters']['include_content']) && $_SESSION['wiki']['lst_article']['filters']['include_content'] != -1)
	  ) $display='block';
?>

	<input type="hidden" name="op" value="wiki" />
	<input type="hidden" name="sub" value="<?= module_wiki::_SUB_LST_ARTICLES; ?>" />
	<div class="sous_cadre_article">
		<div class="title_h3">
			<a onclick="javascript:toggleFiltres();" class="lien_bas lk_filtre" href="javascript:void(0);">
				<? if($display == 'none') echo $_SESSION['cste']['_SHOW_FILTERS']; else echo $_SESSION['cste']['_MASK_FILTERS']; ?>
			</a>
			<h3><? echo $_SESSION['cste']['_FILTERS']; ?></h3>
		</div>

		<div class="zone_filtre bloc_filters"  style="display:<?= $display; ?>;">
			<table class="tab_filters">
				<tr>
					<td class="label">
						<label for="status"><? echo $_SESSION['cste']['_STATE']; ?></label>
					</td>
					<td class="field">
						<select name="status" id="status" onchange="javscript:document.form_filter_articles.submit();">
							<option value="-1"><? echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
							<option value="1" <?php if(!empty($_SESSION['wiki']['lst_article']['filters']['status']) && $_SESSION['wiki']['lst_article']['filters']['status']==1) echo 'selected="selected"';?>><? echo $_SESSION['cste']['UP_TO_DATE']; ?></option>
							<option value="2" <?php if(!empty($_SESSION['wiki']['lst_article']['filters']['status']) && $_SESSION['wiki']['lst_article']['filters']['status']==2) echo 'selected="selected"';?>><? echo $_SESSION['cste']['MODIFIED']; ?></option>
						</select>
					</td>
					<td class="label">
						<label for="creator"><? echo $_SESSION['cste']['_AUTHOR']; ?></label>
					</td>
					<td class="field">
						<select name="creator" id="creator" onchange="javscript:document.form_filter_articles.submit();">
							<option value="-1"><? echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
							<?php

							$work = new workspace();
							$work->open($_SESSION['dims']['workspaceid']);
							$users = $work->getUsersOpen('', '', false, ' ORDER BY dims_user.firstname, dims_user.lastname');
							if (!empty($users)) {
								foreach($users as $u){
									?>
									<option id="opt_<?php echo $u->getId(); ?>" value="<?php echo $u->getId(); ?>" <?php if(isset($_SESSION['wiki']['lst_article']['filters']['creator']) && $_SESSION['wiki']['lst_article']['filters']['creator']==$u->getId()) echo 'selected="selected"';?> ><?php echo $u->fields['firstname'].' '.$u->fields['lastname'];?></option>
									<?php
								}
							}
							?>
						</select>
					</td>
					<td class="label">
						<label for="date_from"><? echo $_SESSION['cste']['_MODIFIED_AT']; ?></label>
					</td>
					<td class="field">
						<input type="text" class="text_date" maxlength="10" name="date_from" id="date_from" rev="date_jj/mm/yyyy" <?php if(!empty($_SESSION['wiki']['lst_article']['filters']['date_from']) && $_SESSION['wiki']['lst_article']['filters']['date_from'] != -1) echo 'value="'.$_SESSION['wiki']['lst_article']['filters']['date_from'].'"'; ?> />
					</td>
					<td class="label">
						<label for="date_to"><? echo $_SESSION['cste']['DATE_TO_THE']; ?></label>
					</td>
					<td class="field">
						<input type="text" class="text_date" maxlength="10" name="date_to" id="date_to"  rev="date_jj/mm/yyyy"  <?php if(!empty($_SESSION['wiki']['lst_article']['filters']['date_to'])  && $_SESSION['wiki']['lst_article']['filters']['date_to'] != -1) echo 'value="'.$_SESSION['wiki']['lst_article']['filters']['date_to'].'"'; ?>/>
					</td>

				</tr>
			</table>

			<table>
				<td class="field">
					<label for="date_to"><? echo $_SESSION['cste']['_DIMS_LABEL_KEYWORDS']; ?></label>
					<input type="text" class="keywords" name="keywords" id="keywords" <?php if(!empty($_SESSION['wiki']['lst_article']['filters']['keywords'])  && $_SESSION['wiki']['lst_article']['filters']['keywords'] != -1) echo 'value="'.$_SESSION['wiki']['lst_article']['filters']['keywords'].'"'; ?>/>
					<input class="filter_cb" type="checkbox" name="include_content" id="include_content" value="1" <?php if(isset($_SESSION['wiki']['lst_article']['filters']['include_content']) && $_SESSION['wiki']['lst_article']['filters']['include_content'] != -1) echo 'checked="checked"';?> />
					<label for="include_content"><? echo $_SESSION['cste']['SEARCH_IN_CONTENT']; ?></label>
				</td>
			</table>

			<table class="tab_filters">
				<tr>
					<td>
						<input class="checkbox" type="checkbox" name="chbx_lang" id="chbx_lang" <?php if(isset($_SESSION['wiki']['lst_article']['filters']['select_lang']) && $_SESSION['wiki']['lst_article']['filters']['select_lang'] != -1) echo 'checked="checked"';?> />
						<label for="chbx_lang"><? echo $_SESSION['cste']['_ONLY_ITEMS_UNTRANSLATED']; ?></label>
						<select name="select_lang" id="select_lang"  onchange="javscript:document.form_filter_articles.submit();" <?php if(!isset($_SESSION['wiki']['lst_article']['filters']['select_lang']) || $_SESSION['wiki']['lst_article']['filters']['select_lang'] == -1) echo 'disabled="disabled"';?>>
						<?

						foreach(wce_lang::getInstance()->getAll(true) as $lang){
							$selected = '';
							if (!empty($_SESSION['wiki']['lst_article']['filters']['select_lang'])  && $_SESSION['wiki']['lst_article']['filters']['select_lang'] == $lang->fields['id']) $selected = 'selected="selected"';
							echo '<option value="'.$lang->fields['id'].'" '.$selected.'>'.$lang->getLabel().'</option>';
						}
						?>
				</select>
					</td>
				</tr>
			</table>
		</div>

		<div class="zone_filtre checkbox bloc_filters"  style="display:<?= $display; ?>;">
			<a href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LST_ARTICLES."&init=1"); ?>"><? echo $_SESSION['cste']['INIT']; ?></a>
			<span class="bouton"><? echo $_SESSION['cste']['_DIMS_OR']; ?></span>
			<input class="bouton" type="submit" value="<? echo $_SESSION['cste']['_DIMS_FILTER']; ?>" name="<? echo $_SESSION['cste']['_DIMS_FILTER']; ?>" />
		</div>
	</div>
<script type="text/javascript">
	function toggleFiltres(){
		$('div.bloc_filters').fadeToggle('fast',function(){
			if ($(this).is(':visible'))
				$('a.lk_filtre').html('<? echo $_SESSION['cste']['_MASK_FILTERS']; ?>');
			else
				$('a.lk_filtre').html('<? echo $_SESSION['cste']['_SHOW_FILTERS']; ?>');
		});
	}
	$(document).ready(function() {
        var dates = $("input#date_from, input#date_to").datepicker({
            showOn: 'both',
            buttonImageOnly: true,
            buttonText: '<? echo $_SESSION['cste']['SELECT_A_DATE_RANGE']; ?>',
            buttonImage: '<?php echo module_wiki::getTemplateWebPath('/gfx/calendar.png'); ?>',
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            onSelect: function( selectedDate ) {
                var option = this.id == "date_from" ? "minDate" : "maxDate",
                instance = $( this ).data( "datepicker" ),
                date = $.datepicker.parseDate(
                instance.settings.dateFormat ||
                    $.datepicker._defaults.dateFormat,
                selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
            }
        });

         $('#chbx_lang').change(function(){
        	if($(this).is(':checked')){
        		$("#select_lang").removeAttr('disabled');
        	}
        	else{
        		$("#select_lang").attr('disabled', 'disabled');
        	}
        });


    });
</script>