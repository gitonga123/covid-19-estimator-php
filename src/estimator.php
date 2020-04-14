
<?php

function covid19ImpactEstimator($data)
{
    $new_data = [
        "data" => $data,
        'impact' => calculateImpact(
            $data,
            10
        ),
        'severeImpact' => calculateImpact(
            $data,
            50
        )
    ];
    return $new_data;
}


function calculateImpact($data, $reportedCasesMultiplier)
{
    $currentlyInfected = $data['reportedCases'] * $reportedCasesMultiplier;
    $numberOfDays = convertPeriodTypeToNumberOfDays($data);
    $factor = pow(2, intval($numberOfDays / 3));
    $infectionsByRequestedTime = $currentlyInfected * $factor;
    $severeCasesByRequestedTime = $infectionsByRequestedTime * 0.15;
    $hospitalBedsByRequestedTime = intval(($data['totalHospitalBeds'] * 0.35) - $severeCasesByRequestedTime);
    $casesForICUByRequestedTime = intval($infectionsByRequestedTime * 0.05);
    $casesForVentilatorsByRequestedTime = intval($infectionsByRequestedTime * 0.02);

    $dollarsInFlight = intval(($infectionsByRequestedTime * $data['region']['avgDailyIncomePopulation'] * $data['region']['avgDailyIncomeInUSD']) / $numberOfDays);

    return [
        'currentlyInfected' => $currentlyInfected,
        "infectionsByRequestedTime" => $infectionsByRequestedTime,
        "severeCasesByRequestedTime" => $severeCasesByRequestedTime,
        "hospitalBedsByRequestedTime" => $hospitalBedsByRequestedTime,
        "casesForICUByRequestedTime" => $casesForICUByRequestedTime,
        "casesForVentilatorsByRequestedTime" => $casesForVentilatorsByRequestedTime,
        'dollarsInFlight' => $dollarsInFlight
    ];
}

function convertPeriodTypeToNumberOfDays($data)
{
    $periodType = $data['periodType'];
    $timeToElapse = $data['timeToElapse'];
    $numberOfDays = '';
    switch ($periodType) {
        case 'weeks':
            $numberOfDays = $timeToElapse * 7;
            break;
        case 'months':
            $numberOfDays = $timeToElapse * 30;
            break;
        default:
            $numberOfDays = $timeToElapse;
            break;
    }

    return $numberOfDays;
}
