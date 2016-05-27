<?php
/***************************************************************************

PHP vCard class v2.01
(c) Kai Blankenhorn
www.bitfolge.de/en
kaib@bitfolge.de

****************************
New function setOrganisation
By Christophe BENOIT http://www.christophebenoit.com
*****************************

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

***************************************************************************/

/** Some functions **/
function encode($string) {
	return escape(quoted_printable_encode($string));
}

function escape($string) {
	return str_replace(";","\;",$string);
}


/****** /!\ IF php >= 5.3 : this function is useless /!\ ********/
if(!function_exists('quoted_printable_encode')) {
	//taken from PHP documentation comments
	function quoted_printable_encode($input, $line_max = 76) {
		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines = preg_split("/(?:\r\n|\r|\n)/", $input);
		$eol = "\r\n";
		$linebreak = "=0D=0A";
		$escape = "=";
		$output = "";

		for ($j=0;$j<count($lines);$j++) {
			$line = $lines[$j];
			$linlen = strlen($line);
			$newline = "";
			for($i = 0; $i < $linlen; $i++) {
				$c = substr($line, $i, 1);
				$dec = ord($c);
				if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
					$c = "=20";
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
					$h2 = floor($dec/16); $h1 = floor($dec%16);
					$c = $escape.$hex["$h2"].$hex["$h1"];
				}
				if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
					$output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay
					$newline = "    ";
				}
				$newline .= $c;
			} // end of for
			$output .= $newline;
			if ($j<count($lines)-1) $output .= $linebreak;
		}
		return trim($output);
	}
}

class vCard {
	private $properties;
	private $filename;

	public function setName($family="", $first="", $additional="", $prefix="", $suffix="") {
		$this->properties["N"] = "$family;$first;$additional;$prefix;$suffix";

		$this->filename = "$first%20$family.vcf";

		if ($this->properties["FN"]=="")
			$this->setFormattedName(trim("$prefix $first $additional $family $suffix"));
	}

	public function setFormattedName($name) {
		$this->properties["FN"] = quoted_printable_encode($name);
	}

	public function setBirthday($date) { // $date format is YYYY-MM-DD
		$this->properties["BDAY"] = $date;
	}

	//Add by Christophe BENOIT http://www.christophebenoit.com
	public function setOrganisation($organisation) {
		$this->properties["ORG"] = quoted_printable_encode($organisation);
	}

	//Add by LIEB Simon @ Netlor
	public function setTitle($title) {
		$this->properties["TITLE"] = quoted_printable_encode($title);
	}

	public function setPhoneNumber($number, $type="") {
	// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		$key = "TEL";

		if ($type!="") $key .= ";".$type;

		$key.= ";ENCODING=QUOTED-PRINTABLE";

		$this->properties[$key] = quoted_printable_encode($number);
	}

	// UNTESTED !!!
	public function setPhoto($type, $photo) { // $type = "GIF" | "JPEG"
		$this->properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
	}


	public function setAddress($name="", $extended="", $street="", $city="", $region="", $zip="", $country="", $type="HOME;POSTAL") {
	// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key = "ADR";

		if ($type!="") $key.= ";$type";

		$key.= ";ENCODING=QUOTED-PRINTABLE";

		$this->properties[$key] = encode($name).";".encode($extended).";".encode($street).";".encode($city).";".encode($region).";".encode($zip).";".encode($country);

		if ($this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] == "") {
			$this->setLabel($name, $extended, $street, $city, $region, $zip, $country, $type);
		}
	}

	public function setLabel($name="", $extended="", $street="", $city="", $region="", $zip="", $country="", $type="HOME;POSTAL") {
	// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$label = "";

		if ($postoffice!="") 	$label.= "$postoffice\r\n";
		if ($extended!="") 		$label.= "$extended\r\n";
		if ($street!="") 		$label.= "$street\r\n";
		if ($zip!="") 			$label.= "$zip ";
		if ($city!="") 			$label.= "$city\r\n";
		if ($region!="") 		$label.= "$region\r\n";
		if ($country!="") 		$label.= "$country\r\n";

		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($label);
	}

	public function setEmail($address, $pref = false) {
		$key = "EMAIL;INTERNET";

		if($pref) $key .= ";PREF";

		$this->properties[$key] = $address;
	}

	public function setNote($note) {
		$this->properties["NOTE;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($note);
	}

	public function setURL($url, $type="") {
	// $type may be WORK | HOME
		$key = "URL";

		if ($type!="") $key.= ";$type";

		$this->properties[$key] = $url;
	}

	public function getVCard() {
		$text = "BEGIN:VCARD\r\n";
		$text.= "VERSION:2.1\r\n";

		foreach($this->properties as $key => $value) {
			$text.= "$key:$value\r\n";
		}

		$text.= "REV:".date("Y-m-d")."T".date("H:i:s")."Z\r\n";
		$text.= "MAILER:PHP I-Net vCard \r\n";
		$text.= "END:VCARD\r\n";

		return $text;
	}

	public function getFileName() {
		return $this->filename;
	}
}

?>
