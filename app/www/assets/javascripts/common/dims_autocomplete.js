if( typeof(register_ac_objects) =='undefined' ) register_ac_objects = {};

(function($){
	$.fn.dims_autocomplete = function(ajax_data,//le contenu des params envoyés par la requête ajax
									  nb_char,//nombre de caractères à partir desquels l'appel ajax est lancé
									  delay,//délai attendu avant la fin de la frappe pour lancer la requête ajax
									  value_target,//selecteur jquery pour indiquer un input qui contiendra une valeur attendue correspondant à ce qui a été sélectionné
									  container, //le dom_elem qu'il faut afficher ou masquer
									  target,//le dom_element selector qui contiendra la liste des résultats
									  row_tpl, //le pattern de template qui va être utilisé pour alimenter la liste des résultats
									  empty_message,//le message en cas de non-résultats
									  callback){//fonction appellée dès que la valeur de value_target est amenée à changer
		//contrôle sur le type
		if($(this).tagName() == 'input'){
			if($(this).attr('id') != null || $(this).attr('id') != 'undefined'){
				if(container != null || container != "undefined")
				{
					if(target != null || target != "undefined")
					{
						//registration de l'objet
						var id = $(this).attr('id');
						if(register_ac_objects[id] == null || register_ac_objects[id] == 'undefined'){
							//création de l'objet
							register_ac_objects[id] = new ACContext();
						}
						//initialisation des valeurs
						register_ac_objects[id].initContext(id, ajax_data, target, value_target, container, row_tpl, empty_message, callback);

						var keyup_timer = '';
						value = $(this).val();

						$(this).keyup(function(){
                            if ($(this).val() != value) clearTimeout(keyup_timer);
							if($(this).val() != value  && $(this).val().length >= nb_char){
								value = $(this).val();

								ref = id;
								keyup_timer = setTimeout('keyupListener()' , delay);
							}
						});

						$(this).blur(function(){
							setTimeout(function() {
								$(container).fadeOut();
							}, 200);
						});
					}
					else alert('The target is undefined');
				}
				else alert('The container is undefined');
			}
			else alert('No ID found on the input');
		}
		else alert('Wrong type of field for Dims Autocompletion : '+$(this).tagName()+' founded, input required');
		return $(this);
	};
})(jQuery);

function ACContext(){
	this.ref = '0';
	this.params = {};
	this.target = null;
	this.value_target = null;
	this.container = null;
	this.tpl = null;
	this.empty_message = '';
	this.callback = null;

	this.initContext = function(r, ajax_params, t, vt, c, r_tpl, mess, func_callback){
		this.ref = r;
		this.params = ajax_params;
		this.target = t;
		this.value_target = vt;
		this.container = c;
		this.tpl = r_tpl;
		this.empty_message = mess;
		this.callback = func_callback;
	}

	this.getRef = function(){
		return this.ref;
	}
	this.getParams = function(){
		return this.params;
	}

	this.getValueTarget = function(){
		return this.value_target;
	}

	this.getTarget = function(){
		return this.target;
	}

	this.getContainer = function(){
		return this.container;
	}

	this.getTpl = function(){
		return this.tpl;
	}

	this.getEmptyMessage = function(){
		return this.empty_message;
	}

	this.getCallback = function(){
		return this.callback;
	}

	this.setParams = function(ajax_params){
		this.params = ajax_params;
	}
}

function keyupListener(){
	var context = register_ac_objects[ref];
	if(context != null && context != 'undefined'){
		var local_data = context.getParams();
		var callback = context.getCallback();

		local_data.text = value;
		jQuery.ajax({
			type: "POST",
			url: 'admin.php',
			async: false,//obligé pour Safari de jouer en synchrone, sinon ça passe pas.
			data : local_data,
			dataType: "json",
			success: function(data){
				//on vide le contenu actuel de la target et de la valeur associée
				$(context.getTarget()).empty();
				if(context.getValueTarget() != null && context.getValueTarget() != "undefined"){
					$(context.getValueTarget()).val('');
					if(callback != null && callback != 'undefined')
						callback();
				}
				$(context.getContainer()).find('span.no_elem').remove();

				if(data.length > 0){
					//on balance dans le div TARGET les rows selon le template principal
					$.template("row", context.getTpl());
					for(var i= 0; i < data.length ; i++)
					{
						var row = $.tmpl("row", data[i]);
						var gen_id = ref+'_row_'+data[i].id;
						row.attr('id', gen_id);
						$(context.getTarget()).append(row);
						//gestion de l'alimentation pour l'id sélectionné
						var label = data[i].label;
						$("#"+gen_id).click(function(){
							if(context.getValueTarget() != null && context.getValueTarget() != "undefined"){
								var generated_id = $(this).attr('id');
								var my_id = generated_id.substring(generated_id.indexOf('_row_') + 5, generated_id.length);
								$(context.getValueTarget()).val(my_id);
							}
							$("#"+ref).val($(this).text());
							$(context.getContainer()).hide();
							if(callback != null && callback != 'undefined')
								callback();
						});
						row.css('cursor', 'pointer');

					}
				}
				else{//message vide
					$(context.getContainer()).append('<span class="no_elem">'+context.getEmptyMessage()+'</span>');
				}
				$(context.getContainer()).fadeIn();
			}
		});
	}
	else alert("Registered object not found");
}


/*

function(){

							}

*/
