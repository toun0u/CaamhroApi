<?php
$v = view::getInstance();

// Alias 2 speed it up
$tabs                   = $v->get('tabs');
$site                   = $v->get('site');
$user                   = $v->get('user');
$layoutTab              = $v->get('layoutTab');
$logged_in              = $v->get('switch_user_logged_in');
$workspaces             = $v->get('workspaces');
$logged_out             = $v->get('switch_user_logged_out');
$pass_forgotten         = $v->get('pass_forgotten');
$switch_dimserrormsg    = $v->get('switch_dimserrormsg');
$modules_js             = $v->get('modules_js');
$cste = $_SESSION['cste'];
?>
<!doctype html>
<!--[if IE 9]><html class="ie9"><![endif]-->
<!--[if IE 8]><html class="ie8"><![endif]-->
<!--[if IE 7]><html class="ie7"><![endif]-->
<!--[if gt IE 9]><!--><html><!--<![endif]-->
<head>
	<title><?php echo $site['WORKSPACE_TITLE']; ?></title>
	<meta charset="<?php echo $site['ENCODING'] ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=10">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo $site['WORKSPACE_META_DESCRIPTION'] ?>" />
	<meta name="keywords" content="<?php echo $site['WORKSPACE_META_KEYWORDS'] ?>" />
	<meta name="author" content="<?php echo $site['WORKSPACE_META_AUTHOR'] ?>" />
	<meta name="copyright" content="<?php echo $site['WORKSPACE_META_COPYRIGHT'] ?>" />
	<meta name="robots" content="<?php echo $site['WORKSPACE_META_ROBOTS']; ?>" />
	<link rel="icon" href="<?php echo $site['TEMPLATE_IMG_PATH']; ?>/img/favicon.png" type="image/png" />

	<!-- Style manager -->
	<?php echo $v->get('styles'); ?>
	<!-- Script manager -->
	<?php echo $v->get('scripts'); ?>
	<!-- NS_CSS -->
	<?php echo $site['DIMS_NS_CSS']; ?>
	<!-- Additionnal_head -->
	<?php echo $site['ADDITIONAL_HEAD']; ?>
</head>

<?php if(isset($logged_out)) : ?>
	<div class="txtcenter w40 m-auto white-box br10 logbox pa3">

		<h1>Bienvenue sur Dims</h1>
		<hr class="mb3">

		<div id="dims_connexion" style="display:<?php if(isset($pass_forgotten)){ echo 'none'; } else { echo 'bloc'; } ?>">

			<?php if(isset($switch_dimserrormsg)) : ?>
				<div class="flash-bag error">
					Login et/ou mot de passe incorrect, veuillez réessayer.
					<?php // echo $site['DIMS_ERROR']; ?>
				</div>
			<?php endif; ?>

			<form name="formlogin" action="admin.php" method="post" onsubmit="document.formlogin.submit();">
				<div class="form-align">
					<input type="text" id="dims_login" name="dims_login" placeholder="Login" required autofocus/>
				</div>

				<div class="form-align mt2">
					<input type="password" id="dims_password" name="dims_password" placeholder="Mot de passe" required/>
				</div>

				<div class="mt3">
					<input type="submit" value="Connexion" class="btn btn-success btn-large" aria-disabled="false"/> <br>
					<a href="#" onclick="javascript:document.getElementById('dims_connexion').style.display='none';document.getElementById('lostpass').style.display='block';">Mot de passe oublié ?</a>
				</div>
			</form>
		</div>
		<div id="lostpass" style="display:<?php if(isset($pass_forgotten)){ echo 'bloc'; } else { echo 'none'; } ?>;">
			<form name="formpassword" action="admin.php?dims_op=forgot_password" method="post" onsubmit="document.formpassword.submit();">
				<div id="loginbackground">

					Merci de remplir le formulaire suivant à l'aide de votre adresse e-mail.

					<?php
					if(isset($pass_forgotten)) :
						if(is_array($pass_forgotten) && array_key_exists('error', $pass_forgotten)):
					?>
							<div class="flash-bag error"><?php echo $pass_forgotten['error']; ?></div>
					<?php
						else:
					?>
							<div class="flash-bag success"><?php echo $pass_forgotten; ?></div>
					<?php
						endif;
					endif;
					?>

					<div class="form-align mt2">
						<input type="email" id="dims_email" name="dims_email" tabindex="1" placeholder="Adresse e-mail" required>
					</div>
					<div class="mt2">
						<input type="submit" class="btn btn-success btn-large" onclick="javascript:document.formpassword.submit();" aria-disabled="false" value="Valider"/>
						<br>
						<a href="#" onclick="javascript:window.location.href='./admin.php';">Annuler</a>
					</div>
					<? if (isset($pass_forgotten)) unset($pass_forgotten); ?>
				</div>
			</form>
		</div>

	<div class="smaller mt2">
		Powered by <a href="http://www.dims.fr">DIMS Portal</a>
		<br>
		<?php echo $site['DIMS_VERSION']; ?>&nbsp;|&nbsp;<a class="whitelink" href="http://www.mozilla-europe.org/fr/products/firefox/">Get Firefox</a>
		&nbsp;| render: <?php echo $site['DIMS_EXEC_TIME']; ?> ms | sql: <?php echo $site['DIMS_NUMQUERIES']; ?> q (<?php echo $site['DIMS_SQL_P100']; ?> %)
	</div>
	<hr class="mt2">
<?php else: ?>

<div id="popup_container">
	<div id="dims_popup" class="dims_popup ui-dialog ui-widget ui-widget-content ui-corner-all" style="display:none;"></div>
	<div id="dims_popup2" class="dims_popup"></div>
	<div id="overlay" class="overlay"></div>
	<div id="overlay2" class="overlay"></div>
</div>

<div class="menu-wrapper">
	<a href="#menu" class="menu-link">	Menu <span class="icon-menu right" aria-hidden="true"></span></a>
	<nav id="menu" role="navigation">
		<div class="menu">
			<ul  class="menu">
				<!-- <li class="current-menu-item"> -->
				<li>
					<a href="admin.php?dims_mainmenu=0&dims_desktop=block&dims_action=public&submenu=0&dims_moduleid=1&init_desktop=1&mode=default"><i class="icon-home"></i> <span class="large-hidden">Accueil</span></a>
				</li>
				<?php
				 if(!empty($tabs) && count($tabs) >= 1) : ?>
				<li class="has-subnav phone-hidden">
					<a href="#">Modules</a>
					<ul class="sub-menu">
						<?php foreach($tabs as $module) : ?>
							<li>
								<a href="<?php echo $module['URL']; ?>" title="<?php echo $module['TITLE']; ?>">
									<?php echo $module['TITLE']; ?>
								</a>
							</li>
							<?php
							if(!empty($module['MODULES'])):
								foreach($module['MODULES'] as $sub) : ?>
									<li>
										<a href="<?php echo $sub['URL']; ?>" title="<?php echo $sub['DESC']; ?>">
											&nbsp; &nbsp; <img style="vertical-align:middle;" border="0" src="<?= $sub['EXT']; ?>"/>
											<?php echo $sub['TITLE']; ?>
										</a>
									</li>
								<?php endforeach;
							endif;
							?>
						<?php endforeach; ?>
					</ul>
				</li>
				<?php endif; ?>
				<div class="right-desk">

					<!-- Simon Lejal 16/05/14 mod_telephony chevalliberte -->
					<div id='write_note' class='telephony-token' data-token='' style='width:350px;position: absolute;top: 0px;right: 40%;display:none;'>
							<div id='deplace_note'>Prise de note de l'appel en cours !</div>
							<textarea id='textareatelephony' style='overflow:visible; min-width:100%;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;' ></textarea>
					</div>

					<!-- Déplacement de la prise de note -->
					<script>
					$('#deplace_note').mousedown(function(event) {
						$("#write_note").draggable()
					});
					</script>

					<!-- script pour telephony (nécessite d'avoir activer phoneforvoip dans le form de dimsuser) -->
					<?php
					if( (isset($_SESSION['dims']['user']['phone'])) && (isset($_SESSION['dims']['user']['phoneforvoip'])) ){
						if( ($_SESSION['dims']['user']['phone']!=null) && ($_SESSION['dims']['user']['phoneforvoip']==1) ){
							?>
								<script type="text/javascript" src="/common/js/telephony/libphonenumber2.js"></script>
								<script type="text/javascript" src="/common/js/telephony/telephony.js"></script>
								<script type="text/javascript" src="<?php echo $site['SCRIPTS_COMMON_PATH'].'voip_call.js'; ?>"></script>
							<?php
						}
					}

					// script de prise de note
					if(isset($_SESSION['dims']['mod_telephony']['0'])){
						if($_SESSION['dims']['mod_telephony']['0']==true){
							?>
							<script type='text/javascript'>
								Telephony.takeNote('ongoing',0);
							</script>
						<?php
						}
					}
					?>



					<?php if (isset($_SESSION['dims']['constantizer']) && $_SESSION['dims']['constantizer']){ ?>
						<li>
							<a id="display_constantizer" href="javascript:void(0);">
								<i class="icon-info"></i> <?= $_SESSION['cste']['_CONSTANT']; ?>
							</a>
						</li>
					<?php } ?>
					<li <?php if(count($workspaces) > 1) : ?> class="has-subnav" <?php endif;?> >
						<?php
						foreach($workspaces as $workspace) :
							if($workspace['SELECTED'] == 'selected'):
						?>
							<a href="<?php echo $workspace['URL']; ?>">
								<i class="icon-tree"></i>
								<?php echo $workspace['TITLE']; ?>

						<?php
							endif;
						endforeach; ?>
						</a>
						<?php if(count($workspaces) > 1) : ?>
						<ul class="sub-menu">
							<?php
							foreach($workspaces as $workspace) :
								if($workspace['SELECTED'] != 'selected'):
							?>
							<li>
								<a href="<?php echo $workspace['URL']; ?>"><?php echo $workspace['TITLE']; ?></a>
							</li>
							<?php
								endif;
							endforeach; ?>
						</ul>
						<?php endif; ?>
					</li>
					<li class="has-subnav phone-hidden">
						<a href="#"><i class="icon-cogs"></i> Administration</a>
						<ul class="sub-menu">
							<?php if( (isset($user['ACCESGROUPMANAGER']) && $user['ACCESGROUPMANAGER'] !='') ) : ?>
								<li><?php echo $user['GROUPMANAGER_WITHLABEL']; ?></li>
							<?php endif; ?>
							<?php if( (isset($user['ACCESWORKSPACES']) && $user['ACCESWORKSPACES'] !='') ) : ?>
								<li><?php echo $user['ACCESWORKSPACES_WITHLABEL']; ?></li>
							<?php endif; ?>
						</ul>
					</li>
					<li>
						<a href="<?php echo $site['MAINMENU_SHOWPROFILE_URL']; ?>">
							<i class="icon-user"></i>
							<?php echo $user['FIRSTNAME']; ?> <?php echo $user['LASTNAME']; ?>
						</a>
					</li>
					<li>
						<a href="<?php echo $site['USER_DECONNECT']; ?>"><i class="icon-exit"></i> Déconnexion</a>
					</li>
				</div>
			</ul>
		</div>
	</nav>
</div>

<div class="wrap">
	<div class="line">

		<?php if(!empty($layoutTab)): ?>
		<div id="onglets" class="left phone-hidden">
			<div id="inside-onglet" class="tabs-pure">
				<?php foreach($layoutTab as $item):?>
				<div class="item <?php if($item->state == 1) echo 'tab-active';?>">
					<div class="close-item-tab">
						<a href="<?= $item->link;?>" data-id="<?= $item->id;?>" onclick="return closeTab(this);"><i class="icon-close"></i></a>
					</div>
					<?php if($item->state == 1): ?>
						<span><?= $item->label;?></span>
					<?php else: ?>
						<a href="<?= $item->link;?>" data-id="<?= $item->id;?>" onclick="return changeTab(this);"><?= $item->label;?></a>
					<?php endif; ?>
				</div>
				<?php endforeach;?>
				<div class="txtcenter"><a href="#" onclick="return closeAllTab(this);">Fermer tous les onglets</a></div>
			</div>
		</div>
		<?php endif; ?>
		<div class="pbo-content wrap-main">

			<div class="external-content">
				<?php echo $site['PAGE_CONTENT']; ?>
			</div>
			<div class="line"></div>

		</div>
	</div> <!-- !line -->
</div> <!-- !wrap -->

<div class="right mr3 mt2 mb2 pr1">
	Powered by Dims - &copy; 2013 Netlor SAS
</div>
<?php endif; ?>
<script type="text/javascript">
$(document).ready(function() {

	/* the Responsive menu script */
	$('body').addClass('js');
	<?php
	if(!isset($_SESSION['dims']['user']['phone'])) $_SESSION['dims']['user']['phone'] = 'null';
	if(!isset($_SESSION['dims']['user']['phoneforvoip'])) $_SESSION['dims']['user']['phoneforvoip'] = 0;
	?>
	if( ('<?= $_SESSION['dims']['user']['phone']; ?>' != "null") && (<?php echo $_SESSION['dims']['user']['phoneforvoip'] ?> == 1) )
		$.each($("span[data-phone]:not([data-phoneone])"),function(){
			$(this).voip_call('<?= $_SESSION['dims']['user']['phone']; ?>','/common/modules/system/desktopV2/templates//gfx/common/tel_sortant16.png');
		});

	var $menu = $('#menu'),
		$menulink = $('.menu-link'),
		$menuTrigger = $('.has-subnav > a'),
		$menuDash = $('#dashmenu');

	$menulink.each(function(i) {
		$(this).click(function(e) {
			e.preventDefault();
			$menulink.toggleClass('active');

			if(i == 0)
				$menu.toggleClass('active');
			if(i == 1)
				$menuDash.toggleClass('active');
		});
	});

	var add_toggle_links = function() {

		$('.menu-link').each(function(j) {
			if ($('.menu-link').is(":visible")){
				if ($(".toggle-link").length > 0){
				}
				else{
					$('.has-subnav > a').before('<span class="toggle-link"> Open submenu </span>');
					$('.toggle-link').click(function(e) {
						var $this = $(this);
						$this.toggleClass('active').siblings('ul').toggleClass('active');
					});
				}
			}
			else{
				$('.toggle-link').empty();
			}
		})
	}

	add_toggle_links();
	$(window).bind("resize", add_toggle_links);

});

</script>

<?php if($user['REAL_LANGUAGE'] != 'en') : ?>
	<script type="text/javascript" src="<?php echo $site['ROOT_PATH']; ?>/js/datepicker_lang/jquery.ui.datepicker-<?= ($user['REAL_LANGUAGE']!='')?$user['REAL_LANGUAGE']:"fr"; ?>.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$.datepicker.setDefaults( $.datepicker.regional["<?= ($user['REAL_LANGUAGE']!='')?$user['REAL_LANGUAGE']:"fr"; ?>"] );
		});
	</script>
<?php else : ?>
	<div id=""></div>
<?php endif; ?>


<?php foreach($modules_js as $module_js) : ?>
<script type="text/javascript" src="<?php echo $module_js['PATH']; ?>"></script>
<?php endforeach; ?>
<!--[if IE]><script type="text/javascript" src="/js/excanvas.js"></script><![endif]-->
<script language="javascript">
<?php echo $site['ADDITIONAL_JAVASCRIPT']; ?>
</script>

<script type="text/javascript">
function closeTab(e) {

	var ref = $(e).attr('href'),
		id = $(e).data('id');

	$.ajax({
		type: 'GET',
		url: 'admin.php?c=tab&a=remove',
		data: { link : ref },
		dataType: "json",
		success: function(data) {
			if(data.redirect) {
				window.location.href = data.redirect;
			}
		}
	});

	return false;
}

function closeAllTab(e) {

	$.ajax({
		type: 'GET',
		url: 'admin.php?c=tab&a=destroy',
		dataType: "json",
		success: function(data) {
			if(data.redirect) {
				window.location.href = data.redirect;
			}
		}
	});

	return false;
}

function changeTab(e) {

	var ref = $(e).attr('href'),
		idtab = $(e).data('id');

	$.ajax({
		type: 'GET',
		url: 'admin.php?c=tab&a=change',
		data: { id : idtab },
		dataType: "json",
		success: function(data) {
			if(data.redirect) {
				window.location.href = data.redirect;
			}
		}
	});

	return false;
}

function singleClick(e) {
	var ref = $(this).attr('href');

	if($(this).data('open') != true) {
		$.ajax({
			type: 'GET',
			url: 'admin.php?c=tab&a=update',
			data: { link : ref },
			dataType: "json",
			success: function(data) {
				if(data.redirect) {
					window.location.href = data.redirect;
				}
			}
		});
	}

	return false;
}

function doubleClick(e) {
	var ref = $(this).attr('href');

	if($(this).data('open') != true) {
		$.ajax({
			type: 'GET',
			url: 'admin.php?c=tab&a=create',
			data: { link : ref },
			dataType: "json",
			success: function(data) {
				if(data.redirect) {
					window.location.href = data.redirect;
				}
			}
		});
	}

	return false;
}

$(document).delegate('*[data-tabable="true"]','click',function(e) {
	e.preventDefault();

	var item = this;
	setTimeout(function() {
		var dblclick = parseInt($(item).data('double'), 10);
		if (dblclick > 0) {
			$(item).data('double', dblclick-1);
		} else {
			singleClick.call(item, e);
		}
	}, 300);
}).delegate('*[data-tabable="true"]','dblclick',function(e) {
	$(this).data('double', 2);
	doubleClick.call(this, e);
});

keepConnexion();

<?php if (isset($_SESSION['dims']['constantizer']) && $_SESSION['dims']['constantizer']){ ?>
	$('document').ready(function(){
		initConstantizer($('a#display_constantizer'));
	});
<?php } ?>
</script>
</body>
</html>
