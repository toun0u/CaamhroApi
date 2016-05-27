<?php

class dims_planning{

	private $view;//mode de visualisation : day / week / month
	private $model;//les données sur la vue courante

	private $today;
	private $weekadd;
	private $dayadd;
	private $monthadd;

	private $datedeb_timestp;
	private $datefin_timestp;
	private $datedeb;
	private $datefin;

	private $prev;
	private $next;

	private $default_bgcolor;
	private $default_today_bgcolor;

	public function __construct($view = 'month', $data=null) {
		$this->setVisuMode($view);
		$this->model = array();
		if(!is_null($data)) $this->setModel($data);

		$this->today = date('Y-m-d');
		$this->setMonthAdd(0);
		$this->setWeekAdd(0);
		$this->setDayAdd(0);

		$this->setDefaultBGColor("#FFFFFF");
		$this->setDefaultTodayBGColor('#CAFEC2');
    }

	public function setModel($data){
		$this->model = $data;
	}
	public function getModel(){
		return $this->model;
	}

	public function getVisuMode(){
		return $this->view;
	}

	public function setVisuMode($mode){
		$this->view = $mode;
	}

	public function setWeekAdd($wa, $onlyweek = false){
		$this->weekadd = $wa;
		if(!$onlyweek){
			$this->monthadd = 0;
			$this->dayadd = 0;
		}
	}
	public function getWeekAdd(){
		return $this->weekadd;
	}
	public function setDayAdd($da){
		$this->dayadd = $da;
		if ($da != 0) {
			$this->weekadd += ($this->dayadd - ($this->dayadd%7)) / 7;
			$this->dayadd = $this->dayadd % 7;
		}
		return $this->weekadd;//parce qu'il faut pouvoir la repasser en session
	}

	public function getDayAdd(){
		return $this->dayadd;
	}
	public function setMonthAdd($ma){
		$this->monthadd = $ma;
	}
	public function getMonthAdd(){
		return $this->monthadd;
	}

	/*
	 * Fonction qui retourne les événements programmés sur une date donnée, pour une plage horaire données
	 */
	public function getListEvents($day, $hdeb='00:00:00', $hfin='23:59:59'){
		$events = array();
		if(!is_null($day)){
			if (isset($this->model[$day]['events'])) {
				$sub_array = $this->model[$day]['events'];
				foreach($sub_array as $k=> $evt){
					if($evt['heuredeb']>=$hdeb && $evt['heuredeb']<$hfin){
						$events[] = $evt;
					}
				}
			}
		}
		return $events;
	}

	/*
	 * Fonction définissant les bornes temporelles du planning courant en fonction du mode de prévisu
	 */
	public function definePlanningBornes(){
		switch($this->getVisuMode()) {
			case 'week':
				// pour le jour, on doit repasser en paramètre la date du mois recherché
				// car le 9 du mois de janvier, par exemple, tombera pas le même jour
				// (lundi, mardi ...) que le 9 du mois de février
				$this->datedeb_timestp = mktime(
											0,
											0,
											0,
											date('n') + $this->monthadd,
											date('j') - date('N', mktime(0, 0, 0, date('n') + $this->monthadd, date('j'), date('Y'))) + ($this->weekadd * 7) + 1,
											date('Y'));
				$this->datefin_timestp = mktime(
											0,
											0,
											0,
											date('n') + $this->monthadd,
											date('j') - date('N', mktime(0, 0, 0, date('n') + $this->monthadd, date('j'), date('Y'))) + ($this->weekadd * 7) + 7,
											date('Y'));

				$this->prev = "&weekadd=".($this->weekadd-1);
				$this->next = "&weekadd=".($this->weekadd+1);
			break;

			case 'day':
				$this->datefin_timestp = $this->datedeb_timestp = mktime(0,0,0,date('n')+$this->monthadd,date('j')+($this->weekadd*7)+$this->dayadd,date('Y'));

				$this->prev = "&dayadd=".($this->dayadd-1);
				$this->next = "&dayadd=".($this->dayadd+1);
			break;

			default:
			case 'month':
				$this->datedeb_timestp = mktime(0,0,0,date('n')+$this->monthadd,1,date('Y'));
				$this->datefin_timestp = mktime(0,0,0,date('n')+$this->monthadd+1,0,date('Y'));

				$this->prev = "&monthadd=".($this->monthadd-1);
				$this->next = "&monthadd=".($this->monthadd+1);
			break;
		}

		$this->datedeb = date('Y-m-d',$this->datedeb_timestp);
		$this->datefin = date('Y-m-d',$this->datefin_timestp);

		$this->initDaysAttributes();
	}

	public function setDayAttributes($day, $crea_allowed= true, $crea_onclick="", $bgcolor=null){
		$this->setDayCreationAllowed($day,$crea_allowed);
		$this->setDayCreationOnClick($day,$crea_onclick);
		$this->setDayBGColor($day, ( is_null($bgcolor)?$this->default_bgcolor:$bgcolor )  );
	}

	private function initDaysAttributes(){
		for($i=$this->datedeb_timestp;$i<=$this->datefin_timestp;$i+=(24*60*60)){
			$this->setDayAttributes(date('Y-m-d',$i));
		}
	}

	public function setDefaultBGColor($color){
		$this->default_bgcolor = $color;
	}

	public function setDefaultTodayBGColor($color){
		$this->default_today_bgcolor =  $color;
	}

	public function getDefaultTodayBGColor(){
		return $this->default_today_bgcolor;
	}
	public function setDayBGColor($day, $bgcolor){
		$this->model[$day]['bgcolor'] = $bgcolor;
	}

	public function getDayBGColor($day){
		return $this->model[$day]['bgcolor'];
	}

	public function setDayCreationAllowed($day, $crea_allowed){
		$this->model[$day]['creation_allowed'] = $crea_allowed;
	}

	public function isDayCreationAllowed($day){
		return $this->model[$day]['creation_allowed'];
	}

	public function setDayCreationOnClick($day, $crea_onclick){
		$this->model[$day]['creation_onclick'] = $crea_onclick;
	}

	public function getDayCreationOnClick($day){
		return $this->model[$day]['creation_onclick'];
	}

	public function addDisplayedEvent($day, $evt){
		$this->model[$day]['events'][] = $evt;
	}

	public function getDateDebTimestp(){
		return $this->datedeb_timestp;
	}

	public function getDateFinTimestp(){
		return $this->datefin_timestp;
	}

	public function getSimpleDateDeb(){
		return $this->datedeb;
	}

	public function getSimpleDateFin(){
		return $this->datefin;
	}

	public function displayPlanning($tpl_file){
		$planning = $this;
		require_once($tpl_file);
	}

	public function isCoveredDay($day){
		return isset($this->model[$day]);
	}

	public function getPreviousStep(){
		return $this->prev;
	}

	public function getNextStep(){
		return $this->next;
	}

	public function isToday($day){
		return $day == $this->today;
	}

	//--------- pour le planning mode jour et semaine
	static function getNumericHour($h){
		$dh = substr($h,0,2);
		$dm = substr($h,3,2);
		return $dh+($dm/60);
	}

	static function getChevauchement($liste, $event, $deb){
		$cpt  = 1;
		$pos  = 0;
		$zindex  = 2;
		$trouve  = false;

		foreach($liste as $e){

			if($e['creneau_id'] != $event['creneau_id']){
				$hd = dims_planning::getNumericHour($e['heuredeb']);
				$hf = dims_planning::getNumericHour($e['heurefin']);
				if($deb >= $hd && $hd < $hf){
					$hcompare = floor($hd);
					$hparam = floor($deb);
					if($hparam==$hcompare){
						$cpt++;
						if($trouve==false) $pos++;
					}
					$zindex++;
				}
			}
			else{
				$trouve = true;
			}
		}
		$res = array();
		$res[0] = $pos;
		$res[1] = floor(100/$cpt);
		$res[2] = $zindex;
		return $res;
	}

}
?>
