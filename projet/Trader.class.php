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