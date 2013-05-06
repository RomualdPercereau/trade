<?php


class Trader
{
	public $start_capital;
	public $total_days;
	public $left_days;
	public $days_past;
	public $values;

    public $tendances;

    public function __construct()
	{
		$this->tendances = new stdClass;
		$this->tendances->mma = array();
		$this->tendances->mmp = array();
		$this->tendances->mme = array(0);
		$this->tendances->macd = array();
	}

	public function get_decision()
	{
		//debug(print_r($this, true));
		$this->do_calcul();
		return ("wait");
	}

	private function mma($data, $jours)
	{
		$mma = 0;
		for ($n = 0; $n < $jours - 1; $n++)
			$mma += $data[$n];
		$mma /= $jours;
		$this->tendances->mma[] = $mma;
		return ($mma);
	}

	private function mmp($data, $jours)
	{
		$mmp = 0;
		for ($n = 0, $coeff = 0; $n < $jours; $n++)
		{
			$mmp += $data[$n] * ($n + 1);
			$coeff += ($n + 1);
		}
		$mmp /= $coeff;
		$this->tendances->mmp[] = $mmp;
		return ($mmp);
	}

	private function mme()
	{/*
	La Moyenne Mobile Pondérée 
     MMP(p) = (Somme des (p-n)*Cours(n))) / (p*(p+1)/2) 
	où n varie de 0 à p - 1*/
	/*
	La Moyenne Mobile Exponentielle
     MME(p) = (Dernier cours - (MME(p) de la veille))*K + (MME(p) de la veille) 
     où K = 2/(p+1)*/
     $last_mme = end($this->tendances->mme);
     $value = $this->values[$this->days_past - 1];
     $jour = $this->days_past;
     $mme = ($value - $last_mme) * ( 2 / ($jour + 1)) + $last_mme;
     $this->tendances->mme[] = $mme;
     return ($mme);
	}
    
     private function macd()
     {
     	if ($this->days_past >= 26)
     	{
     		$mme12 = $this->tendances->mme[$this->days_past - 12];
     		$mme26 = $this->tendances->mme[$this->days_past - 26];
     		$macd = $mme26 - $mme12;
     		$this->tendances->macd[] = $macd;
     		return ($macd);
     	}
     	return (0);
     }

 private function do_calcul()
 {
 	debug("===\n");
 	debug("long terme (mma) :");
 	debug(print_r($this->mma($this->values, $this->days_past), true));
 	debug("\ncourt/moyen terme (mmp) :");
 	debug(print_r($this->mmp($this->values, $this->days_past), true));
 	debug("\ncourt/moyen terme (mme) :");
 	debug(print_r($this->mme(), true));
 	debug("\ndétection de tendance (macd) :");
 	debug(print_r($this->macd(), true));
 	debug("\n===\n");
 }


}