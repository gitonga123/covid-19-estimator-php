<?php
require_once(dirname(__FILE__) . '/../../src/estimator.php');

use PHPUnit\Framework\TestCase;

class ImpactEstimatorTest extends TestCase
{
    protected $data;
    protected $decoded_data;

    public function setUp(): void
    {
        $this->data = '{"data":{"region":{"name":"Africa","avgAge":19.7,"avgDailyIncomeInUSD":3,"avgDailyIncomePopulation":0.71},"periodType":"months","timeToElapse":3,"reportedCases":553,"population":8265701,"totalHospitalBeds":66934},"impact":{"currentlyInfected":5530,"infectionsByRequestedTime":5937792286720},"severeImpact":{"currentlyInfected":27650,"infectionsByRequestedTime":29688961433600}}';
        $this->decoded_data = convertJsonToArray($this->data, true);
    }
    public function testConvertJsonToArray()
    {
        $data = convertJsonToArray($this->data);
        $this->assertIsArray($data);
    }

    public function testCalculateImpact()
    {

        $result = calculateImpact($this->decoded_data);
        $currentlyInfected = $this->decoded_data['impact']['currentlyInfected'];
        $infectionsByRequestedTime = $this->decoded_data['impact']['infectionsByRequestedTime'];
        $impact = ["currentlyInfected" => $currentlyInfected, "infectionsByRequestedTime" => $infectionsByRequestedTime];

        $this->assertEquals($impact, $result);
    }

    public function testCalculateSevereImpact()
    {
        $result = calculateSevereImpact($this->decoded_data);
        $currentlyInfected = $this->decoded_data['severeImpact']['currentlyInfected'];
        $infectionsByRequestedTime = $this->decoded_data['severeImpact']['infectionsByRequestedTime'];
        $severImpact = ["currentlyInfected" => $currentlyInfected, "infectionsByRequestedTime" => $infectionsByRequestedTime];

        $this->assertEquals($severImpact, $result);
    }

    public function testCovid19ImpactEstimator()
    {
        $data = [
            'data' => $this->decoded_data['data'],
            "estimates" => [
                'impact' => calculateImpact($this->decoded_data),
                'severeImpact' => calculateSevereImpact($this->decoded_data)
            ]
        ];
        $data_json = convertArrayToJson($data);

        $result = covid19ImpactEstimator($this->data);

        $this->assertEquals($data_json, $result);
    }
}
