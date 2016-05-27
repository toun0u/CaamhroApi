<?
/* ***** BEGIN LICENSE BLOCK *****
* Version: MPL 1.1
*
* The contents of this file are subject to the Mozilla Public License Version
* 1.1 (the "License"); you may not use this file except in compliance with
* the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS" basis,
* WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
* for the specific language governing rights and limitations under the
* License.
*
* The Original Code is : Debora, un g�n�rateur de codes barre.
*
* The Initial Developer of the Original Code is
* Olivier Meunier.
* Portions created by the Initial Developer are Copyright (C) 2003
* the Initial Developer. All Rights Reserved.
*
* Contributor(s):
* R�mi Ch�no (ajout des s�parateurs gauche, centre et droite)
*
* ***** END LICENSE BLOCK ***** */

class debora
{
    /**
    * D�claration des propri�t�s
    */
    var $arryGroup = array('A' => array(
                0 => "0001101", 1 => "0011001",
                2 => "0010011",    3 => "0111101",
                4 => "0100011",    5 => "0110001",
                6 => "0101111",    7 => "0111011",
                8 => "0110111",    9 => "0001011"
                ),
                'B' => array(
                0 => "0100111",    1 => "0110011",
                2 => "0011011",    3 => "0100001",
                4 => "0011101",    5 => "0111001",
                6 => "0000101",    7 => "0010001",
                8 => "0001001",    9 => "0010111"
                ),
                'C' => array(
                0 => "1110010",    1 => "1100110",
                2 => "1101100",    3 => "1000010",
                4 => "1011100",    5 => "1001110",
                6 => "1010000",    7 => "1000100",
                8 => "1001000",    9 => "1110100"
                )
                );

    var $arryFamily = array(
                    0 => array('A','A','A','A','A','A'),
                    1 => array('A','A','B','A','B','B'),
                    2 => array('A','A','B','B','A','B'),
                    3 => array('A','A','B','B','B','A'),
                    4 => array('A','B','A','A','B','B'),
                    5 => array('A','B','B','A','A','B'),
                    6 => array('A','B','B','B','A','A'),
                    7 => array('A','B','A','B','A','B'),
                    8 => array('A','B','A','B','B','A'),
                    9 => array('A','B','B','A','B','A')
                    );
	var $keyFamily = array(
                    0 => 1,
                    1 => 3,
                    2 => 1,
                    3 => 3,
                    4 => 1,
                    5 => 3,
                    6 => 1,
                    7 => 3,
                    8 => 1,
                    9 => 3,
                    10 => 1,
                    11 => 3
                    );

    /**
    * Constructeur
    *
    * Initialise la classe
    *
    * @EAN13            string        code EAN13
    *
    * return            void
    */
    function debora($EAN13)
    {
        settype($EAN13,'string');
		if (strlen($EAN13)==12)
		{
			//Transformation de la chaine EAN en tableau
		for($i=0;$i<12;$i++)
		{
		    $this->EAN13[$i] = substr($EAN13,$i,1);
		}

			$this->EAN13[12]= $this->makeKey();
			$EAN13.=$this->EAN13[12];
		}
		else
		{
			//Transformation de la chaine EAN en tableau
		for($i=0;$i<13;$i++)
		{
		    $this->EAN13[$i] = substr($EAN13,$i,1);
		}

		}


        $this->strCode = $this->makeCode();
    }


    /**
    * Cr�ation du code binaire
    *
    * Cr�e une chaine contenant des 0 ou des 1 pour indiquer les espace blancs ou noir
    *
    * return            string        Chaine r�sultante
    */
    function makeCode()
    {
        //On r�cup�re la classe de codage de la partie qauche
        $arryLeftClass = $this->arryFamily[$this->EAN13[0]];

        //Premier s�parateur (101)
        $strCode = '101';

        //Codage partie gauche
        for ($i=1; $i<7; $i++)
        {
            $strCode .= $this->arryGroup[$arryLeftClass[$i-1]][$this->EAN13[$i]];
        }

        //S�parateur central (01010)
        $strCode .= '01010';

        //Codage partie droite (tous de classe C)
        for ($i=7; $i<13; $i++)
        {
            $strCode .= $this->arryGroup['C'][$this->EAN13[$i]];
        }

        //Dernier s�parateur (101)
        $strCode .= '101';

        return $strCode;
    }


    /**
    * Cr�ation de l'image
    *
    * Cr�e une image GIF ou PNG du code g�n�r� par giveCode
    *
    * return            void
    */
    function makeImage($imageType="png",$ean="123456",$filename="123456")
    {
        //Initialisation de l'image
        $img = imagecreate(190, 1);

        $color[0] = ImageColorAllocate($img, 255,255,255);
        $color[1] = ImageColorAllocate($img, 0,0,0);

        $coords[0] = 0;
        $coords[1] = 0;
        $coords[2] = 2;
        $coords[3] = 40;

        for($i = 0; $i < strlen($this->strCode); $i++)
        {
            $posX = $coords[0];
            $posY = $coords[1];
            $intL = $coords[2];
            $intH = $coords[3];

            $fill_color = substr($this->strCode,$i,1);

            imagefilledrectangle($img, $posX, $posY, $posX + 1, 30, $color[$fill_color]);

            //Deplacement du pointeur
            $coords[0] = $coords[0] + $coords[2];
        }

        Header( "Content-type: image/".$imageType);

        $func_name = 'image'.$imageType;

        $path = './data/barcode/';
        if(!is_dir($path)) make_dir($path);
        $func_name($img,$path.$filename .'.'. $imageType);
    }

    /**
    * Cr�ation de la cl� de contr�le
    *
    *
    *
    * return            char
    */
    function makeKey()
    {
        $total = 0;
        for($i=0;$i<12;$i++)
            $total += $this->EAN13[$i] * $this->keyFamily[$i];

        if ($total==0)
            return -1;
        else
            return ((10-($total % 10))%10);
    }

}//Fin de la classe
?>
