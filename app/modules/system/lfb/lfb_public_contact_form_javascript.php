<script type="text/javascript" language="text/javascript">
	var timersearch;

	function upKeysearch() {
		clearTimeout(timersearch);
		timersearch = setTimeout('execSearch()', 800);
	}

	function execSearch() {
		clearTimeout(timersearch);
		var nomsearch = dims_getelem('search_ent').value;
		var divtoaffich = dims_getelem('dispres_searchent');

		if(nomsearch.length>=2) {
			dims_xmlhttprequest_todiv("admin.php", "op=search_enttoadd&action=<?php echo _BUSINESS_TAB_CONTACTSTIERS;?>&ent_name="+nomsearch, "", "dispres_searchent");
			divtoaffich.style.display = "block";
		}
	}

	function affiche_div(id_div) {
		var div_tochange = dims_getelem(id_div);
		if(div_tochange.style.display == 'block') div_tochange.style.display = 'none';
		else div_tochange.style.display = 'block';
	}

	function sendRequestInfo(id_record, type, rubcour) {
		var retour = dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<?php echo _BUSINESS_CAT_CONTACT; ?>&action=<?php echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=add_ticket&id_record="+id_record+"&type="+type+"&rubcour="+rubcour,"", 'dims_popup');
		dims_showpopup("popup", "450", "300", '',"dims_popup", 200, 100);
	}

	function validate_form_ct(id) {
		if(dims_validatefield('ct_nom',document.getElementById("ct_nom"), 'string') &&
			dims_validatefield('ct_prenom',document.getElementById("ct_prenom"), 'string')) {
			if(id == 0) {
				verif_similar(document.getElementById("ct_nom").value,document.getElementById("ct_prenom").value);
				return false;
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
	}

	function validate_form_ct_new(id) {
		if(dims_validatefield('ct_nom',document.getElementById("ct_nom"), 'string') &&
			dims_validatefield('ct_prenom',document.getElementById("ct_prenom"), 'string')) {
			if(id == 0) {
				var popup = document.getElementById("inf_similarite");
				popup.style.display="block";
				verif_similar(document.getElementById("ct_nom").value,document.getElementById("ct_prenom").value);
				document.getElementById('inf_similarite').focus();
			}
			else {
				document.form_ct.submit();
			}
		}
		else {
			alert('Error');
		}
	}

	function verif_similar(nom,prenom) {
		dims_xmlhttprequest_todiv("admin.php", "dims_mainmenu=9&cat=<?php echo _BUSINESS_CAT_CONTACT; ?>&action=<?php echo _BUSINESS_TAB_CONTACTSTIERS; ?>&op=verif_similar_pers&nom="+nom+"&prenom="+prenom,"",'inf_similarite');
	}

</script>
