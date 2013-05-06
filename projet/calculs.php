<?php

function mma($data, $jours)
{
	$mma = 0;
	for ($n = 0; $n < $jours - 1; $n++)
		$mma += $data[$n];
	$mma /= $jours;
	return ($mma);
}

function mmp($data, $jours)
{
	for ($n = 0, $coeff = 0; $n < $jours; $n++)
	{
		$mmp += $data[$n] * ($n + 1);
		$coeff += ($n + 1);
	}
	$mmp /= $coeff;
	return ($mmp);
}

function do_calcul($trader)
{
	debug("===\n");
	debug("long terme (mma) :");
	debug(print_r(mma($trader->values, $trader->days_past), true));
	debug("\ncourt/moyen terme (mmp) :");
	debug(print_r(mmp($trader->values, $trader->days_past), true));
	debug("\n===\n");
}