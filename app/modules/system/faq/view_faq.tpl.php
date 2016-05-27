<?php

	if (isset($data_browser_liste)){
		foreach($data_browser_liste as $val){
			foreach($val as $v)
				echo $v;
		}
	}elseif(isset($data_browser_form)){

		$faq = new dims_faq();
		$faq->init_description();
		echo '<div style="margin:10px;" id="content_'.$data_browser_form.'">';

		if ($data_browser_form == 0){
			global $dims;
			if($properties['categ']){
				?>
				<span style="width:100%;margin-bottom:10px;" id="arianeCateg_X">
				<?
				echo 'Aucune cat&eacute;gorie';
				echo dims_create_button("Choisir cat&eacute;gorie",'','javascript:publiFaqAddCateg(0);','','float:right;');
				?>
				</span>
				<?
			}
			?>
			<form method="POST" action="<? echo $dims->getScriptEnv(); ?>" name="saveFaq_X">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("saveFaq",	"saveFaq");
					$token->field("id",			"0");
					$token->field("langFaq_title");
					$token->field("faq_type");
					$token->field("isBefore");
					$token->field("fck_langFaq_content");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<input type="hidden" name="saveFaq" value="saveFaq" />
				<input type="hidden" name="id" value="0" />
				<?
				if($properties['categ']){
					?>
						<input type="hidden" name="idCateg" id="idCateg_X" value="0" />
					<?
					$token->field("idCateg", "0");
				}
				dims_fckeditor('langFaq_content', $faq->getContent(), '100%', 290,true);
				?>
				<div style="width: 60%;float:left;">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td style="text-align:right;width:30%;">
								<? echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?> :
							</td>
							<td>
								<input onfocus="javascript: $(this).css('background','');" type="text" name="langFaq_title" value="<? echo $faq->getTitle(); ?>" style="width:100%" />
							</td>
						</tr>
						<tr>
							<td style="text-align:right;">
								<? echo $_SESSION['cste']['_DIMS_LABEL_VISIBLE']; ?> :
							</td>
							<td>
								<select name="faq_type">
									<option <? if ($faq->getType() == dims_faq::TYPE_BOTH) echo 'selected=true'; ?> value="<? echo dims_faq::TYPE_BOTH; ?>"><? echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
									<option <? if ($faq->getType() == dims_faq::TYPE_BO) echo 'selected=true'; ?> value="<? echo dims_faq::TYPE_BO; ?>">Backoffice</option>
									<option <? if ($faq->getType() == dims_faq::TYPE_FO) echo 'selected=true'; ?> value="<? echo dims_faq::TYPE_FO; ?>">Frontoffice</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="text-align:right;">
								Mise en avant :
							</td>
							<td>
								<input type="checkbox" name="isBefore" <? if($faq->getIsBefore()) echo 'checked=true'; ?> />
							</td>
						</tr>
					</table>
				</div>
				<div style="float:left;margin-left:5%;">
					<?
					require_once DIMS_APP_PATH.'/modules/system/class_lang.php';
					$lang = new lang();
					$lang->open($_SESSION['dims']['faq']['lang']);
					if ($urlFlag = $lang->getFlag())
						echo '<img src="'.$urlFlag.'">';
					else
						echo 'No flag available !';
					?>
				</div>
			</form>
			<?
			echo dims_create_button($_SESSION['cste']['_DIMS_SAVE'],'','javascript:if(document.saveFaq_X.langFaq_title.value != \'\') document.saveFaq_X.submit(); else document.saveFaq_X.langFaq_title.style.backgroundColor=\'#FF0000\';','','float:right;','javascript:void(0);');
			echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_CANCEL'],'','javascript:window.location.reload();','','float:right;');
		}else{
            $faq->open($data_browser_form);
			?>
				<iframe class="frame_<? echo $data_browser_form; ?>" style="border:none;width:100%;height:380px;"></iframe>
				<div class="contentIframe_<? echo $data_browser_form; ?>">
					<?
					echo $faq->getContent();
					?>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						setTimeout(function(){
							$('iframe.frame_<? echo $data_browser_form; ?>').contents().find('body').append($('div.contentIframe_<? echo $data_browser_form; ?>')).css({'color': '#1F1F1F', 'font': '13px Trebuchet MS,Tahoma,Verdana,Arial,sans-serif'});
						},1000);
					});
				</script>
			<?
		}
		echo '</div>';
	}
?>
