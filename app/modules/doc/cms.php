<?php
dims_init_module('doc');
require_once(DIMS_APP_PATH . "/include/functions/image.php");
require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfolder.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfiledraft.php';

if(!isset($op)) $op = '';

global $template_name;
global $nav;
global $idpage;
global $op2;

global $_DIMS;
global $business_jour;
global $business_mois;
global $articleid;
global $headingid;

if (isset($op2)) $op = $op2;

switch($op) {
	case 'browse':
		$folder_id	= $obj['object_id'];
		include(DIMS_APP_PATH . '/modules/doc/cms_folder_display.php');
		break;
	case "display_gallery":
		$select_galery = "SELECT g.*,u.lastname,u.firstname
							FROM dims_mod_doc_gallery as g
							LEFT JOIN dims_user as u
							ON	u.id=g.id_user
							WHERE g.id = :objectid";

		$current_page=dims_load_securvalue('current_page',dims_const::_DIMS_NUM_INPUT,true,true);
		$heading_id=dims_load_securvalue('heading_id',dims_const::_DIMS_NUM_INPUT,false,true);
		$articleid=dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true);
		$headingid=dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true);

		$result_galery = $db->query($select_galery, array(':objectid' => $obj['object_id']) );

		if ($row_galery = $db->fetchrow($result_galery)) {
			if ($current_page==0) $current_page = 1;

			$picture_extension = "'jpeg', 'jpg', 'gif', 'png', 'bmp','avi','mpeg','mpg','mov','wmv','mp4', ";
			$textfile_extension = "'pdf', 'doc', 'docx', 'odt', 'txt', ";
			$compressfile_extension = "'zip', 'rar', 'ace', 'tar.bz2', 'tar.gz', 'tgz', '7z', ";

			$extension = "AND extension IN (";

			if ($row_galery['show_picture']=='yes') $extension .= $picture_extension;
			if ($row_galery['show_textfile']=='yes') $extension .= $textfile_extension;
			if ($row_galery['show_compressfile']=='yes') $extension .= $compressfile_extension;

			$title=$row_galery['name'];
			$description=$row_galery['description'];
			$author=$row_galery['firstname']." ".$row_galery['lastname'];
			$extension = substr($extension,0,-2);
			$extension .= ")";

			$select_picture = "SELECT * FROM dims_mod_doc_file WHERE id_folder = :idfolder {$extension} order by name";
			$result_picture = $db->query($select_picture, array(':idfolder' => $row_galery['id_folder'] ) );
			$nb_picture = $db->numrows($result_picture);

			$nb_picture_page = intval($row_galery['nb_column']) * intval($row_galery['nb_row']);

			$id_start = ($current_page-1) * $nb_picture_page;
			$id_end = $id_start + $nb_picture_page;
			$cpt = $id_start;

			$select_picture_display = "SELECT * FROM dims_mod_doc_file WHERE id_folder = :idfolder	{$extension} ORDER BY name";// LIMIT {$id_start},{$id_end}";

			$result_picture_display = $db->query($select_picture_display, array(':idfolder' => $row_galery['id_folder'] ) );

			?>
			<style>
			/*----------------------------
				Thumbnails
			-----------------------------*/

			.thumbs{
				width:auto;
				margin:10px auto 10px;
				text-align:center;
			}

			.thumbs a{
				width:120px;
				height:120px;
				display:inline-block;
				border:5px solid #303030;
				box-shadow:0 1px 3px rgba(0,0,0,0.5);
				border-radius:4px;
				margin: 6px 6px 40px;
				position:relative;
				text-decoration:none;

				background-position:center center;
				background-repeat: no-repeat;

				background-size:cover;
				-moz-background-size:cover;
				-webkit-background-size:cover;
			}

			.thumbs a:after{
				background-color: #303030;
				border-radius: 7px;
				bottom: -136px;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
				color: #FFFFFF;
				content: attr(title);
				display: inline-block;
				font-size: 10px;
				max-width: 90px;
				overflow: hidden;
				padding: 2px 10px;
				position: relative;
				text-align: center;
				white-space: nowrap;
			}

			/*----------------------------
				Media Queries
			-----------------------------*/

			@media screen and (max-width: 960px) {
				.thumbs, #credit{
					width:auto;
				}

				#bsaHolder{
					display:none;
				}
			}
			</style>
			<h1 class="ultima"><?= $row_galery['name']; ?></h1>
			<p>
				<?= nl2br($row_galery['description']); ?>
			</p>
			<div data-gallery="<?= str_replace('"','\"',$row_galery['name']); ?>" class="thumbs">
			<?php
			$preloads = array();
			while($r = $db->fetchrow($result_picture_display)){
				$doc = new docfile();
				$doc->openFromResultSet($r);
				//$preloads[] = $doc->getwebpath();
				?>
				<a href="<?= $doc->getThumbnail(1024,null,true); /*$doc->getwebpath();*/ ?>" style="background-image:url(<?= $doc->getThumbnail($row_galery['small_width']); ?>);" title="<?= ($doc->get('description')!='')?nl2br($doc->get('description')):$doc->get('name'); ?>"></a>
				<?php
			}
			?>
			</div>
			<!--<link rel="stylesheet" href="/common/js/touchTouch/assets/css/styles.css" />-->
			<link rel="stylesheet" href="/common/js/touchTouch/assets/touchTouch/touchTouch.css" />
			<script src="/common/js/touchTouch/assets/touchTouch/touchTouch.jquery.js"></script>
			<script type="text/javascript">
			$(function(){
				$('.thumbs a').touchTouch();
			});
			/*$(document).ready(function(){
				var preloads = ['<?= implode("','",$preloads); ?>'];
				$(preloads).each(function(){$('<img />')[0].src = this;});
			});*/
			</script>
			<?php
		}
	break;
}
?>
