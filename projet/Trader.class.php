<?php

include("../pchart/examples/azerty.php");
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
		$this->tendances->mme = array();
		$this->tendances->macd = array();
		$this->tendances->cash = array();
	}

	private function main_can_buy()
	{
		$action_value = end($this->values) + 1;
		$nb = ($this->start_capital / 3) / ($action_value + 0.015 * $action_value);
		return ($nb);
	}

	private function buy($curr_macd)
	{
		$nb_buy = 0;
		if ($this->owned == 0 && $curr_macd > 0)
		{
			$nb_buy = $this->main_can_buy();//rand (1, 4);
			$this->update_buy_value($nb_buy);
			$this->owned += $nb_buy;
			$this->start_capital -= $nb_buy * (end($this->values) + 0.015 * end($this->values)); 
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
			$this->start_capital += $val * (end($this->values) - 0.015 * end($this->values)); 
			return ($val);
		}
		return (0);
	}

	private function end()
	{
		if ($this->days_past == $this->total_days)
		{
			
			@chart($this->tendances->macd, "macd");				
			//@chart($this->tendances->mmp, "mmp");				
			@chart($this->tendances->mma, "mma");				
			@chart($this->tendances->mme, "mme");				
			@chart($this->values, "values");				
			@chart($this->tendances->variance, "variance");				
			@chart($this->tendances->cash, "cash");
			return ($this->owned);
		}		
	}

	public function get_decision()
	{
		$this->tendances->cash[] = $this->start_capital + $this->owned * end($this->values);
		$nb = 0;
		$this->do_calcul();
		$curr_macd = end($this->tendances->macd);
		debug("decision : $curr_macd $this->owned\n");
		if ($nb = floor($this->buy($curr_macd)))
			return ("buy $nb");
		if ($nb = floor($this->sell($curr_macd)))
			return ("sell $nb");
		if ($nb = floor($this->end()))
			return ("sell $nb");
		return ("wait");
	}

	private function mma()
	{
		if ($this->days_past == 1)
		{
			$this->tendances->mma[] = $this->values[0];
			return ($this->values[0]);
		}
		$mma = 0;
		for ($n = 0; $n < $this->days_past - 1; $n++)
			$mma += $this->values[$n];
		$mma /= $this->days_past;
		$this->tendances->mma[] = $mma;
		return ($mma);
	}

	private function mmp()
	{
		$mmp = 0;
		for ($n = 0, $coeff = 0; $n < $this->days_past; $n++)
		{
			$mmp += $this->values[$n] * ($n + 1);
			$coeff += ($n + 1);
		}
		$mmp /= $coeff;
		$this->tendances->mmp[] = $mmp;
		return ($mmp);
	}

	private function variance()
	{
		$res = 0;
		if ($this->days_past > 1)
		{
			$moy = array_sum($this->values) / ($this->days_past - 1);
			//$res = pow(($this->values[$this->days_past - 1] + $moy), 2);
			$res = ($this->values[$this->days_past - 1] + $moy);
			$this->tendances->variance[] = $res;
			return ($res);
		}
		$this->tendances->variance[] = $this->values[$this->days_past - 1] * 2;
		return ($res);
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

     if ($this->days_past == 1)
     {
     	$this->tendances->mme[] = $this->values[0];
     	return ($this->values[0]);
     }
     $last_mme = end($this->tendances->mme);
     $value = $this->values[$this->days_past - 1];
     $jour = $this->days_past;
     $mme = ($value - $last_mme) * ( 2 / ($jour + 1)) + $last_mme;
     $this->tendances->mme[] = $mme;
     return ($mme);
	}
    
     private function macd()
     {
     	if ($this->days_past >= 30)
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
 	debug(print_r($this->mma(), true));
 	debug("\ncourt/moyen terme (mmp) :");
 	debug(print_r($this->mmp(), true));
 	debug("\ncourt/moyen terme (mme) :");
 	debug(print_r($this->mme(), true));
 	debug("\ndétection de tendance (macd) :");
 	debug(print_r($this->macd(), true));
 	debug("\nVARIANCE :");
 	debug(print_r($this->variance(), true));
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