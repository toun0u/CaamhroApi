function initConstantizer(link){
	$('body').append('<div id="constantizer"><div class="close"><a href="javascript:void(0);" class="a_switch_cstz"> <img src="./common/img/close.png" title="close"/></a></div><div class="search"><p><label for="cstz_text">Expression : </label><input type="text" name="cstz_text" id="cstz_text" /></p><a href="javascript:void(0);" onclick="javascript:reloadConstantes();">Recharger les constantes</a><a class="create_link" href="javascript:void(0);" onclick="javascript:displayFormConstante(\'add\',0);">Nouvelle constante</a><div style="clear:both"></div></div><div class="results"></div><div class="form" style="display:none"><div class="fields"></div><div class="actions"><a href="javascript:void(0);" id="create_button" onclick="javascript:saveConstante();">Créer</a><a href="javascript:void(0);" onclick="javascript:hideConstanteForm();">Annuler</a></div><div class="erreur"></div><div style="clear:both"></div></form></div><div class="success_edition" style="display:none;"><div class="bloc_infos"></div><div class="actions"><a href="javascript:void(0);" onclick="javascript:displayFormConstante(\'add\',0);">Nouvelle constante</a><a href="javascript:void(0);" onclick="javascript:hideConstanteForm();">Retour à la recherche</a></div></div><div class="footer"></div></div>');
	link.addClass('a_switch_cstz');

	$("a.a_switch_cstz").click(function(){
		$('div#constantizer').toggle();
		$('input#cstz_text').focus();

	});

	selected = 0;

	$('input#cstz_text').keyup(function(){
		setTimeout(execSearch, 1000);
	});

	//initialisation des champs de langue
	scope_langue = null;
	$('document').ready(function(){
		$.ajax({
			type: "POST",
			url: "admin.php",
			data: {
				'dims_op' : 'getLangFields'
			},
			dataType: "json",
			async: true,
			success: function(data){
				scope_langue = data;
				var table = '<table><tr><td><label for="phpvalue">PHP value</label></td><td><input type="text" name="phpvalue" id="phpvalue"/><input type="hidden" name="current_cste" id="current_cste"/></td></tr></table><table>';
				for (var id in scope_langue){
					table += '<tr><td><label for="lang_'+id+'">'+scope_langue[id]+'</label></td><td><input type="text" name="lang_'+id+'" id="lang_'+id+'"/></td></tr>'
				}
				table += '</table>';
				$('div#constantizer div.form div.fields').append(table);
			},
			error: function(data){
			}
		});
	});

	//draggable bloc
/*	$(function(){
		$('div#constantizer').draggable();
	});*/
}

function execSearch(){
	var value = $('input#cstz_text').val();
	if(value.length >= 2){
		if( ! $('div#constantizer div.results').is(':visible')){
			$('div#constantizer div.form').hide();
			$('div#constantizer div.success_edition').hide();
			$('div#constantizer div.results').fadeIn();
		}
		$.ajax({
			type: "POST",
			url: "admin.php",
			data: {
				'dims_op' : 'constantizer',
				'value': value
			},
			dataType: "json",
			async: true,
			success: function(data){
				if(data.length > 0){
					var list ='';
					for(var i=0;i<data.length;i++){
						list += '<div id="cste_'+data[i]['id']+'"><a class="constante" href="javascript:void(0);" onclick="javascript:displayConstante('+data[i]['id']+', \''+data[i]['phpvalue']+'\');">'+data[i]['value']+'</a></div>';
					}

					$('div#constantizer div.results').html(list);
				}
				else $('div#constantizer div.results').html('<span style="font-style:italic">Aucune constante ne correspond à cette recherche</span>');

			},
			error: function(data){
			}
		});
	}
	else{
		$('div#constantizer div.results').html('');
		$('div#constantizer div.footer').text('');
	}
}

function reloadConstantes(){
	$.ajax({
		type: "POST",
		url: "admin.php",
		data: {
			'dims_op' : 'reloadConstantes'
		},
		dataType: "text",
		async: true,
		success: function(data){
			location.reload();
		},
		error: function(data){
			location.reload();
		}
	});
}

function displayConstante(id, constante){
	if(selected != 0) $('div#cste_'+selected+' a').removeClass('selected');
	selected = id;
	$('div#constantizer div.footer').html('<p><a href="javascript:void(0);" onclick="javascript:displayFormConstante(\'edit\',\''+constante+'\');"><img src="./common/img/edit.gif"/></a>$_SESSION[\'cste\'][\''+constante+'\']</p>');
	$('div#cste_'+id+' a.constante').addClass('selected');
}

function displayFormConstante(mode, constante){
	$('div#constantizer div.results').hide();
	$('div#constantizer div.footer').text('');
	$('div#constantizer div.form').fadeIn();
	$('div#constantizer div.success_edition').hide();
	//réinitialisation des values
	if(typeof(constante) != "undefined" && constante != ''){//------------- EDITION
		//récupération des valeurs de la constante
		$.ajax({
			type: "POST",
			url: "admin.php",
			data: {
				'dims_op' : 'getConstanteInfos',
				'phpvalue' : constante
			},
			dataType: "json",
			async: true,
			success: function(data){
				$('div#constantizer div.form div.fields input').each(function(){
					$(this).val('');
				});
				$('div#constantizer div.form div.erreur').html('');
				for(var i=0;i<data.length;i++){
					var ctz = data[i];
					$('div#constantizer div.form div.fields input#lang_'+ctz.id_lang).val(ctz.value);
				}
				$('div#constantizer div.form div.fields input#phpvalue').val(constante);
				$('div#constantizer div.form div.fields input#current_cste').val(constante);//c'est ce qui permet de faire le distinguo entre l'édition ou l'ajout
				$('div#constantizer div.form a#create_button').text('Modifier');
			}
		});
	}
	else{//----------------- CREATION
		$('div#constantizer div.form div.fields input').each(function(){
			$(this).val('');
		});
		$('div#constantizer div.form div.erreur').html('');
		$('div#constantizer div.form a#create_button').text('Créer');
	}
}

function saveConstante(){
	var fields = {};
	$('div#constantizer div.form div.fields input').each(function(){
		fields[$(this).attr('name')] = $(this).val();
	});

	$.ajax({
		type: "POST",
		url: "admin.php",
		data: {
			'dims_op' : 'saveConstante',
			'fields': JSON.stringify(fields)
		},
		dataType: "json",
		async: true,
		success: function(data){
			if(typeof(data) != "undefined" ){
				if(data!= -1 && data != -2){
					$('div#constantizer div.form div.erreur').html('');
					$('div#constantizer div.form').hide();

					var success = '<div class="phpvalue">$_SESSION[\'cste\'][\''+data.phpvalue+'\']</div><div class="liste_sql">';
					for(var i=0;i<data.sql.length;i++){
						success += data.sql[i]+='<br/><br/>';
					}
					success +='</div>';
					$('div#constantizer div.success_edition div.bloc_infos').html(success);
					$('div#constantizer div.success_edition').fadeIn();
				}
				else{//affichage message d'erreur
					if(data == -1){
						$('div#constantizer div.form div.erreur').html('Aucune valeur n\'a été renseignée');
					}
					else{
						$('div#constantizer div.form div.erreur').html('La PHP_VALUE est manquante');
					}
				}
			}
		},
		error: function(data){

		}
	});
}

function hideConstanteForm(){
	$('div#constantizer div.form').hide();
	$('div#constantizer div.success_edition').hide();
	$('div#constantizer div.results').fadeIn();
	execSearch();

}