<div class="title_h3">
    <h3>Pages d'accueil après connexion : <? echo $this->fields['domain']; ?></h3>
</div>
<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_PARAM); ?>" name="save_meta">
		<input type="hidden" name="action" value="<? echo module_wce::_PARAM_INFOS_SAVE_ACCUEIL2; ?>" />
		<input type="hidden" name="id" value="<? echo $this->fields['id']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td class="title_table">
							<?
							$home = new wce_article();
							$home->init_description();

							$lk = "";
							if ( $this->getPostConnexionPage() != '' && $this->getPostConnexionPage() > 0) {
								$home->open($this->getPostConnexionPage());
								$lk = "&articleid=".$this->getPostConnexionPage();
							}
							?>
							<input type="hidden" id="wce_article_id_article_link_PRIVATE" name="wce_article_id_article_link_PRIVATE" value="<? echo $home->fields['id']; ?>">
							<input style="float:left;width:40%;" type="text" readonly class="text" id="linkedpage_displayed_PRIVATE" value="<? echo $home->fields['title']; ?>">
						</td>
						<td>
							<input type="button" style="width:auto;" class="button" value="<? echo $_SESSION['cste']['_FORM_SELECTION']; ?>" onclick="javascript:dims_showpopup('',300,event,'click','dims_popup');dims_xmlhttprequest_todiv('admin-light.php','dims_op=selectlinkarticle&input=wce_article_id_article_link_PRIVATE&display=linkedpage_displayed_PRIVATE<? echo $lk; ?>',false,'dims_popup');"/>&nbsp;
							<input type="button" style="width:auto;" class="button" value="<? echo $_SESSION['cste']['_DIRECTORY_LEGEND_DELETE']; ?>" onclick="javascript:dims_getelem('wce_article_id_article_link_PRIVATE').value='';dims_getelem('linkedpage_displayed_PRIVATE').value='';" /></span>
						</td>
					</tr>
				</table>
			</div>
			<div class="sub_form">
				<div class="form_buttons">
					<div>
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
					</div>
					<div>
						<? echo $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_DEF; ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	function wce_showheading(hid,str) {
		elt = document.getElementById(hid+'_plus');
		if (elt.innerHTML.indexOf('plusbottom') != -1) elt.innerHTML = elt.innerHTML.replace('plusbottom', 'minusbottom');
		else  if (elt.innerHTML.indexOf('minusbottom')  != -1) elt.innerHTML = elt.innerHTML.replace('minusbottom', 'plusbottom');
		else  if (elt.innerHTML.indexOf('plus')  != -1) elt.innerHTML = elt.innerHTML.replace('plus', 'minus');
		else  if (elt.innerHTML.indexOf('minus')  != -1) elt.innerHTML = elt.innerHTML.replace('minus', 'plus');


		if (elt = document.getElementById(hid)) {
			if (elt.style.display == 'none') {
				if (elt.innerHTML.length < 20) dims_xmlhttprequest_todiv('<? echo dims::getInstance()->getScriptEnv(); ?>','op=xml_detail_heading&hid='+hid+'&str='+str,'',hid);
				document.getElementById(hid).style.display='block';
			}
			else {
				document.getElementById(hid).style.display='none';
			}
		}
	}
</script>