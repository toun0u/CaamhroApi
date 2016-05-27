<?php
$view = view::getInstance();
$object = $view->get('object');
?>
<h1>Réservation pour <strong class="resa-object"><?= $object->get('label'); ?></strong></h1>
<div class="resa-header line grid1-3">
	<div class="mod photo">
		<?php
		$default_picto = '/assets/images/frontoffice/zooparis/design/logo_zoo.png';
		$src = $default_picto;

		switch($view->get('type')){
			case 'fam':
				$imgs = $object->getThumbnails(1);
				if(!empty($imgs)){
					$first = $imgs[0];
					$src = $first->getDocFile()->getwebpath();
				}
				break;
			case 'art':
				$img = $object->getOriginal();
				if( ! empty($img) ) $src = $img->getwebpath();
				break;
		}
		?>
		<img src="<?= $src; ?>" title="<?= $object->get('label'); ?>"/>
	</div>
	<div class="mod description">
		<div class="description">
			<?= $object->get('description'); ?>
		</div>
		<?php
		$kit = $view->get('kit');
		if(!empty($kit)){
			?>
			<div>
				<strong class="bigger">Ce package comprend :</strong>
				<ul>
					<?php
					foreach($kit as $id => $infos){
						?>
						<li><strong><?= $infos['qty']; ?></strong> <?= $infos['label']; ?></li>
						<?php
					}
					?>
				</ul>
			</div>
			<?php
		}
		?>
		<div class="actions back">
			<input type="button" value="Revenir à la billetterie" onclick="javascript:document.location.href='<?= get_path('billets', 'index');?>';">
		</div>
	</div>
</div>
<?php
$articles = $view->get('articles');
if( ! empty($articles) ){
	$form = new Dims\form(array(
		'name'			=> 'reservation',
		'action'		=> get_path('billets', 'validpanier'),
		'submit_value'	=> 'Réserver',
		'back_name'		=> 'Annuler',
		'back_url'		=> get_path('billets', 'index'),
		'validation'	=> false
		));
	?>
	<div class="ligne resa-subcontent">
		<?= $form->get_header(); ?>
		<table>
			<tr>
				<th class="w100p"></th>
				<th>Article</th>
				<th>Tarif TTC</th>
				<th class="w100p">Qté</th>
			</tr>
			<?php
			foreach($articles as $art){
				?>
				<tr>
					<td class="txtcenter">
						<?php
						$src = $default_picto;
						$img = $art->getVignette(100);
						if( ! empty($img) )	$src = $img;
						?>
						<img src="<?= $src; ?>" title="<?= $art->get('label'); ?>" />
					</td>
					<td>
						<strong><?= $art->get('label'); ?></strong><br/>
						<div class="small"><?= $art->get('description'); ?></div>
					</td>
					<td>
						<?php
						$degressive = $art->getDegressiveTable(1);
						if( ! empty($degressive) ){
							?>
							<ul>
								<li>Prix unitaire : <?= number_format($art->calculate_PUTTC(), 2); ?> €</li>
								<?php
								foreach($degressive as $qty => $puht){
									?>
									<li>A partir de <?= number_format($qty); ?> : <?= number_format($puht * (1 + ($art->getTauxTVA() / 100)), 2); ?> €</li>
									<?php
								}
								?>
							</ul>
							<?php
						}
						else{
							echo number_format($art->calculate_PUTTC(), 2).' €';
						}
						?>
					</td>
					<td class="txtcenter">
						<select name="to_panier[<?= $art->get('id'); ?>]">
							<?php
							$in_session = $_SESSION['catalogue']['panier']['articles'][$art->fields['reference']]['qte'];
							for($i=0; $i<=20; $i++){
								?>
								<option value="<?= $i; ?>" <?= (isset($in_session) &&  $in_session == $i) ? 'selected="selected"' : ''; ?>><?= $i; ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<div class="actions back">
			<div class="txtright">
				<?php
				echo $form->submit_field(array('value'=>'Réserver'));
				?>
			</div>
		</div>
		<?php $form->close_form(); ?>
	</div>
	<?php
}
?>

