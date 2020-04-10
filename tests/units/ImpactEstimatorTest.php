<?php
require_once(dirname(__FILE__) . '/../../src/estimator.php');

use PHPUnit\Framework\TestCase;

class ImpactEstimatorTest extends TestCase
{
    protected $data;

    public function setUp(): void
    {
        $this->data = '{ "region": { "name": "Africa", "avgAge": 19.7, "avgDailyIncomeInUSD": 4, "avgDailyIncomePopulation": 0.73 }, "periodType": "days", "timeToElapse": 38, "reportedCases": 2747, "population": 92931687, "totalHospitalBeds": 678874}';
        $this->decoded_data = json_decode($this->data, true);
    }
    public function testConvertJsonToArray()
    {
        $data = convertJsonToArray($this->data);
        $this->assertIsArray($data);
    }

    public function testCalculateImpact()
    {

        $result = calculateImpact($this->decoded_data);
        $currentlyInfected = 2747 * 10;
        $infectionsByRequestedTime = $currentlyInfected * (pow(2, intval(38 / 3)));
        $impact = ["currentlyInfected" => $currentlyInfected, "infectionsByRequestedTime" => $infectionsByRequestedTime];

        $this->assertEquals($impact, $result);
    }

    public function testCalculateSevereImpact()
    {
        $result = calculateSevereImpact($this->decoded_data);
        $currentlyInfected = 2747 * 50;
        $infectionsByRequestedTime = $currentlyInfected * (pow(2, intval(38 / 3)));
        $severImpact = ["currentlyInfected" => $currentlyInfected, "infectionsByRequestedTime" => $infectionsByRequestedTime];

        $this->assertEquals($severImpact, $result);
    }

    public function testCovid19ImpactEstimator()
    {
        $data = [
            'data' => $this->decoded_data,
            'impact' => calculateImpact($this->decoded_data),
            'severeImpact' => calculateSevereImpact($this->decoded_data)
        ];
        $data_json = convertArrayToJson($data);

        $result = covid19ImpactEstimator($this->decoded_data);

        $this->assertEquals($data_json, $result);
    }
}
