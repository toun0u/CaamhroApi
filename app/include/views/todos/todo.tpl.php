<?php
//formulaire générique de création d'un todo
$from_origin = $this->getLightAttribute('from');
$link_to_object = '';
if( isset($from_origin) && $from_origin == 'desktop' ){
	$gobject = $this->getLightAttribute("gobject");
	$addons_classes = $gobject->getLightAttribute('additional_object_classes');
	if(!isset($addons_classes)) $addons_classes = '';
	$link_to_object = ' '.$gobject->getLightAttribute('on_the_record').' <a href="'.$gobject->getLightAttribute('link_to').$gobject->getLightAttribute('home_param').'" class="a_action '.$addons_classes.'">'.$gobject->getLightAttribute('title_object').'</a>';
}

?>
<a name="todo_<?= $this->getId();?>"></a>
<div class="todo <?php if($this->fields['id_parent'] == 0) echo ' ground0'; ?>" id="todo_<?= $this->getId(); ?>">
	<div class="details">
		<div class="avatar">
			<?php
			$creator = $this->getLightAttribute('creator');
			global $_DIMS;
			$file = $creator->getPhotoPath(40);//real_path
			if(file_exists($file)){
				?>
				<img class="picture" src="<?php echo $creator->getPhotoWebPath(40); ?>" width="24px" height="24px">
				<?php
			}
			else{
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human40.png" width="24px" height="24px">
				<?php
			}
			?>
		</div>
		<div class="description">
			<?php
			if( ! $this->isSimpleMessage() ){//c'est que c'est un vrai todo
				?>
				<div class="task_img">
					<?php
					if( !$this->isValidated() ){
						if($this->fields['type'] == todo::TODO_TYPE_WITH_ALL_DEST_VALIDATION){
							$picto = 'accuse40.png';
						}
						else $picto = 'task40.png';
						?>
						<img width="24px" height="24px" src="./common/img/views/todos/gfx/<?= $picto; ?>" alt="<?= $_SESSION['cste']['TASK_TO_REALIZE']; ?>" title="<?= $_SESSION['cste']['TASK_TO_REALIZE']; ?>"/>
						<?php
					}
					else{
						if($this->fields['type'] == todo::TODO_TYPE_WITH_ALL_DEST_VALIDATION){
							$picto = 'accuse40_validated.png';
						}
						else $picto = 'task40_green.png';
						?>
						<img width="24px" height="24px" src="./common/img/views/todos/gfx/<?= $picto; ?>" alt="<?= $_SESSION['cste']['TODO_VALIDATED']; ?>" title="<?= $_SESSION['cste']['TODO_VALIDATED']; ?>"/>
						<?php
					}
					?>
				</div>
				<?php
			}
			else if( $this->fields['is_validator']){
				?>
				<div class="task_img">
					<img width="24px" height="24px" src="./common/img/views/todos/gfx/todo_validated.png" alt="<?= $_SESSION['cste']['VALIDATION_COMMENT']; ?>" title="<?= $_SESSION['cste']['VALIDATION_COMMENT']; ?>"/>
				</div>
				<?php
			}

			if($this->fields['id_user'] == $_SESSION['dims']['userid'])
				$from = $_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF'];
			else $from = $creator->fields['firstname']. ' '.$creator->fields['lastname'];
			echo $_SESSION['cste']['_DIMS_LABEL_FROM'].' <span class="maj_par">'.$from; ?></span>
			<?php
			$this->initDestinataires();
			$dests = $this->getListDestinataires();
			$nb_dests = count($dests);
			if($nb_dests > 0) echo $_SESSION['cste']['POUR'].' ';
			$i = 1;
			foreach($dests as $dest){
				$dest_id = $dest->getUserID();
				if($dest->getUserID() == $_SESSION['dims']['userid']){
					$destinataire = $_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF'];
				}
				else{
					$u = new user();
					$u->open($dest->getUserID());
					if( ! $u->isNew() )
						$destinataire = $u->fields['firstname'].' '.$u->fields['lastname'];
					else $destinataire = $_SESSION['cste']['_DIMS_UNKNOWNUSER'];
				}
				if( ! $dest->fields['validated'] )
					echo '<span class="maj_par">'.$destinataire.'</span>';
				else echo '<span class="validated_par">'.$destinataire.'</span>';
				if($i < $nb_dests) echo ', ';
				$i++;
			}
			echo $link_to_object;
			$date = dims_timestamp2local($this->fields['timestp_create']);
			?>
			<br/>
			<span class="dates"><?php echo ucfirst($_SESSION['cste']['SINGLE_THE']). ' '.$date['date'].' '.strtolower($_SESSION['cste']['_AT']).' '.$date['time'];?></span>
		</div>
	</div>

	<div class="message">
		<?= nl2br($this->fields['content']); ?>
	</div>
	<div class="actions <?php if (isset($from_origin) && $from_origin == 'desktop') echo 'content_right'; ?>" id="actions_<?= $this->getId();?>">
		<?php
		if( isset($from_origin) && $from_origin == 'desktop' ){
			?>
			<a class="a_action" href="<?= $gobject->getLightAttribute('link_to').$gobject->getLightAttribute('todo_param'); ?>"><?php if($this->isSimpleMessage()) echo $_SESSION['cste']['SEE_THE_MESSAGE']; else echo $_SESSION['cste']['SEE_THE_TODO']; ?></a>
			<?php
		}
		else{
			?>
			<a class="a_action" href="javascript:void(0);" onclick="javascript:show_response_form('<?= $this->getId(); ?>' , <?= $this->fields['id_globalobject_ref']; ?> , '<?= base64_encode($this->getLightAttribute('keep_context'));?>' );"><?= $_SESSION['cste']['ANSWER_ACTION']; ?></a>
			<?php
		}
		if($this->fields['id_user'] == $_SESSION['dims']['userid'] && ( ! isset($from_origin) || $from_origin != 'desktop') ){//alors l'utilisateur peut éditer le contenu du todo
			?>
			<a class="a_action" href="<?= dims::getInstance()->getScriptEnv().'?todo_op='.dims_const::_EDIT_INTERVENTION.'&todo_id='.$this->getId().$this->getLightAttribute('keep_context');?>"><?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?></a>
			<?php
		}

		if( ! $this->isSimpleMessage() && ! $this->isValidated() && ! $this->isValidatedByUser($_SESSION['dims']['userid']) && ( $this->fields['id_user'] == $_SESSION['dims']['userid'] || ($this->hasDestinataire($_SESSION['dims']['userid']) ) )){//si c'est un todo
			$redirect_on = $this->getLightAttribute('redirect_on');
			if(empty($redirect_on)) $redirect_on ='';
			if( isset($from_origin) && $from_origin == 'desktop' ){
				?>
				<a class="a_action to_validate" href="javascript:void(0);" onclick="javascript:validate_todo('<?= $this->getId(); ?>', 'desktop', <?= $this->fields['id_globalobject_ref']; ?>, '<?= base64_encode($this->getLightAttribute('keep_context'));?>', '<?= base64_encode($redirect_on); ?>');"><?= $_SESSION['cste']['_DIMS_VALID']; ?></a>
				<?php
			}
			else{
			?>
				<a class="a_action to_validate" href="javascript:void(0);" onclick="javascript:validate_todo('<?= $this->getId(); ?>', 'list', <?= $this->fields['id_globalobject_ref']; ?>, '<?= base64_encode($this->getLightAttribute('keep_context'));?>', '<?= base64_encode($redirect_on); ?>');"><?= $_SESSION['cste']['_DIMS_VALID']; ?></a>
			<?php
			}
		}
		?>
	</div>
	<?php

	if( ( ! isset($from_origin) || $from_origin != 'desktop') && !empty($this->children)){
		foreach($this->children as $child){
			$child->setLightAttribute('keep_context', $this->getLightAttribute('keep_context'));//on est obligé de redescendre l'infos aux fils
			$child->display(DIMS_APP_PATH.'/include/views/todos/todo.tpl.php');
		}
	}
	?>
</div>
