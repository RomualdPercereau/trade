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
		do_calcul($this);
		return ("wait");
	}

}