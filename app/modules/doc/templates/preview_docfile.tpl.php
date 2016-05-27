<?php

$src_w = 0;//$size[0];
$src_h = 0;//$size[1];
?>
<link type="text/css" rel="stylesheet" href="modules/doc/templates/styles.css" media="screen" />
<script language="javascript">
    var ratio = new Array(10);
    var wimg=<? echo $src_w; ?>;
    var himg=<? echo $src_h; ?>;
    var cour=5;
    ratio[0]=0.10;
    ratio[1]=0.20;
    ratio[2]=0.25;
    ratio[3]=0.33;
    ratio[4]=0.5;
    ratio[5]=1;
    ratio[6]=2;
    ratio[7]=3;
    ratio[8]=4;
    ratio[9]=5;

function resizeImage(sens) {
    if (sens==0) {
        // on reduit
        if (cour>0) cour--;
    }
    else {
        // on zoom
        if (cour<9) cour++;
    }

    updateImage();
}

function updateImage() {
    // on redimensionne l'image
    var img=document.getElementById('objpreviewdoc');
    var newx=wimg*ratio[cour];
    var newy=himg*ratio[cour];

    img.width=newx;
    img.height=newy;
}

function updateView() {
	if( window.innerWidth) {
		x = window.innerWidth;
	}
  	else {
  		x=document.body.offsetWidth;
  	}

	var wdiv=x/2+20;

        // 800 => 2200
        // calcul du ratio
        var rat=1/(wimg/wdiv);

        for (i=0;i<=9;i++) {
            if (ratio[i]<rat) {
                cour=i;
            }
        }

        updateImage();
        var divpreview=document.getElementById('divpreviewdoc');

        divpreview.style.width=wdiv+"px";
        var img=document.getElementById('objpreviewdoc');
        img.style.visibility="visible";
}


window.onload = function() {
    //updateView();
}

window.onresize = function (){
    //updateView();
}
</script>

<?php
require_once DIMS_APP_PATH.'modules/system/class_category.php';
class preview_docfile {
	public static function display(docfile $doc, $popupid) {
            global $_DIMS;
		$user = new user();
		$user->open($doc->fields['id_user_modify']);
		?>
		<div id="doc_preview_popup">
			<div class="actions">
				<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $popupid; ?>');">
					<img src="./common/img/close.png" />
				</a>
			</div>
			<h2>
				<?php
				echo $doc->fields['name'];
				?>
			</h2>

			<div class="aside">
				<div class="infos_doc">
					<h3>
						<?php echo $doc->fields['name']; ?>
					</h3>
					<div>
						<span class="label">
							<?php
							echo $_SESSION['cste']['_DIMS_DATE'];
							?> :
						</span>
						<span class="value">
							<?php
							$create_date = dims_timestamp2local($doc->fields['timestp_create']);
							echo $create_date['date'];
							?>
						</span>
					</div>
					<div>
						<span class="label">
							<?php
							echo $_SESSION['cste']['_AUTHOR'];
							?> :
						</span>
						<span class="value">
							<?php
								if (isset($user->fields['firstname']) && $user->fields['firstname']!='') {
									echo $user->fields['firstname'].' '.$user->fields['lastname'];
								}
								else {
									echo "-";
								}
							?>
						</span>
					</div>
					<div>
						<span class="label">
							<?php
							echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTIF'];
							?> :
						</span>
						<span class="value">
							<?php
							echo $doc->fields['description'];
							?>
						</span>
					</div>
					<div>
					<?php
/*
 * 	//TODO : tags
						<span class="label">
							<?php
							echo $_SESSION['cste']['_DIMS_LABEL_TAGS'];
							?> :
						</span>
						<span class="value">

						</span>
*/
					?>
					</div>
				</div>
				<div>
					<h3>
						<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
					</h3>
					<div>
						<a href="?dims_op=doc_file_download&docfile_md5id=<?php echo $doc->fields['md5id']; ?>">
							<img src="./common/img/save.gif" />
							<?php echo $_SESSION['cste']['_DIMS_DOWNLOAD']; ?>
						</a>
					</div>
					<div>
						<a href="Javascript: void(0);" onclick="Javascript: if(confirm('<?php echo $_DIMS['cste']['_DIMS_LABEL_CONFIRM_ACTION']; ?>')) { deleteProcedure('<?php echo $doc->fields['md5id'];?>'); dims_closeOverlayedPopup('<?php echo $popupid; ?>'); }">
							<img src="./common/img/delete.gif" />
							<?php echo $_SESSION['cste']['_DELETE']; ?>
						</a>
					</div>
				</div>
			</div>
			<div class="preview">
				<?php
				echo $doc->getPreview();
				?>
			</div>
		</div>
		<?php
	}
}
