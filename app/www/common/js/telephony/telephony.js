var Telephony = {
	// Settings and constants
	settings: {
		proxy: "/admin-voip.php",
		iconIncomingCall: "/common/img/telephony/incoming_call.png",
		iconOutgoingCall: "/common/img/telephony/outgoing_call.png",
		iconErrorCall: "/common/img/telephony/error_call.png",
		contact: "/common/img/contacts40.png",
		callnow : "/common/img/telephony/call_now.png",
	},

	//evenements
	C: {
		ACT_CALL: 'call',
		ACT_REJECT: "reject",
		ACT_SEND_SMS: 'send_sms',
		ACT_POLL: 'poll',
		ACT_TAKE: 'takeCall',
		CALL_INCOMING: 'incoming',
		CALL_OUTGOING: 'outgoing'
	},

	// Local storage,
	S: {
		//date de l'évenènement
		lastEvent: 0,
		nameMatches: {},
		//nom contact et photo
		ongoing: new Object(nc='',photo=''),
		//token de sécurité
		token: ''
	},

	// Main functions - retourne un objet promised
	//retourne le nom d'un contact ou d'un tiers
	getName: function (number) {
		if (Telephony.S.nameMatches[number] != undefined) {
			var def = $.Deferred();
			def.resolve(Telephony.S.nameMatches[number]);
			return def.promise();
		} else {
			return $.post("/admin-light.php?dims_op=telephony&todo_op=getNameMatch&ajax=1", {"action":"getNameMatch","number":number}, function(data) {
				if (data.length < 3) {
					Telephony.S.nameMatches[number] = Telephony.formatPhone("+"+number);
				} else {
					Telephony.S.nameMatches[number] = data;
				}
			});
		}
	},

	//retourne la photo du contact du contact
	getPhoto: function(numero){
		$.post("/admin-light.php?dims_op=telephony&todo_op=getPhoto&ajax=1", {"number":numero},function(data){
			return data;
		});
	},

	//fonction d'appel
	call: function(token, number) {
		$.post(Telephony.settings.proxy, {"action":Telephony.C.ACT_CALL,"number":number,'token':token, 'nom_ref':""},
			function(data) {
				console.log(data);
				if (data.indexOf("OK") < 0) {
					Telephony.showError("Impossible d'effectuer l'appel: ", data);
				} else {
					Telephony.showInfo("Appel lancé", " à " + Telephony.getName(number));
				}
		});
	},

	//fonction sms	
	sendSMS: function(token, number, text) {
		$.post(Telephony.settings.proxy, {"action":Telephony.C.ACT_SEND_SMS,"number":number,"text":text,'token':token},
			function(data) {
				if (data.indexOf("OK") < 0) {
					Telephony.showError("Impossible d'envoyer le SMS: ", data);
				} else {
					Telephony.showInfo("SMS envoyé", " à " + Telephony.getName(number));
				}
		});
	},

	//call incoming ou outgoing
	_getCallDirection: function(account, caller, callee) {
		if (account == caller) {
			return {"number":callee, "type":Telephony.C.CALL_OUTGOING};
		} else {
			return {"number":caller, "type":Telephony.C.CALL_INCOMING};
		}
	},


	//fonction pour allez chercher les events en temps réel sur le serveur node.js via les appels ajax long-poll
	doPolling: function() {

			var prec_lastevent=Telephony.S.lastEvent;
			$.post(Telephony.settings.proxy, {"action":Telephony.C.ACT_POLL, "lastEvent":Telephony.S.lastEvent, "token":Telephony.S.token}).done(function(response) {
				//console.log(response);
				var data = $.parseJSON(response);
				var ongoing = data.ongoing;
				var log = data.log;
				var taken = data.taken;
				// Process real-time events first
				for (var i = 0; i < ongoing.length; i++) {
					var item = ongoing[i];
					var callDirection = Telephony._getCallDirection(item.account, item.caller, item.callee);

					if (item.date > Telephony.S.lastEvent) {
						Telephony.S.lastEvent = item.date;
					}

					// When doing a click-to-call, the first call can be removed as it's the call
					// of the actual user phone, before establishing the real call. The NodeJS
					// proxy then returns a RELEASE event for that call without logging it in call log
					if (item.status == 'RELEASE') {
						Telephony.onCallRelease(item.callref);
						continue;
					}

					if (item.status == 'SETUP_INC_CALL' || item.status == 'SETUP_OUT_CALL') {
						Telephony.onCallSetup(callDirection, item.callref, callDirection.number, item.date, item.sessionid);
					}
					else if (item.status == 'ONGOING_INC_CALL' || item.status == 'ONGOING_OUT_CALL') {
						Telephony.onCallConnect(callDirection, item.callref, callDirection.number, item.date);
					}else if(item.status=='UNK'){
						//console.log(item);
						Telephony.onCallConnect(callDirection, item.callref, callDirection.number, item.date);
					}
				}

				// Then process the call log
				for (var i = 0; i < log.length; i++) {
					var item = log[i];
					var callDirection = Telephony._getCallDirection(item.account, item.caller, item.callee);

					if (item.dateEnd > Telephony.S.lastEvent) {
						Telephony.S.lastEvent = item.dateEnd;
					}

					if (item.status == 'NORMAL_OUT_CALL' || item.status == 'NORMAL_INC_CALL') {
						if(prec_lastevent!=0)
							Telephony.takeNote('stop',item.callref);
						Telephony.onCallEnd(Telephony.S.token, callDirection, item.callref, callDirection.number, item.dateStart, item.dateEnd);
					}
					else if (item.status == 'FAILED_CALL') {
						Telephony.onCallFailed(Telephony.S.token, item.callref, callDirection.number, item.dateEnd);
					}
					else if (item.status == 'MISSED_CALL') {
						Telephony.onCallMissed(Telephony.S.token, item.callref, callDirection.number, item.dateEnd);
					}
				}

				//prise de notes
				if(taken!=''){
					var nu=($(".telephony-tile-element").attr('title'));
					var sip='';
					$.ajax({
						type: 'POST',
						url: "/admin-light.php?dims_op=telephony&todo_op=sip&ajax=1",
						success: function(data) {
				        	if(data!=''){
								sip=data;
							}
						},
						dataType: "html",
						async:false
						});	

				 	if((taken.account)!=sip){
	
				 		$("#write_note").css('display', 'none');
				 		$('#textareatelephony').val("");
				 		delete Telephony.S.ongoing[nu];
						Telephony.hideOngoingItem(taken.ref);
						$("div[title="+nu+"]").remove();
						var keys=Object.keys(Telephony.S.ongoing);
						if(keys.length==0){
							$("#ongoing-calls").empty().text("Aucun appels en cours");
						}	
				 	}
				 	Telephony.S.lastEvent = taken.lastevent;
				}

				// Restart polling immediately
				Telephony.doPolling();
			}).fail(function(){
				Telephony.doPolling();
				//console.log("pull failed");
				$.post("/admin-light.php?dims_op=telephony&todo_op=reload_token&ajax=1",function(data){
					Telephony.S.token=data;
				});
			});		
	},

	//Fin d'appel
	onCallRelease: function (callref) {
	   Telephony.hideOngoingItem(callref);
	},

	//debut d'appel
	onCallSetup: function(direction, callref, number, date, sessionid) {

		var icon = Telephony.settings.contact;
		var hint;
		if (direction.type == Telephony.C.CALL_INCOMING) {
			hint = "Nouvel appel entrant";
		} else {
			hint = "Appel en cours";
		}

		var nom_contact="Contact Inconnu";
		if(number !='anonymous'){
			Telephony.getName(number).done(function(data){
				if (data!="" && data.indexOf("+")<0)
					nom_contact=data.split('|');
				$.ajax({
					type: 'POST',
					url: "/admin-light.php?dims_op=telephony&todo_op=getPhoto&ajax=1",
					data: {"number":number},
					success: function(data) {
			        	if(data!=''){
							var n=data.indexOf("data");
							var data = data.substr(n-1,data.length);
							icon=data;
							Telephony.S.photo=icon;
						}
					},
					dataType: "html",
					async:false
				});			
				var html =
				'<div title='+number+' class="telephony-tile-element" data-callref="'+callref+'">'
					+ '<img src="'+icon+'"> <span class="number">'+nom_contact[0]+' '+nom_contact[1]+'<br>'+Telephony.formatPhone(number)+'</span><br>'
					+ hint + ', <br>le ' + php_date("j F Y \à H:i:s", date)
				+ '<br><a id="takecall" href="#" onclick="Telephony.takeNote(\'start\',\''+callref+'\',\''+number+'\',\''+direction.type+'\',\''+sessionid+'\');">Prendre l\'appel</a></div>';
				
				Telephony.S.ongoing[number]={'nc':nom_contact,'photo':icon};
	
				if($("#ongoing-calls").text()=='Aucun appels en cours'){
					$("#ongoing-calls").empty();
				}
					
				else
					$("#ongoing-calls").prepend('</br>');
				$("#ongoing-calls").prepend(html).hide().show(400);
			});		
		}else{
			var html =
				'<div title='+number+' class="telephony-tile-element" data-callref="'+callref+'">'
					+ '<img src="'+icon+'"> <span class="number">Appel anonyme</span><br>'
					+ hint + ', <br>le ' + php_date("j F Y \à H:i:s", date)
				+ '<br><a id="takecall" href="#" onclick="Telephony.takeNote(\'start\',\''+callref+'\');">Prendre l\'appel</a></div>';
			if($("#ongoing-calls").text()=='Aucun appels en cours')
				$("#ongoing-calls").empty();
			else
				$("#ongoing-calls").prepend('</br>');
			$("#ongoing-calls").prepend(html).hide().show(400);
		}
	},

	//en cours d'appel
	onCallConnect: function(direction, callref, number, date) {

		if(Telephony.S.ongoing[number]!=undefined){
			var nc= Telephony.S.ongoing[number]['nc'];
			var icon='';
			if(Telephony.S.ongoing[number]['photo']!=''){
				icon=Telephony.S.ongoing[number]['photo'];
			}else{
				icon = Telephony.settings.contact;
			}
		}else{
			var icon = Telephony.settings.contact;
			var hint;
			if (direction.type == Telephony.C.CALL_INCOMING) {
				hint = "Nouvel appel entrant";
			} else {
				hint = "Appel en cours";
			}

			var nc="Contact Inconnu";
			if(number !='anonymous'){
				Telephony.getName(number).done(function(data){
					if (data!="" && data.indexOf("+")<0)
						nc=data.split('|');
					$.ajax({
						type: 'POST',
						url: "/admin-light.php?dims_op=telephony&todo_op=getPhoto&ajax=1",
						data: {"number":number},
						success: function(data) {
				        	if(data!=''){
								var n=data.indexOf("data");
								var data = data.substr(n-1,data.length);
								icon=data;
							}
						},
						dataType: "html",
						async:false
					});
				});
			}
		}

		Telephony.S.ongoing[number]={'nc':nc,'photo':icon};
		var htmlContained =
			'<div title='+number+' class="telephony-tile-element" data-callref="'+callref+'">';
		if(number !='anonymous'){
			var html =
					'<img src="'+icon+'"> <span class="number">'+nc[0]+' '+nc[1]+'<br>'+Telephony.formatPhone(number)+'</span><br>'
					+ 'Appel en cours : <br> (<span data-start="'+(new Date().getTime()/1000)+'" class="telephony-duration">00 secondes</span>),<br> le ' + php_date("j F Y \à H:i:s", date)
					+ '<div class="clear"></div>'
		}else{
			var html =
					'<img src="'+icon+'"> <span class="number">Appel anonyme</span><br>'
					+ 'Appel en cours : <br> (<span data-start="'+(new Date().getTime()/1000)+'" class="telephony-duration">00 secondes</span>),<br> le ' + php_date("j F Y \à H:i:s", date)
					+ '<div class="clear"></div>'
		}
		htmlContained += html + '</div>';

		var existingCall = $("#ongoing-calls").find("[data-callref='" + callref + "']");

		if (existingCall.length > 0) {
			existingCall.html(html);
		} else {
			$("#ongoing-calls").prepend(htmlContained).hide().show(400);
		}
	},

	//en fin d'appel
	onCallEnd: function(token, direction, callref, number, dateStart, dateEnd) {
		delete Telephony.S.ongoing[number];
		Telephony.hideOngoingItem(callref);
		$("div[title="+number+"]").remove();
		var keys=Object.keys(Telephony.S.ongoing);
		if(keys.length==0){
			$("#ongoing-calls").empty().text("Aucun appels en cours");
		}	
	},

	//évènement appel manqué	
	onCallMissed: function(token, callref, number, date) {
		$.ajax({
	        type: 'POST',
	        url: "/admin-light.php?dims_op=telephony&todo_op=callmissed&ajax=1",
	        dataType: "html",
	        async: true,
		});
		delete Telephony.S.ongoing[number];
		Telephony.hideOngoingItem(callref);
		$("div[title="+number+"]").remove();
		var keys=Object.keys(Telephony.S.ongoing);
		if(keys.length==0){
			$("#ongoing-calls").empty().text("Aucun appels en cours");
		}
		$('#textareatelephony').val("");
		$("#write_note").css('display', 'none');	

		var misscall=$("#missed-calls").data('countmc');
		misscall++;
		$("#missed-calls").data('countmc',misscall);
		$("#missed-calls").empty().text("("+misscall+" appels manqués)");
		Telephony.hideOngoingItem(callref);
	},

	//évènement appel échoué
	onCallFailed: function(token, callref, number, date) {
		$.ajax({
	        type: 'POST',
	        url: "/admin-light.php?dims_op=telephony&todo_op=callmissed&ajax=1",
	        dataType: "html",
	        async: true,
		});
		Telephony.hideOngoingItem(callref);
		delete Telephony.S.ongoing[number];
		Telephony.hideOngoingItem(callref);
		$("div[title="+number+"]").remove();
		var keys=Object.keys(Telephony.S.ongoing);
		if(keys.length==0){
			$("#ongoing-calls").empty().text("Aucun appels en cours");
		}
		$('#textareatelephony').val("");
		$("#write_note").css('display', 'none');
	},

	//timer temps réel	
	updateCallTimers: function() {
		$(".telephony-duration").each(function() {
			var timeCall = parseInt(new Date().getTime() / 1000) - parseInt($(this).data('start'));
			$(this).text(Telephony.secondsToTime(timeCall));
		});
		setTimeout(function() { Telephony.updateCallTimers(); }, 500);
	},


	// UI functions
	_getIcon: function(direction) {
		var icon;

		if (direction.type == Telephony.C.CALL_INCOMING) {
			icon = Telephony.settings.iconIncomingCall;
		} else if (direction.type == Telephony.C.CALL_OUTGOING) {
			icon = Telephony.settings.iconOutgoingCall;
		} else {
			icon = Telephony.settings.iconErrorCall;
		}

		return icon;
	},

	hideOngoingItem: function(callref) {
		var existingCall = $("#ongoing-calls").find("[data-callref='" + callref + "']");

		if (existingCall.length > 0) {
			existingCall.slideUp(500, function() { $(this).remove(); });
		}
	},

	showError: function(title, message) {
		$("#telephony-box-error").slideDown(500).html("<b>"+title+"</b> " + message);
	},

	showInfo: function(title, message) {
		$("#telephony-box-info").slideDown(500).html("<b>"+title+"</b> " + message);
	},

	doChromePopup: function(image, title, message) {
		var havePermission = window.webkitNotifications.checkPermission();
		if (havePermission == 0) {
			// 0 is PERMISSION_ALLOWED
			var notification = window.webkitNotifications.createNotification(
				image, title, message
				);

			notification.onclick = function () {
				window.focus();
				notification.close();
			}

			// Auto-hide after a while
			notification.ondisplay = function(event) {
				setTimeout(function() {
					event.currentTarget.cancel();
				}, 5000);
			};
			notification.show();
		} else {
			window.webkitNotifications.requestPermission();
		}
	},

	secondsToTime: function(seconds) {
		var hours = Math.floor(seconds / 3600);
		var minutes = Math.floor((seconds - hours*3600) / 60);
		var seconds = Math.floor(seconds - hours*3600 - minutes*60);

		if (hours < 10) {
			hours = "0"+hours;
		}
		if (minutes < 10) {
			minutes = "0"+minutes;
		}
		if (seconds < 10) {
			seconds = "0"+seconds;
		}

		if (hours > 0)
			return hours+":"+minutes+":"+seconds;
		else if (minutes > 0)
			return minutes+":"+seconds;
		else
			return seconds+" secondes";
	},

	//format INTERNATIONAL
	formatPhone: function(phoneNumber) {
		var PNF = i18n.phonenumbers.PhoneNumberFormat;
		var phoneUtil = i18n.phonenumbers.PhoneNumberUtil.getInstance();
		var number = phoneUtil.parseAndKeepRawInput("+"+phoneNumber, 'FR');
		return phoneUtil.format(number, PNF.INTERNATIONAL);
	},

	//fonction prise de note
	takeNote: function(action,ref,number,direction,sessionid) {
		

		if(action=='start'){	
			
			//on maj le bouton prendre l'appel
			$('a#takecall').text('Appel pris');
			$('a#takecall').attr('onclick','');

			//on previent que le server qu'on prend l'appel
			$.post(Telephony.settings.proxy, {"action":Telephony.C.ACT_TAKE, "ref":ref, "token":Telephony.S.token, "number":number, "direction":direction, "sessionid":sessionid});

			$('#textareatelephony').val("");
			$("#write_note").css('display', 'block');

			$.ajax({
		        type: 'POST',
		        url: "/admin-light.php?dims_op=telephony&todo_op=callongoin&ajax=1",
		        dataType: "html",
		        async: false,
			});
			
			Telephony.takeNote('ongoing',0);	
		}

		if(action=='ongoing'){
			$("#write_note").css('display', 'block');
			$.ajax({
		        type: 'POST',
		        url: '/admin-light.php?dims_op=telephony&todo_op=telephonynotes_receive&ajax=1',
		        dataType: 'html',
		        async: false,
		        success : function(data){
		        	$('#textareatelephony').text(data);
		        }
			});

			var session=$('#textareatelephony').val();
			$('#textareatelephony').keyup(function () {
				session=($(this).val());
			});

			$(window).on('beforeunload', function() {
			    $.ajax({
			        type: 'POST',
			        url: '/admin-light.php?dims_op=telephony&todo_op=telephonynotes_send&ajax=1',
			        data: { session : session },
			        dataType: 'html',
			        async: false,
				});

			});
		
		}

		if(action=='stop'){
				
				$("#write_note").css('display', 'none');
				
				var session=($('#textareatelephony').val());
				
			    $.ajax({
			        type: 'POST',
			        url: '/admin-light.php?dims_op=telephony&todo_op=telephonynotes_send&ajax=1',
			        data: { session : session },
			        dataType: 'html',
			        async: false,
				});

				$.ajax({
			        type: 'POST',
			        url: "/admin-light.php?dims_op=telephony&todo_op=callend&ajax=1",
			        dataType: "html",
			        data: { callref : ref },
			        success: function(data) {
			        	//console.log('resume de la conversation : '+data);
			        }
				});

				$('#textareatelephony').val("");
			}
		},
};


$( document ).ready(function() {
	$("#telephony-box-error").hide();
	$("#telephony-box-info").hide();

	
	//on charge le token
	$.ajax({
		type: 'POST',
		url: "/admin-light.php?dims_op=telephony&todo_op=reload_token&ajax=1",
		success: function(data) {
        	if(data!=''){
				Telephony.S.token=data;
			}
		},
		dataType: "html",
		async:false
	});	

	Telephony.doPolling();
	Telephony.updateCallTimers();
	
	//appels manqués reset
	$("#logcall").click(function(){
		$("#missed-calls").data('countmc','0');
	});

});




////////////////////////////////////////////////////////////////////////
// EXTERNAL FUNCTIONS
////////////////////////////////////////////////////////////////////////

// From phpjs.org
function php_date (format, timestamp) {
	var that = this,
	jsdate,
	f,
	formatChr = /\\?([a-z])/gi,
	formatChrCb,
	  // Keep this here (works, but for code commented-out
	  // below for file size reasons)
	  //, tal= [],
	  _pad = function (n, c) {
		n = n.toString();
		return n.length < c ? _pad('0' + n, c, '0') : n;
	  },
	  txt_words = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "aôut", "septembre", "octobre", "novembre", "décembre"];
	  formatChrCb = function (t, s) {
		return f[t] ? f[t]() : s;
	  };
	  f = {
	// Day
	d: function () { // Day of month w/leading 0; 01..31
		return _pad(f.j(), 2);
	},
	D: function () { // Shorthand day name; Mon...Sun
		return f.l().slice(0, 3);
	},
	j: function () { // Day of month; 1..31
		return jsdate.getDate();
	},
	l: function () { // Full day name; Monday...Sunday
		return txt_words[f.w()] + 'day';
	},
	N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
		return f.w() || 7;
	},
	S: function(){ // Ordinal suffix for day of month; st, nd, rd, th
		var j = f.j()
		i = j%10;
		if (i <= 3 && parseInt((j%100)/10) == 1) i = 0;
		return ['st', 'nd', 'rd'][i - 1] || 'th';
	},
	w: function () { // Day of week; 0[Sun]..6[Sat]
		return jsdate.getDay();
	},
	z: function () { // Day of year; 0..365
		var a = new Date(f.Y(), f.n() - 1, f.j()),
		b = new Date(f.Y(), 0, 1);
		return Math.round((a - b) / 864e5);
	},

	// Week
	W: function () { // ISO-8601 week number
		var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
		b = new Date(a.getFullYear(), 0, 4);
		return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
	},

	// Month
	F: function () { // Full month name; January...December
		return txt_words[6 + f.n()];
	},
	m: function () { // Month w/leading 0; 01...12
		return _pad(f.n(), 2);
	},
	M: function () { // Shorthand month name; Jan...Dec
		return f.F().slice(0, 3);
	},
	n: function () { // Month; 1...12
		return jsdate.getMonth() + 1;
	},
	t: function () { // Days in month; 28...31
		return (new Date(f.Y(), f.n(), 0)).getDate();
	},

	// Year
	L: function () { // Is leap year?; 0 or 1
		var j = f.Y();
		return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0;
	},
	o: function () { // ISO-8601 year
		var n = f.n(),
		W = f.W(),
		Y = f.Y();
		return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
	},
	Y: function () { // Full year; e.g. 1980...2010
		return jsdate.getFullYear();
	},
	y: function () { // Last two digits of year; 00...99
		return f.Y().toString().slice(-2);
	},

	// Time
	a: function () { // am or pm
		return jsdate.getHours() > 11 ? "pm" : "am";
	},
	A: function () { // AM or PM
		return f.a().toUpperCase();
	},
	B: function () { // Swatch Internet time; 000..999
		var H = jsdate.getUTCHours() * 36e2,
		// Hours
		i = jsdate.getUTCMinutes() * 60,
		// Minutes
		s = jsdate.getUTCSeconds(); // Seconds
		return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
	},
	g: function () { // 12-Hours; 1..12
		return f.G() % 12 || 12;
	},
	G: function () { // 24-Hours; 0..23
		return jsdate.getHours();
	},
	h: function () { // 12-Hours w/leading 0; 01..12<
		return _pad(f.g(), 2);
	},
	H: function () { // 24-Hours w/leading 0; 00..23
		return _pad(f.G(), 2);
	},
	i: function () { // Minutes w/leading 0; 00..59
		return _pad(jsdate.getMinutes(), 2);
	},
	s: function () { // Seconds w/leading 0; 00..59
		return _pad(jsdate.getSeconds(), 2);
	},
	u: function () { // Microseconds; 000000-999000
		return _pad(jsdate.getMilliseconds() * 1000, 6);
	},

	// Timezone
	e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
	  // The following works, but requires inclusion of the very large
	  // timezone_abbreviations_list() function.
/*              return that.date_default_timezone_get();
*/
throw 'Not supported (see source code of date() for timezone on how to add support)';
},
	I: function () { // DST observed?; 0 or 1
	  // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
	  // If they are not equal, then DST is observed.
	  var a = new Date(f.Y(), 0),
		// Jan 1
		c = Date.UTC(f.Y(), 0),
		// Jan 1 UTC
		b = new Date(f.Y(), 6),
		// Jul 1
		d = Date.UTC(f.Y(), 6); // Jul 1 UTC
		return ((a - c) !== (b - d)) ? 1 : 0;
	},
	O: function () { // Difference to GMT in hour format; e.g. +0200
		var tzo = jsdate.getTimezoneOffset(),
		a = Math.abs(tzo);
		return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
	},
	P: function () { // Difference to GMT w/colon; e.g. +02:00
		var O = f.O();
		return (O.substr(0, 3) + ":" + O.substr(3, 2));
	},
	T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
		return 'UTC';
	},
	Z: function () { // Timezone offset in seconds (-43200...50400)
		return -jsdate.getTimezoneOffset() * 60;
	},

	// Full Date/Time
	c: function () { // ISO-8601 date.
		return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb);
	},
	r: function () { // RFC 2822
		return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
	},
	U: function () { // Seconds since UNIX epoch
		return jsdate / 1000 | 0;
	}
};
this.date = function (format, timestamp) {
	that = this;
	jsdate = (timestamp === undefined ? new Date() : // Not provided
	  (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
	  new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
	  );
	return format.replace(formatChr, formatChrCb);
};
return this.date(format, timestamp);
}