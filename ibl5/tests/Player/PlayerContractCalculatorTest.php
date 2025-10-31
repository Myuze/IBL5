<?php

use PHPUnit\Framework\TestCase;
use Player\PlayerContractCalculator;
use Player\PlayerData;

class PlayerContractCalculatorTest extends TestCase
{
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PlayerContractCalculator();
    }

    public function testGetCurrentSeasonSalaryForYear1()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 1;
        $playerData->contractYear1Salary = 1000;
        $playerData->contractYear2Salary = 1100;
        
        $result = $this->calculator->getCurrentSeasonSalary($playerData);
        
        $this->assertEquals(1000, $result);
    }

    public function testGetCurrentSeasonSalaryForYear2()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 2;
        $playerData->contractYear1Salary = 1000;
        $playerData->contractYear2Salary = 1100;
        $playerData->contractYear3Salary = 1200;
        
        $result = $this->calculator->getCurrentSeasonSalary($playerData);
        
        $this->assertEquals(1100, $result);
    }

    public function testGetCurrentSeasonSalaryForYear0()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 0;
        $playerData->contractYear1Salary = 1000;
        
        $result = $this->calculator->getCurrentSeasonSalary($playerData);
        
        $this->assertEquals(1000, $result);
    }

    public function testGetCurrentSeasonSalaryForYear7()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 7;
        
        $result = $this->calculator->getCurrentSeasonSalary($playerData);
        
        $this->assertEquals(0, $result);
    }

    public function testGetNextSeasonSalary()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 2;
        $playerData->contractYear3Salary = 1200;
        
        $result = $this->calculator->getNextSeasonSalary($playerData);
        
        $this->assertEquals(1200, $result);
    }

    public function testGetRemainingContractArray()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 2;
        $playerData->contractTotalYears = 4;
        $playerData->contractYear2Salary = 1100;
        $playerData->contractYear3Salary = 1200;
        $playerData->contractYear4Salary = 1300;
        
        $result = $this->calculator->getRemainingContractArray($playerData);
        
        $this->assertEquals([1 => 1100, 2 => 1200, 3 => 1300], $result);
    }

    public function testGetRemainingContractArrayWithZeroValues()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 0;
        $playerData->contractTotalYears = 0;
        
        $result = $this->calculator->getRemainingContractArray($playerData);
        
        $this->assertEquals([1 => 0], $result);
    }

    public function testGetTotalRemainingSalary()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 2;
        $playerData->contractTotalYears = 4;
        $playerData->contractYear2Salary = 1000;
        $playerData->contractYear3Salary = 1100;
        $playerData->contractYear4Salary = 1200;
        
        $result = $this->calculator->getTotalRemainingSalary($playerData);
        
        $this->assertEquals(3300, $result);
    }

    public function testGetLongBuyoutArray()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 1;
        $playerData->contractTotalYears = 3;
        $playerData->contractYear1Salary = 1200;
        $playerData->contractYear2Salary = 1200;
        $playerData->contractYear3Salary = 1200;
        
        $result = $this->calculator->getLongBuyoutArray($playerData);
        
        $expectedPerYear = round(3600 / 6);
        $this->assertEquals([
            1 => $expectedPerYear, 
            2 => $expectedPerYear, 
            3 => $expectedPerYear, 
            4 => $expectedPerYear, 
            5 => $expectedPerYear, 
            6 => $expectedPerYear
        ], $result);
    }

    public function testGetShortBuyoutArray()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 1;
        $playerData->contractTotalYears = 2;
        $playerData->contractYear1Salary = 1000;
        $playerData->contractYear2Salary = 1000;
        
        $result = $this->calculator->getShortBuyoutArray($playerData);
        
        $expectedPerYear = round(2000 / 2);
        $this->assertEquals([1 => $expectedPerYear, 2 => $expectedPerYear], $result);
    }
}
