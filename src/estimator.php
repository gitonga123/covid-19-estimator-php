
<?php

function covid19ImpactEstimator($data)
{


  $new_data = [
    "data" => $data,
    'impact' => calculateImpact($data),
    'severeImpact' => calculateSevereImpact($data)
  ];
  return $new_data;
}

function convertJsonToArray($data)
{
  return json_decode($data, true);
}

function convertArrayToJson($data)
{
  return json_encode($data);
}

function calculateImpact($data)
{
  $currentlyInfected = $data['reportedCases'] * 10;
  $factor = pow(2, intval(convertPeriodTypeToNumberOfDays($data) / 3));
  $infectionsByRequestedTime = $currentlyInfected * $factor;

  return ['currentlyInfected' => $currentlyInfected, "infectionsByRequestedTime" => $infectionsByRequestedTime];
}

function calculateSevereImpact($data)
{
  $currentlyInfected = $data['reportedCases'] * 50;
  $factor = pow(2, intval(convertPeriodTypeToNumberOfDays($data) / 3));
  $infectionsByRequestedTime = $currentlyInfected * $factor;

  return ['currentlyInfected' => $currentlyInfected, "infectionsByRequestedTime" => $infectionsByRequestedTime];
}

function convertPeriodTypeToNumberOfDays($data)
{
  $periodType = $data['periodType'];
  $timeToElapse = $data['timeToElapse'];
  $number_of_days = '';
  switch ($periodType) {
    case 'weeks':
      $number_of_days = $timeToElapse * 7;
      break;
    case 'months':
      $number_of_days = $timeToElapse * 30;
      break;
    default:
      $number_of_days = $timeToElapse;
      break;
  }

  return $number_of_days;
}
