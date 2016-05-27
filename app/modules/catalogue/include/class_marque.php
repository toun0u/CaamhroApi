<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
class cata_marque extends dims_data_object {

	const MY_GLOBALOBJECT_CODE = 234;
	const TABLE_NAME = 'dims_mod_cata_marque';

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
		$this->has_many('article', article::TABLE_NAME, 'id', 'marque');
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = 'Marque : '.$this->fields['libelle'];
	}

	public function save() {
		// Enregistrement de la photo si presente
		if (!empty($_FILES['image']) && !$_FILES['image']['error']) {
			$error = 0;

			if ($_FILES['image']['size'] > 0) {
				if ($_FILES['image']['size'] < _CATA_PHOTO_MAX_UPLOAD_SIZE) {
					$path = realpath('.')."/photos/marques";
					$filename = strtolower($_FILES['image']['name']);
					$fullfilename = $path.'/'.$filename;

					if (!file_exists($fullfilename)) {
						if (move_uploaded_file($_FILES['image']['tmp_name'], $fullfilename)) {
							// On met à jour l'image dans la table
							chmod($fullfilename, 0660);
							$this->fields['image'] = $filename;
						} else {
							$error = _CATA_PHOTO_COPY_ERROR;
						}
					} else {
						$error = _CATA_PHOTO_ALREADY_EXISTS;
					}
				} else {
					$error = _CATA_PHOTO_HUGE_DOC;
				}
			} else {
				$error = _CATA_PHOTO_EMPTY_DOC;
			}

			if ($error) {
				$_SESSION['catalogue']['erreur'] = $error;
			}
		}

		parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function drop_image() {
		if ($this->fields['image'] != '' && file_exists(realpath('.')."/photos/marques/{$this->fields['image']}")) {
			unlink(realpath('.')."/photos/marques/{$this->fields['image']}");
		}
		$this->fields['image'] = '';
		parent::save();
	}

	public function getLabel() {
		return $this->fields['libelle'];
	}

	public function setLabel($label) {
		$this->fields['libelle'] = $label;
	}

}
