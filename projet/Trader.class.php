<?php

require "calculs.php";

class Trader
{
	public $start_capital;
	public $total_days;
	public $left_days;
	public $days_past;
	public $values;

	public function get_decision()
	{
		debug(print_r($this, true));
		$this->do_calcul();
		return ("wait");
	}

private function mma($data, $jours)
{
	$mma = 0;
	for ($n = 0; $n < $jours - 1; $n++)
		$mma += $data[$n];
	$mma /= $jours;
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
	return ($mmp);
}

private function mme($last_mme, $value, $jour)
{/*
La Moyenne Mobile Pondérée 
     MMP(p) = (Somme des (p-n)*Cours(n))) / (p*(p+1)/2) 
     où n varie de 0 à p - 1*/
/*
La Moyenne Mobile Exponentielle
     MME(p) = (Dernier cours - (MME(p) de la veille))*K + (MME(p) de la veille) 
     où K = 2/(p+1)*/
     $mme = ($value - $last_mme) * ( 2 / ($jour + 1)) + $last_mme;
     return ($mme);
}

private function do_calcul()
{
	debug("===\n");
	debug("long terme (mma) :");
	debug(print_r($this->mma($this->values, $this->days_past), true));
	debug("\ncourt/moyen terme (mmp) :");
	debug(print_r($this->mmp($this->values, $this->days_past), true));
	debug("\n===\n");
}


}