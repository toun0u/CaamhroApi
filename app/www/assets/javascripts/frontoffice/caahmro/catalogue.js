// Add to cart
function addToCart(article_id, quantity) {
	if (article_id == undefined) article_id = 0;
	if (quantity == undefined) quantity = 1;

	if (article_id > 0 && quantity > 0) {
		$.ajax({
			url: '/index.php',
			data: {
				op: 'ajouter_panierart',
				article_id: article_id,
				quantity: quantity
			},
			dataType: 'json',
			async: true,
			success: function(data) {
				switch (data.articles.length) {
					case 0:
						var cart_text = 'Votre panier (vide)';
						break;
					case 1:
						var cart_text = '1 article';
						break;
					default:
						var cart_text = data.articles.length + ' articles';
						break;
				}
				$('#nbArtPanier').html(cart_text);
			}
		});
	}
}

function dropArticle(reference) {
	dims_confirmlink('/index.php?op=enlever_article&pref='+reference, 'Sûr(e) ?');
}

function modifyQte(inputField, qteMod) {
	if (qteMod  > 0 || (qteMod < 0 && parseInt(inputField.value) > Math.abs(qteMod))) {
		inputField.value = parseInt(inputField.value, 10) + qteMod;
	}
}

function constraintFieldsUvente(inputField, uvente) {
	value = parseInt(inputField.value);
	inputField.value = String(value).replace('-','');
	moduloValueUvente = value % uvente;

	if(moduloValueUvente != 0) {
		inputField.value = value + (uvente - moduloValueUvente);
		flashPopup("Les quantités ont été ajustées au colisage supérieur");
	}
}

var timeFlashPopup;
function flashPopup(message, divElem) {
	if(divElem == undefined) divElem = $('#flashpopup')

	clearTimeout(timeFlashPopup);
	divElem.append(message+'<br>').fadeIn('fast', function(){
		timeFlashPopup = setTimeout(function() {
			divElem.fadeOut('fast', function() {
				divElem.text('');
			});
		}, 1500);
	});
}

function showMoreFilters(filterId) {
	$('#showMore'+filterId).hide();
	$('.filterOption'+filterId).show();
}

jQuery(document).ready(function ($) {
	$('#msgboxclose').click(function() {
		$('#msgbox').animate({'top': '-4000px'},200);
		$('#overlay').fadeOut('fast');
	});
	$('#overlay').click(function(){
		$('#msgbox').animate({'top': '-4000px'},200);
		$('#overlay').fadeOut('fast');
	});
});

function keepalive() {
	dims_xmlhttprequest('/index.php', 'dims_op=keep_connection');
}
setInterval("keepalive()", 180000);
