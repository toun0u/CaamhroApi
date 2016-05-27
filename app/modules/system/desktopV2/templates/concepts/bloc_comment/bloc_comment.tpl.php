<?php
$_SESSION['desktopv2']['concepts']['comment_search'] = dims_load_securvalue('comment_search',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['comment_search']);

// initialisation des filtres
$init_comment_search = dims_load_securvalue('init_comment_search', dims_const::_DIMS_NUM_INPUT, true, true);
if ($init_comment_search) {
	$_SESSION['desktopv2']['concepts']['comment_search'] = '';
}

// texte du champ de recherche
if ($_SESSION['desktopv2']['concepts']['comment_search'] != '') {
	$text_comment_search = $_SESSION['desktopv2']['concepts']['comment_search'];
	$button['class'] = 'searching';
	$button['href'] = '/admin.php?init_comment_search=1';
	$button['onclick'] = '';
}
else {
	$text_comment_search = $_SESSION['cste']['LOOKING_FOR_A_COMMENT']. ' ?';
	$button['class'] = '';
	$button['href'] = 'Javascript: void(0);';
	$button['onclick'] = 'Javascript: if($(\'input#bloc_editbox_search_comment\').val() != \''.$text_comment_search.'\') $(this).closest(\'form\').submit();';
}

// affichage a gauche ou a droite en fonction de la présence du bloc suivis
/*
if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
	$style = 'float: left; clear: left; width: 100%;';
}
else {
*/
	//$style = 'float: right;';
/*
}
*/
?>

<div class="bloc_comment">
    <div class="title_bloc_comment">
		<h2><? echo $_SESSION['cste']['_DIMS_COMMENTS']; ?></h2>
	</div>
    <div class="bloc_zone_search_comment bloc_zone_search">
        <div class="bloc_searchform_comment">
            <form action="admin.php" method="post" name="formsearch" id="bloc_formsearch_comment">
            	<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
					$token->field("button_search_y");
					$token->field("comment_search");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
            	?>
                <span>
                    <input type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left">
                    <input type="text" name="comment_search" id="bloc_editbox_search_comment" class="editbox_search<? if ($button['class'] == 'searching') echo ' working'; ?>" maxlength="80" value="<?php echo htmlspecialchars($text_comment_search); ?>" <? if ($button['class'] != 'searching') echo 'onfocus="Javascript:this.value=\'\'; $(this).addClass(\'working\');"'; ?> onblur="Javascript:if (this.value==''){ $(this).removeClass('working'); this.value='<?php echo htmlspecialchars(addslashes($text_comment_search)); ?>'; }">
                    <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;">

					<a class="<?php echo $button['class']; ?>" href="<?php echo $button['href']; ?>" onclick="<?php echo $button['onclick']; ?>"></a>
                </span>
            </form>
        </div>
    </div>
    <div class="cadre_bloc_comment">
        <div class="add_comment">
			<div>
				<a href="Javascript: void(0);" onclick="javascript:addCommentConcepts(event);">
					<span><?php echo $_SESSION['cste']['ADD_COMMENT_NO_EDIT']; ?></span>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png" />
				</a>
			</div>
        </div>
        <div class="add_comment_content">
			<?
			$lstComments = $this->getAnnotations();
			foreach($lstComments as $comment) {
				if ( $_SESSION['desktopv2']['concepts']['comment_search'] == '' || stristr($comment->fields['content'], $_SESSION['desktopv2']['concepts']['comment_search']) ) {
					$comment->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_comment/fiche_bloc_comment.tpl.php');
				}
			}
			?>
        </div>
    </div>
</div>
