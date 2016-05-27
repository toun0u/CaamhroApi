<?php

//require_once DIMS_APP_PATH.'/include/class_form_tokenizer.php';

namespace InVal {

/**
 * Permet la validation et le pré-filtrage d'un ensemble d'entrées utilisateur.
 */
class ValidatorSet {
	private $tab;
	private $validators;
	private $prefix;

	private $rules, $errors;

	private $token;

	/**
	 * Construit un ValidatorSet.
	 * @param tab le tableau contenant les données à valider, ou 'post' ou 'get'
	 *            pour utiliser les valeurs de $_POST ou de $_GET
	 */
	public function __construct($tab = 'post') {
		$this->tab = $tab;
		$this->validators = array();
		$this->rules = array();
		$this->errors = array();
		$this->prefix = '';
		$this->token = new \FormToken\TokenField();
	}


	/**
	 * Spécifie un préfixe se trouvant devant les noms de toutes les entrées.
	 * Ce préfixe n'aura pas à être écrit pour les prochains ajouts.
	 */
	public function setPrefix($pref) {
		$this->prefix = $pref;
	}


	/**
	 * Ajoute une entrée à valider.
	 * Cette fonction accepte en argument soit un objet de type
	 * \InVal\Validator, soit une chaîne indiquant le nom de l'entrée. Si une
	 * chaîne est fournie, un second paramètre permet d'indiquer le type de
	 * données.
	 * S'il y a plus de deux paramètres, les paramètres suivants sont passés
	 * directement au constructeur du validateur.
	 *
	 * Les types valides sont "string", "integer", "real" et "boolean". Si
	 * aucun n'est précisé, "string" est choisi.
	 */
	public function add() {
		if(func_num_args() < 1 || func_num_args() > 2)
			throw new \Error_class(array('message' => 'Incorrect number of parameters'));

		$args = func_get_args();
		$arg0 = array_shift($args);

		// Si l'utilisateur nous a passé directement un validateur
		if($arg0 instanceof Validator) {
			if(!is_null($arg0->getName()))
				$this->validators[$arg0->getName()] = $arg0;
			else
				$this->validators[] = $arg0;
			return $arg0;
		}

		// Si l'utilisateur nous a passé un nom ou un couple (nom, type)
		if($this->tab == 'post' || $this->tab == 'get') {
			$in = $this->prefix.$arg0;
			$tab = $this->tab;
		}
		else {
			$in = $this->tab[$this->prefix.$arg0];
			$tab = null;
		}

		// On crée le bon validateur
		$valid;
		if(func_num_args() == 2) {
			$type = array_shift($args);
			switch($type) {
			case 'string':  $className = 'Validator';        break;
			case 'integer': $className = 'IntegerValidator'; break;
			case 'real':    $className = 'RealValidator';    break;
			case 'boolean': $className = 'BooleanValidator'; break;
			case 'date':    $className = 'DateValidator';    break;
			case 'file':    $className = 'FileValidator';    break;
			default:
				throw new \Error_class(array('message' => 'Incorrect parameter'));
			}

			// On passe au constructeur le nom de l'entrée, le tableau, puis
			// les paramètres suivants de add
			array_unshift($args, $in, $tab);

			$class = new \ReflectionClass('\\InVal\\'.$className);
			$valid = $class->newInstanceArgs($args);
		}
		else {
			$valid = new Validator($in, $tab);
		}

		if(!is_null($valid->getName())) {
			$this->validators[$valid->getName()] = $valid;
			$this->token->field($valid->getName());
		}
		else
			$this->validators[] = $valid;
		return $valid;
	}


	/**
	 * Ajoute une entrée constante, c'est-à-dire fournie dans un input hidden
	 * et ne devant pas être modifiée par l'utilisateur.
	 *
	 * Prend les mêmes paramètres que add, mais le dernier doit contenir la
	 * valeur constante du champ.
	 */
	public function addConstant() {
		$params = func_get_args();
		$value = array_pop($params);
		$valid = call_user_func_array(array($this, 'add'), $params);
		$this->token->field($valid->getName(), $value);
	}


	/**
	 * Ajoute une règle à appliquer sur l'ensemble d'entrées.
	 */
	public function rule(Rule\SetRule $rule) {
		$this->rules[] = $rule;
	}


	/**
	 * Valide toutes les entrées.
	 * @param tab le tableau des entrées, ou null pour utiliser la valeur du
	 *            constructeur
	 * @return un booléen indiquant si toutes les entrées passent les tests
	 */
	public function validate($tab = null) {
		$ok = true;
		foreach($this->validators as $name => $val) {
			if(is_null($tab)) {
				if(!$val->validate())
					$ok = false;
			}
			else {
				if(!isset($tab[$name]) || is_null($tab[$name]))
					$tab[$name] = '';
				if(!$val->validate($tab[$name]))
					$ok = false;
			}
		}

		if(!$ok)
			return false;

		$this->errors = array();
		foreach($this->rules as $rule) {
			$res = $rule->validate($this);

			if(is_array($res)) {
				foreach($res as $in => $err) {
					$this->errors[$in][] = $err;
				}
			}
		}

		return empty($this->errors);
	}

	/**
	 * Retourne les erreurs de validation.
	 * @return un tableau associatif, dont les clés sont les noms des entrées et
	 *         les valeurs sont des tableaux contenant toutes les erreurs
	 *         concernant cette entrée
	 */
	public function getErrors() {
		$errors = array();

		foreach($this->validators as $val) {
			$errors[$val->getName()] = $val->getErrors();
		}

		return array_merge_recursive($errors, $this->errors);
	}

	public function getInput($name = null) {
		if($name != null) {
			return $this->validators[$name]->getInput();
		}
		else {
			$inputs = array();

			foreach($this->validators as $val) {
				$inputs[$val->getName()] = $val->getInput();
			}

			return $inputs;
		}
	}

	public function getRawInput() {
		$inputs = array();

		foreach($this->validators as $val) {
			$inputs[$val->getName()] = $val->getRawInput();
		}

		return $inputs;
	}

	public function generateToken() {
		return $this->token->generate();
	}
}




/**
 * Permet la validation et le pré-filtrage d'une entrée utilisateur.
 */
class Validator {
	// Nom et conteneur de l'entrée, ou l'entrée elle-même et null
	protected $in, $tab;

	// Règles et filtres ajoutés au validateur
	private $elems;

	// $validated contient le résultat de la validation (booléen) ou null si
	// elle n'a pas eu lieu ; $errors contient toutes les erreurs
	private $validated, $errors;

	// Contient la donnée filtrée ou null si elle ne l'a pas été
	private $filteredInput;


	/**
	 * Construit un validateur. Celui-ci peut être lié dès le départ à une
	 * entrée grâce aux paramètres in et tab, lié plus tard grâce à
	 * setRawInput, ou jamais lié. Dans ce dernier cas, il faudra passer
	 * l'entrée à validate.
	 *
	 * @param in  le nom ou la valeur de l'entrée
	 * @param tab 'post' ou 'get' pour utiliser les valeurs de $_POST ou de
	 *             $_GET, ou null si l'entrée est passée telle quelle et non pas
	 *             par son nom
	 */
	public function __construct($in = '', $tab = 'post') {
		$this->setRawInput($in, $tab);

		$this->elems = array();
	}

	public function getName() {
		return is_null($this->tab) ? null : $this->in;
	}

	/**
	 * Change l'entrée testée par ce validateur.
	 * Cela permet d'utiliser le même validateur pour vérifier et filtrer
	 * plusieurs entrées.
	 */
	public function setRawInput($in, $tab = 'post') {
		$this->in = $in;

		if(is_null($tab))
			$this->tab = null;
		else
			$this->tab = is_array($tab) ? $tab : ($tab == 'post' ? $_POST : $_GET);

		// On réinitialise les résultats
		$this->validated = null;
	}


	/**
	 * Retourne l'entrée utilisateur non filtrée
	 */
	public function getRawInput() {
		if(is_null($this->tab))
			return $this->in;
		else
			return isset($this->tab[$this->in]) ? $this->tab[$this->in] : null;
	}


	/**
	 * Ajoute une règle. Cette règle est appliquée dans l'ordre des ajouts de
	 * règles et de filtres, c'est-à-dire que si un filtre a été ajouté avant
	 * elle alors la règle s'appliquera sur le résultat filtré.
	 */
	public function rule($rule) {
		$this->elems[] = $rule;
		$this->validated = null;
		return $this;
	}

	/**
	 * Ajoute un filtre.
	 * Si une chaîne ou une fonction est fournie, un filtre Func est
	 * automatiquement créé.
	 */
	public function filter($filter) {
		if(!($filter instanceof Filter\Filter))
			$filter = new Filter\Func($filter);

		$this->elems[] = $filter;
		$this->validated = null;
		return $this;
	}


	/**
	 * Valide l'entrée.
	 * L'entrée est validée seulement après l'application des filtres.
	 *
	 * @param input l'entrée à valider, ou null pour valider l'entrée liée à ce
	 *              validateur (via le constructeur ou setRawInput).
	 * @return un booléen indiquant si l'entrée passe tous les tests
	 */
	public function validate($input = null) {
		if(is_null($input) && !is_null($this->filteredInput) && !is_null($this->validated))
			return $this->validated;

		$this->validated = true;
		$this->filteredInput = null;
		$this->errors = array();

		$input = is_null($input) ? $this->getRawInput() : $input;

		foreach($this->elems as $elem) {
			// Si c'est une règle, on la vérifie
			if($elem instanceof Rule\Rule) {
				$res = $elem->validate($input);
				if($res !== true) {
					$this->validated = false;
					$this->errors[] = $res;
				}
			}

			// Si c'est un filtre et que la donnée a validé toutes les règles
			// jusqu'à présent, on l'applique. Sinon on arrête.
			else if($elem instanceof Filter\Filter) {
				if(!$this->validated)
					return $this->validated;
				$input = $elem->filter($input);
			}
		}

		if($this->validated) {
			$this->filteredInput = $input;
		}

		return $this->validated;
	}


	/**
	 * Retourne les erreurs levées par la validation.
	 * @return un tableau d'erreurs, ou un tableau vide si l'entrée est validée
	 */
	public function getErrors() {
		if(is_null($this->validated))
			$this->validate();

		return $this->errors;
	}


	/**
	 * Retourne l'entrée filtrée.
	 */
	public function getInput() {
		if(!is_null($this->validated))
			return $this->filteredInput;

		$this->validate();

		return $this->filteredInput;
	}
}


/**
 * Permet la validation et le pré-filtrage d'un fichier téléversé par
 * l'utilisateur.
 */
class FileValidator extends Validator {
	public function __construct($in = '', $tab = null) {
		parent::__construct($in, $_FILES);
	}

	public function getRawInput() {
		$raw = parent::getRawInput();
		if(empty($raw['name']))
			return null;
		else
		  return $raw;
	}
}


// Helpers : validateurs possédant les règles et filtres correspondant à un type donné

class IntegerValidator extends Validator {
	public function __construct($in = '', $tab = 'post') {
		parent::__construct($in, $tab);
		$this->rule(new Rule\Integer)->filter(new Filter\Integer);
	}
}

class RealValidator extends Validator {
	public function __construct($in = '', $tab = 'post') {
		parent::__construct($in, $tab);
		$this->rule(new Rule\Real)->filter(new Filter\Real);
	}
}

class BooleanValidator extends Validator {
	public function __construct($in = '', $tab = 'post') {
		parent::__construct($in, $tab);
		$this->filter(new Filter\Boolean);
	}
}

class DateValidator extends Validator {
	public function __construct($in = '', $tab = 'post', $format = 'd/m/y') {
		parent::__construct($in, $tab);
		$format = new DateFormat($format);
		$this->rule(new Rule\Date($format))->filter(new Filter\Timestamp($format));
	}
}


// Classe helper pour les règles et filtres de date
class DateFormat {
	private $reg, $order;
	public function __construct($format) {
		// Détermination de l'ordre des composantes
		$di = strpos($format, 'd');
		$mi = strpos($format, 'm');
		$yi = strpos($format, 'y');

		if($di == -1 || $mi == -1 || $yi == -1)
			throw new Error_class(array('message' => 'Incorrect format'));

		if($di < $mi) {
			if($mi < $yi)      // di < mi < yi
				$this->order = array(1, 2, 3);
			else if($di < $yi) // di < yi < mi
				$this->order = array(1, 3, 2);
			else               // yi < di < mi
				$this->order = array(2, 3, 1);
		}
		else {
			if($di < $yi)      // mi < di < yi
				$this->order = array(2, 1, 3);
			else if($mi < $yi) // mi < yi < di
				$this->order = array(3, 1, 2);
			else               // yi < mi < di
				$this->order = array(3, 2, 1);
		}

		// Création de la Regexp
		$format = preg_quote($format);
		$format = str_replace(array('d', 'm', 'y', '#'), array('(\d{2})', '(\d{2})', '(\d{4}|\d{2})', '\#'), $format);
		$this->reg = '#^'.$format.'$#';
	}

	public function parse($date) {
		$res = preg_match($this->reg, $date, $match);
		if($res == 0) {
			return false;
		}
		else {
			return array(
				'day'   => $match[$this->order[0]],
				'month' => $match[$this->order[1]],
				'year'  => $match[$this->order[2]]
			);
		}
	}
}


}


/* ================================= Règles ================================= */

namespace InVal\Rule {

define('_DIMS_INVAL_NOTINTEGER', 1);
define('_DIMS_INVAL_NOTREAL', 2);
define('_DIMS_INVAL_EMPTY', 3);
define('_DIMS_INVAL_DONTMATCH', 4);
define('_DIMS_INVAL_NOTINLIST', 5);
define('_DIMS_INVAL_BADEXT', 6);
define('_DIMS_INVAL_NOTIMAGE', 7);
define('_DIMS_INVAL_TOOBIG', 8);
define('_DIMS_INVAL_COMPFAILED', 9);
define('_DIMS_INVAL_NOTINRANGE', 10);
define('_DIMS_INVAL_TOOLONG', 11);
define('_DIMS_INVAL_NOTDATE', 12);
define('_DIMS_INVAL_INVALIDDATE', 13);

interface Rule {
	/**
	 * Vérifie si l'entrée respecte la règle.
	 * @return true si l'entrée est validée, une erreur sinon.
	 */
	public function validate($val);
}

interface SetRule {
	public function validate(\InVal\ValidatorSet $set);
}


/**
 * Vérifie si l'entrée est un entier.
 * Cette règle doit être appliquée sur la donnée brute.
 */
class Integer implements Rule {
	public function validate($val) {
		return (empty($val) || ctype_digit($val)) ? true : _DIMS_INVAL_NOTINTEGER ;
	}
}


/**
 * Vérifie si l'entrée est un réel.
 * Cette règle doit être appliquée sur la donnée brute.
 */
class Real implements Rule {
	public function validate($val) {
		return (empty($val) || preg_match('/^\d+(?:[.,]\d+)?$/', $val) === 1) ? true : _DIMS_INVAL_NOTREAL ;
	}
}


/**
 * Vérifie si l'entrée est une date.
 * Cette règle doit être appliquée sur la donnée brute.
 */
class Date implements Rule {
	private $reg, $order;
	/**
	 * Construit une règle Date.
	 * Le format passé en paramètre doit contenir les lettres 'd', 'm' et 'y',
	 * indiquant la position du jour, du mois et de l'année.
	 *
	 * Les autres caractères sont considérés comme dans une expression
	 * régulière.
	 *
	 * Exemples de format : 'd/m/y', 'y-m-d'...
	 * Note : http://xkcd.com/1179/
	 */
	public function __construct($format) {
		if($format instanceof \InVal\DateFormat)
			$this->format = $format;
		else
			$this->format = new \InVal\DateFormat($format);
	}
	public function validate($val) {
		$res = $this->format->parse($val);
		if($res === false)
			return _DIMS_INVAL_NOTDATE;

		return checkdate($res['month'], $res['day'], $res['year']) ? true : _DIMS_INVAL_INVALIDDATE;
	}
}


/**
 * Vérifie si l'entrée est non vide.
 */
class Required implements Rule {
	public function validate($val) {
		return ($val === 0 || !empty($val)) ? true : _DIMS_INVAL_EMPTY ;
	}
}


/**
 * Vérifie si l'entrée ne dépasse pas une longueur maximale (incluse).
 */
class MaxLength implements Rule {
	private $len;
	public function __construct($len) {
		$this->len = $len;
	}
	public function validate($val) {
		return strlen($val) <= $this->len ? true : _DIMS_INVAL_TOOLONG;
	}
}


/**
 * Vérifie si l'entrée correspondant à une expression régulière.
 */
class Regex implements Rule {
	private $regex;
	public function __construct($regex) {
		$this->regex = $regex;
	}
	public function validate($val) {
		return preg_match($this->regex, $val) == 1 ? true : _DIMS_INVAL_DONTMATCH;
	}
}


/**
 * Vérifie si l'entrée est parmi une liste de valeurs définie.
 */
class InList implements Rule {
	private $vals;
	public function __construct() {
		$vals = func_get_args();

		if(is_array($vals[0]))
			$this->vals = $vals[0];
		else
			$this->vals = $vals;
	}
	public function validate($val) {
		return in_array($val, $this->vals) ? true : _DIMS_INVAL_NOTINLIST;
	}
}


/**
 * Vérifie si l'entrée est dans un intervalle.
 */
class Range implements Rule {
	private $inf, $sup, $ouvInf, $ouvSup;
	/**
	 * L'intervalle est fourni sous une de ces formes :
	 * - [0;5.2] : de 0 à 5.2 inclus
	 * - ]0;7]   : de 0 à 7, 0 exclu, 7 inclus
	 * - [5;8[   : de 5 à 8, 5 inclus, 8 exclu
	 * - ]0;1[   : de 0 à 1 exclus
	 * - [-1;inf[ ou [0 : au minimum -1 inclus
	 * - ]-1;inf[ ou ]0 : au minimum -1 exclu
	 * - ]inf;5] ou 5] : au maximum 5 inclus
	 * - ]inf;5[ ou 5[ : au maximum 5 exclu
	 *
	 * Le séparateur décimale doit être un point.
	 */
	public function __construct($range) {
		$etat = 0; // 0(deb), 1(avantNum1), 2(aprèsNum1), 3(avantNum2), 4(aprèsNum3), 5(fin)
		$i = 0;
		$len = strlen($range);

		$this->inf = null;
		$this->sup = null;
		$this->ouvInf = true;
		$this->ouvSup = true;

		$parseNum = function() use($range, $len, &$i) {
			if(substr($range, $i, 3) === 'inf') {
				$i += 3;
				return null;
			}

			if(!ctype_digit($range{$i}) && $range{$i} != '-')
				throw new Error_class(array('message' => 'Incorrect range'));

			$num = $range{$i};
			$dot = false;

			++$i;
			while($i < $len && (ctype_digit($range{$i}) || $range{$i} == '.' && !$dot)) {
				if($range{$i} == '.')
					$dot = true;
				$num .= $range{$i};
				++$i;
			}

			return (float) $num;
		};

		while($etat != 5) {
			switch($etat) {
			case 0:
				switch($range{$i}) {
				case '[': $this->ouvInf = false; $etat = 1; ++$i; break;
				case ']': $this->ouvInf = true;  $etat = 1; ++$i; break;
				default:  $this->sup = $parseNum(); $etat = 4;
				}
				break;
			case 1:
				$this->inf = $parseNum();
				$etat++;
				break;
			case 2:
				if($i >= $len) {
					$etat = 5;
				}
				else if($range{$i} != ';') {
					throw new Error_class(array('message' => 'Incorrect range'));
				}
				else {
					$etat = 3;
					++$i;
				}
				break;
			case 3:
				$this->sup = $parseNum();
				$etat++;
				break;
			case 4:
				switch($range{$i}) {
				case '[': $this->ouvSup = true;  $etat = 5; break;
				case ']': $this->ouvSup = false; $etat = 5; break;
				default: throw new Error_class(array('message' => 'Incorrect range'));
				}
				break;
			}
		}
	}

	public function validate($val) {
		if(!is_null($this->inf)) {
			if($this->ouvInf)
				$ok = $val > $this->inf;
			else
				$ok = $val >= $this->inf;
			if(!$ok)
				return _DIMS_INVAL_NOTINRANGE;
		}

		if(!is_null($this->sup)) {
			if($this->ouvSup)
				$ok = $val < $this->sup;
			else
				$ok = $val <= $this->sup;
			if(!$ok)
				return _DIMS_INVAL_NOTINRANGE;
		}

		return true;
	}
}


/**
 * Vérifie qu'une entrée est supérieure à une autre.
 */
class Greater implements SetRule {
	private $big, $small, $strict;
	public function __construct($big, $small, $strict = false) {
		$this->big = $big;
		$this->small = $small;
		$this->strict = $strict;
	}
	public function validate(\InVal\ValidatorSet $set) {
		if($this->strict)
			$ok = $set->getInput($this->big) > $set->getInput($this->small);
		else
			$ok = $set->getInput($this->big) >= $set->getInput($this->small);

		return $ok ? true : array($this->big   => _DIMS_INVAL_COMPFAILED,
		                          $this->small => _DIMS_INVAL_COMPFAILED);
	}
}


/**
 * Vérifie que le fichier a une extension autorisée.
 */
class Ext implements Rule {
	private $exts;
	public function __construct() {
		$exts = func_get_args();

		if(is_array($exts[0]))
			$this->exts = $exts[0];
		else
			$this->exts = $exts;
	}
	public function validate($val) {
		if(!is_array($val))
			return true;

		foreach($this->exts as $ext) {
			$ext = '.'.$ext;
			$pos = strlen($val['name']) - strlen($ext);
			if(strrpos($val['name'], $ext, 0) === $pos)
				return true;
		}

		return _DIMS_INVAL_BADEXT;
	}
}

/**
 * Vérifie que le fichier est une image.
 */
class Image implements Rule {
	private $gif;
	public function __construct($gif = false) {
		$this->gif = (bool) $gif;
	}
	public function validate($val) {
		if(!is_array($val))
			return true;

		$exts = array('png', 'jpg', 'jpeg', 'ico');
		if($this->gif)
			$exts[] = 'gif';

		$extRule = new Ext($exts);
		$res = $extRule->validate($val);

		if($res !== true)
			return $res;

		preg_match('#\.([A-Za-z0-9]+)$#', $val['name'], $match);
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		$mime = $finfo->file($val['tmp_name']);

		switch($match[1]) {
		case 'png':
			if($mime == 'image/png')  return true; break;
		case 'jpg': case 'jpeg':
			if($mime == 'image/jpeg') return true; break;
		case 'gif':
			if($mime == 'image/gif')  return true; break;
		case 'ico':
		    if($mime == 'image/x-icon' || $mime == 'image/vnd.microsoft.icon') return true; break;
		}

		return _DIMS_INVAL_NOTIMAGE;
	}
}

/**
 * Vérifie que le fichier pèse moins qu'un certain poids.
 */
class MaxSize implements Rule {
	private $max;
	public function __construct($bytes) {
		$this->max = $bytes;
	}
	public function validate($val) {
		if(!is_array($val))
			return true;
		return $val['size'] <= $this->max ? true : _DIMS_INVAL_TOOBIG;
	}
}

}


/* ================================ Filtres ================================= */

namespace InVal\Filter {

interface Filter {
	/**
	 * Filtre l'entrée.
	 */
	public function filter($val);
}


class Integer implements Filter {
	public function filter($val) {
		return (int)$val;
	}
}

class Real implements Filter {
	public function filter($val) {
		return (float) str_replace(',', '.', $val);
	}
}

class Boolean implements Filter {
	public function filter($val) {
		return ! empty($val);
	}
}

/**
 * Convertit une date d'un format donné en timestamp Unix.
 */
class Timestamp implements Filter {
	private $format;
	public function __construct($format) {
		if($format instanceof \InVal\DateFormat)
			$this->format = $format;
		else
			$this->format = new \InVal\DateFormat($format);
	}
	public function filter($val) {
		$parts = $this->format->parse($val);
		return mktime(0, 0, 0, $parts['month'], $parts['day'], $parts['year']);
	}
}

/**
 * Convertit un timestamp Unix en date d'un format donné.
 */
class Date implements Filter {
	private $format;
	/**
	 * @param format un format compatible avec la fonction PHP date.
	 */
	public function __construct($format) {
		$this->format = $format;
	}
	public function filter($val) {
		return date($this->format, $val);
	}
}

/**
 * Convertit un timestamp Unix en date sous le format YmdHis.
 */
class DateInt14 implements Filter {
	public function filter($val) {
		return date('YmdHis', $val);
	}
}

/**
 * Filtre permettant d'affecter une valeur par défaut à l'entrée au cas où
 * celle-ci serait vide.
 */
class DefaultVal implements Filter {
	private $def;
	public function __construct($default) {
		$this->def = $default;
	}
	public function filter($val) {
		return empty($val) ? $this->def : $val;
	}
}

/**
 * Filtre custom permettant de spécifier une fonction utilisateur.
 * La fonction peut être passée par son nom (ce qui permet aussi d'utiliser les
 * fonctions natives de PHP) ou de manière directe.
 */
class Func implements Filter {
	private $func;
	public function __construct($func) {
		$this->func = $func;
	}
	public function filter($val) {
		return call_user_func($this->func, $val);
	}
}


/**
 * Permet de déplacer un fichier téléversé au bon endroit.
 * Après application du filtre, le tableau aura son "tmp_name" mis à jour avec
 * le nouveau chemin et contiendra une entrée "moved" mise à true.
 */
class MoveFile implements Filter {
	private $path;
	/**
	 * Construit un filtre déplaçant le fichier vers path, qui peut être
	 * spécifié de trois manières :
	 * - s'il termine par un slash, le fichier est déplacé dans ce répertoire,
	 *   sous le même nom qu'a spécifié l'utilisateur (potentiellement
	 *   dangereux !) ;
	 * - s'il termine par un point, le fichier est déplacé et renommé, et
	 *   l'extension fournie par l'utilisateur est ajoutée à la fin du nom ;
	 * - sinon, le fichier est déplacé et renommé exactement comme précisé.
	 */
	public function __construct($path) {
		$this->path = $path;
	}
	public function filter($val) {
		$lastChar = $this->path{strlen($this->path)-1};
		if($lastChar == '/') {
			$path = $this->path . $val['name'];
		}
		else if($lastChar == '.') {
			preg_match('#\.([A-Za-z0-9]+)$#', $val['name'], $match);
			$path = $this->path . (empty($match) ? '' : $match[1]);
		}
		else {
			$path = $this->path;
		}

		if(move_uploaded_file($val['tmp_name'], $path) === true) {
			$val['tmp_name'] = $path;
			$val['moved'] = true;
		}

		return $val;
	}
}

}

?>
