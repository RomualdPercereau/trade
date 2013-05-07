<?php

include ("../pchart/examples/azerty.php");
include("../pchart/class/pData.class.php");
include("../pchart/class/pDraw.class.php");
include("../pchart/class/pImage.class.php");


class Trader
{
	public $start_capital;
	public $total_days;
	public $left_days;
	public $days_past;
	public $values;
    public $tendances;
    private $owned = 0;
    private $average_buy_value = 0;

    public function __construct()
	{
		$this->tendances = new stdClass;
		$this->tendances->mma = array();
		$this->tendances->mmp = array();
		$this->tendances->mme = array(6000);
		$this->tendances->macd = array();
	}


	private function buy($curr_macd)
	{
		$nb_buy = 0;
		if ($this->owned == 0 && $curr_macd > 0)
		{
			$nb_buy = 1;//rand (1, 4);
			$this->update_buy_value($nb_buy);
			$this->owned += $nb_buy;
			return ($nb_buy);
		}
		return (0);
	}


	private function sell($curr_macd)
	{
		if (end($this->values) > $this->average_buy_value && $this->owned > 0 && $curr_macd < 0)
		{
			$val = $this->owned;
			$this->owned = 0;
			return ($val);
		}
		return (0);
	}

	private function end()
	{
		if ($this->days_past == $this->total_days)
		{
			//@chart($this->tendances->macd, "macd");				
			//@chart($this->tendances->mmp, "mmp");				
			//@chart($this->tendances->mma, "mma");				
			//@chart($this->tendances->mme, "mme");				
			//@chart($this->values, "values");				

			return ($this->owned);
		}		
	}

	public function get_decision()
	{
		$nb = 0;
		$this->do_calcul();
		$curr_macd = end($this->tendances->macd);
		debug("decision : $curr_macd $this->owned\n");
		if ($nb = $this->buy($curr_macd))
			return ("buy $nb");
		if ($nb = $this->sell($curr_macd))
			return ("sell $nb");
		if ($nb = $this->end())
			return ("sell $nb");
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
     if ($mme < 5000)
     	$mme = 5000;
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
     		if ($macd > 400 || $macd < -400)
     			$macd = 0;
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
 	debug("\npossessions  :");
 	debug(print_r($this->owned, true));
 	debug("\average_buy_value  :");
 	debug(print_r($this->average_buy_value, true));
 	debug("\nbudget  :");
 	debug(print_r($this->start_capital, true));
 	debug("\n===\n");
 }

 private function update_buy_value($nb_buy)
 {
 	$this->average_buy_value = (end($this->values) * $nb_buy +  $this->average_buy_value * $this->owned) / ($nb_buy + $this->owned);
 }

}