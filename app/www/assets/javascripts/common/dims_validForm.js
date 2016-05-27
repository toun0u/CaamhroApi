(function($){
	$.fn.dims_validForm = function(params){
		var form = $(this);
		var full_error = false;
		var dims_form_submitted = false;

		var email =    /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;//adresse email
		var number = /^[-]?\d*[\.,]?\d*$/; // Nombre
		var color = /^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/; //couleur
		var date_frslashes = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/; //date française au format jj/mm/aaaa
		var heure_doublepoint = /^[0-9]{2}:[0-9]{2}$/; //heure au format hh:mm
		var heure_part = /^[0-9]{2}$/; //soit hh soit mm
		var alpha_num = /^[a-z0-9]+$/; //alphanumérique pour par exemple les urls


		var defaults = {
			messages: {
					defaultError: 'Ce champ est obligatoire',
					formatMail: 'Le format de cette adresse email est incorrect',
					formatAlphanum: 'Le format de ce champ n\'est pas alphanumérique',
					formatNombre: 'Le format de ce nombre est incorrect',
					formatCouleur: 'La couleur passée doit être au format HTML. Ex : #34F3DF',
					login: 'Ce login est déjà existant, vous ne pouvez pas l\'utiliser',
					password: 'Les deux mots de passe ne correspondent pas',
					formatDate: 'Le format de la date est incorrect',
					formatHeureMin: 'Le format de l\'heure est incorrect (hh:mm)',
					formatHeure: 'Le format de l\'heure est incorrect (hh)',
					formatMin: 'Le format des minutes est incorrect (mm)',
					mails: 'Les deux adresses email ne correspondent pas',
					extensionFile: 'L\'extension du fichier est incorrecte. Extension attendue : ',
					checkbox: 'Au moins un élément doit être sélectionné',
					globalMessage: 'Vérifiez que tous les champs obligatoires sont saisis !'
					},
			alert: false,
			displayMessages: true,
			classMessage: 'dims_error_valid',
			classInput: 'dims_error_input',
			refId: null,
			refAttr: 'name',
			globalId: null,
			extended_controls: {},
			ajax_submit: false,
			submit_replace: null,
		};

		var empty = {};
		var opts = $.extend(empty,defaults, params);
		if ('messages' in params) // permet de garder les valeurs des messages par défaut, si non présentes dans les paramètres
			opts.messages = $.extend(defaults.messages,params.messages);

		form.ready(function(){
			$("input[type!='checkbox']",$(this)).focusout(valideField);
			$("input[type!='checkbox']",$(this)).focusin(function(){
				$(this).removeClass(opts.classInput);
				$(this).parents('.form-group:first').removeClass('has-error'); // compatibilité bootstrap
				if (opts.displayMessages){
					if (opts.refId == null)
						$(this).next('span.'+opts.classMessage).remove();
					else{
						if ($(this).attr(opts.refAttr) != 'undefined' && $(this).attr(opts.refAttr) != null && $(this).attr(opts.refAttr).match(/\[\]$/)) {
							$("#"+opts.refId+"_"+$(this).attr(opts.refAttr).substring(0,$(this).attr(opts.refAttr).length-2)).empty();
						} else {
							$("#"+opts.refId+"_"+$(this).attr(opts.refAttr)).empty();
						}
					}
				}
				full_error = false;
			});
		});

		form.submit(function(event){
			full_error = false;
			dims_form_submitted = true;
			$("input[name!='']",$(this)).each(valideField);
			$("select[name!='']",$(this)).each(valideField);
			$("textarea[name!='']",$(this)).each(valideField);
			dims_form_submitted = false;

			if(full_error){
				if (opts.alert) alert(opts.messages.globalMessage);
				if (opts.globalId != null){
					$("."+opts.globalId).html(opts.messages.globalMessage);
					if($("."+opts.globalId).is(':hidden')){
						$("."+opts.globalId).show();
					}
				}
				return false;
			}
			if($("."+opts.globalId).is(':visible')){
				$("."+opts.globalId).hide();
			}
			if(opts.ajax_submit){
				event.preventDefault();
				$.ajax({
					type: form.attr("method"),
					url: form.attr("action"),
					data: form.serialize(),
					dataType: "html",
					success: function(data){
						if(opts.submit_replace && $(opts.submit_replace).length && data != ""){
							$(opts.submit_replace).html(data);
						}
					},
				});
				return false;
			}else{
				return true;
			}
		});

		function valideField(){
			error =false;
			target= null;
			message = null;
			if(dims_form_submitted){
				if($(this).tagName()!='select'){
					// gestion spécifique pour les passwords
					/*if ($(this).attr('rel')=='requis' && ($(this).attr("rev") == 'dims_pwd_confirm' || $(this).attr("rev") == 'dims_pwd') && $('#dims_login_reference',form).val() != '' && $("input[rev='dims_pwd_confirm']",form).val() != $("input[rev='dims_pwd']",form).val()){
						target = $("input[rev='dims_pwd_confirm']",form);
						error = true;
						message = opts.messages.password;
					}else */
					if($(this).tagName()=='input' && $(this).attr('type') == 'radio' && $(this).attr('rel')=='requis'){ // gestion des radio button
						if($("input["+opts.refAttr+"='"+$(this).attr(opts.refAttr)+"']:checked",form).length == 0){
							error = true;
							if($("input["+opts.refAttr+"='"+$(this).attr(opts.refAttr)+"']",form).index($(this)) == $("input["+opts.refAttr+"='"+$(this).attr(opts.refAttr)+"']",form).length-1) { // test si c'est le dernier pour n'afficher qu'une seule fois le message
								message = opts.messages.checkbox;
							} else {
								message = '';
							}
						}
					}else if( $(this).val() == '' && $(this).attr('rel')=='requis'){
						if($(this).attr("rev") != null && $(this).attr("rev") == 'dims_pwd'){
							target = $("input[rev='dims_pwd_confirm']",form);
						}
						error = true;
						message = opts.messages.defaultError;
					}
					else if( $(this).val() != '' && $(this).attr('rel')=='requis' && ($(this).attr("rev") == 'dims_pwd_confirm') && $("input[rev='dims_pwd']",form).val() == ''){//cas très particulier pour que si dims_pwd est vide on balance l'erreur quand même
						error = true;
						message = opts.messages.defaultError;
					}
				}
				else if($(this).tagName()=='select' && $(this).attr('rel')=='requis'){
					error =true;
					if($(this).val() !='dims_nan' && $(this).val() !=''){
						error = false;
						//return false; //équivalent du break dans le each
					}
					if(error)message = opts.messages.defaultError;
				}
			}

			//Gestion des contrôles étendus avec les fonctions de callback métier
			if(opts.extended_controls[$(this).attr('id')] != null){
				var callback_result = opts.extended_controls[$(this).attr('id')]();
				error = callback_result.error;
				message = callback_result.message;
			}

			if(!error)
			{
				if($(this).attr("rev") == 'email' )
				{
					if($(this).val() != '' && !$(this).val().match(email)){
						 error = true;
						 message = opts.messages.formatMail;

					}
				}

				else if($(this).attr("rev") == 'number')
				{
					if( $(this).val() != '' && !$(this).val().match(number)){
						 error = true;
						 message = opts.messages.formatNombre;
					}
				}

				else if($(this).attr("rev") == 'color')
				{
					if( $(this).val() != '' && !$(this).val().match(color)){
						 error = true;
						 message = opts.messages.formatCouleur;
					}
				}
				else if($(this).attr("rev") == 'alpha_num')
				{
					if( $(this).val() != '' && !$(this).val().match(alpha_num)){
						 error = true;
						 message = opts.messages.formatAlphanum;
					}
				}

				else if($(this).attr("rev") == 'dims_login')
				{
					if( $(this).val() != '' && $(this).val() != $('#dims_login_reference',form).val() && !isUniqueLogin($(this).val())){
						error = true;
						message = opts.messages.login;
					}
				}
				else if($(this).attr("rev") == 'dims_pwd'){
					target = $("input[rev='dims_pwd_confirm']",form);
					if ($('#dims_login_reference',form).val() != '' && $("input[rev='dims_pwd_confirm']",form).val() != '' && $(this).val() != $("input[rev='dims_pwd_confirm']",form).val()){
						error = true;
						message = opts.messages.password;
					}else if($(this).val() !='' && target.val() !='' && $(this).val() != target.val()){
						error = true;
						message = opts.messages.password;
					}
				}

				else if($(this).attr("rev") == 'dims_pwd_confirm'){

					if ($('#dims_login_reference',form).val() != '' && $(this).val() != $("input[rev='dims_pwd']",form).val()){
						error = true;
						message = opts.messages.password;
					}else if($("input[rev='dims_pwd']",form).val() !='' && $(this).val() != '' && $(this).val() != $("input[rev='dims_pwd']",form).val()){
						error = true;
						message = opts.messages.password;
					}
				}

				else if($(this).attr("rev") == 'date_jj/mm/yyyy'){
					if( $(this).val() != '' && !$(this).val().match(date_frslashes)){
						 error = true;
						 message = opts.messages.formatDate;
					}
				}

				else if($(this).attr("rev") == 'heure_hh:mm'){
					if( $(this).val() != '' && !$(this).val().match(heure_doublepoint)){
						error = true;
						message = opts.messages.formatHeureMin;
					}
					else if($(this).val() != '' && $(this).val().match(heure_doublepoint))
					{
						var tabH = $(this).val().split(':');
						var h = parseInt(tabH[0]);
						var m = parseInt(tabH[1]);
						if(!(h >= 0 && h<24 && m >= 0 && h<60) )
						{
							error = true;
							message = opts.messages.formatHeureMin;
						}
					}
				}

				else if($(this).attr("rev") == 'heure_hh'){
					if( $(this).val() != '' && !$(this).val().match(heure_part)){
						error = true;
						message = opts.messages.formatHeure;
					}
					else if($(this).val() != '' && $(this).val().match(heure_part))
					{
						var h = $(this).val();
						if(!(h >= 0 && h<24) )
						{
							error = true;
							message = opts.messages.formatHeure;
						}
					}
				}

				else if($(this).attr("rev") == 'heure_mm'){
					if( $(this).val() != '' && !$(this).val().match(heure_part)){
						error = true;
						message = opts.messages.formatMin;
					}
					else if($(this).val() != '' && $(this).val().match(heure_part))
					{
						var m = $(this).val();
						if(!(m >= 0 && m<60) )
						{
							error = true;
							message = opts.messages.formatMin;
						}
					}
				}
				else if($(this).attr("rev") !=null && $(this).attr("rev").substring(0,8) == 'compare:'){//mode comparaison pour deux inputs
					if($(this).tagName()=='input'){
						//alert("'"+$(this).attr("rev").substring(8,$(this).attr("rev").length)+"'");
						var cmpTo = $(this).attr("rev").substring(8,$(this).attr("rev").length);
						var cmpToValue = $('#'+cmpTo).val();
						if($(this).val() != '' && cmpToValue != '' && $(this).val() != cmpToValue){
							error = true;
							message = opts.messages.mails;
						}
					}
				}

				else if($(this).attr("rev") !=null && $(this).attr("rev").substring(0,4) == 'ext:'){//mode comparaison pour input type file ou autre sur l'extension de la valeur
				// plusieurs extensions peuvent être saisies en les séparant avec des ','
					if($(this).tagName()=='input'){
						var extension = $(this).attr("rev").substring(4,$(this).attr("rev").length).toLowerCase();
						var LstExtension = extension.split(',');
						var file_type = $(this).val().toLowerCase().split('.');
						file_type = file_type[file_type.length-1];
						if(file_type != '' && extension != ''){
							error = true;
							for(var i = 0;i < LstExtension.length; i++){
								if (LstExtension[i] == file_type)
									error = false;
							}
							message = opts.messages.extensionFile+LstExtension.join(' / ');
						}
					}
				}

				else if($(this).attr("rev") !=null && $(this).attr("rev").substring(0,8) == 'gr_check'){//mode comparaison pour savoir si au moins une checkbox du groupe est cochée
					if($(this).tagName()=='input' && $(this).attr('type')=='checkbox'){
						if($("input["+opts.refAttr+"='"+$(this).attr(opts.refAttr)+"']:checked",form).length == 0){
							error = true
							if($("input["+opts.refAttr+"='"+$(this).attr(opts.refAttr)+"']",form).index($(this)) == $("input["+opts.refAttr+"='"+$(this).attr(opts.refAttr)+"']",form).length-1) { // test si c'est le dernier pour n'afficher qu'une seule fois le message
								message = opts.messages.checkbox;
							} else {
								message = '';
							}
						}
					}
				}
			}
			if(error && message != ''){
				if(target==null){
					$(this).addClass(opts.classInput);
					$(this).parents('.form-group:first').addClass('has-error'); // compatibilité bootstrap
					if (opts.displayMessages){
						if (opts.refId == null){
							$(this).next('span.'+opts.classMessage).remove();
							$(this).after('<span class="'+opts.classMessage+'">'+message+'</span>');
						}
						else{
							var suffixe = $(this).attr(opts.refAttr);

							$("#"+opts.refId+"_"+suffixe).empty();
							if (suffixe.match(/\[\]$/))
								$("#"+opts.refId+"_"+suffixe.substring(0,suffixe.length-2)).text(message);
							else
								$("#"+opts.refId+"_"+suffixe).html(message);
						}
					}
				}
				else{
					target.addClass(opts.classInput);
					target.parents('.form-group:first').addClass('has-error'); // compatibilité bootstrap
					if (opts.displayMessages){
						if (opts.refId == null){
							target.next('span.'+opts.classMessage).remove();
							target.after('<span class="'+opts.classMessage+'">'+message+'</span>');
						}
						else{
							if (target.attr(opts.refAttr) != 'undefined' && target.attr(opts.refAttr) != null && target.attr(opts.refAttr).match(/\[\]$/)){
								var n = target.attr(opts.refAttr).substring(0,target.attr(opts.refAttr).length-2);
								$("#"+opts.refId+"_"+n).empty();
								$("#"+opts.refId+"_"+n).html(message);
							}else{
								$("#"+opts.refId+"_"+target.attr(opts.refAttr)).empty();
								$("#"+opts.refId+"_"+target.attr(opts.refAttr)).html(message);
							}
						}
					}
				}
			}
			else{
				if(target==null){
					$(this).removeClass(opts.classInput);
					$(this).parents('.form-group:first').removeClass('has-error'); // compatibilité bootstrap
					if (opts.displayMessages){
						if (opts.refId == null)
							$(this).next('span.'+opts.classMessage).remove();
						else{
							if ($(this).attr(opts.refAttr) != 'undefined' && $(this).attr(opts.refAttr) != null && $(this).attr(opts.refAttr).match(/\[\]$/)) {
								$("#"+opts.refId+"_"+$(this).attr(opts.refAttr).substring(0,$(this).attr(opts.refAttr).length-2)).empty();
							} else {
								$("#"+opts.refId+"_"+$(this).attr(opts.refAttr)).empty();
							}
						}
					}
				}
				else{
					target.removeClass(opts.classInput);
					target.parents('.form-group:first').removeClass('has-error'); // compatibilité bootstrap
					if (opts.displayMessages){
						if (opts.refId == null)
							target.next('span.'+opts.classMessage).remove();
						else{
							if (target.attr(opts.refAttr) != 'undefined' && target.attr(opts.refAttr) != null && target.attr(opts.refAttr).match(/\[\]$/)) {
								$("#"+opts.refId+"_"+target.attr(opts.refAttr).substring(0,target.attr(opts.refAttr).length-2)).empty();
							} else {
								$("#"+opts.refId+"_"+target.attr(opts.refAttr)).empty();
							}
						}
					}
				}
			}
			full_error = full_error || error;
		}

		return $(this);
	};
})(jQuery);

if (document.getElementById('fn.tagName')==null)
$.fn.tagName = function() {
   return this.get(0).tagName.toLowerCase();
}
