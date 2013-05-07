#!/usr/local/bin/php
<?php

/*
* moyenne moblie -> tendance à moyen et long terme 
*/

function mma($jours, $cours)
{
  $mma = 0;
  for ($n = 0; $n < $jours - 1; $n++)
    $mma += $cours[$n];
  $mma /= $jours;
  printf("MMA %d = %f\n", $jours, $mma);
}

/*
* moyenne moblie -> tendance à court terme (plus grande importance aux dernières valeurs)
*/

function mmp($jours, $cours)
{
  for ($n = 0, $coeff = 0; $n < $jours; $n++)
    {
      $mmp += $cours[$n] * ($n + 1);
      $coeff += ($n + 1);
    }
  $mmp /= $coeff;
  printf("MMP %d = %f\n", $coeff, $mmp);
}

function variance($cours)
{
  $i = 0;
  $sum = 0;
  $moy = 
  while (isset($cours[$si]))
    {
      $sum += $cours[$i] + 
      $i++;
    }
}

?>