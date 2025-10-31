<?php

use PHPUnit\Framework\TestCase;
use Waivers\WaiversProcessor;

class WaiversProcessorTest extends TestCase
{
    private $processor;
    
    protected function setUp(): void
    {
        $this->processor = new WaiversProcessor();
    }
    
    public function testCalculateVeteranMinimumSalaryFor10PlusYears()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(10);
        $this->assertEquals(103, $salary);
        
        $salary = $this->processor->calculateVeteranMinimumSalary(15);
        $this->assertEquals(103, $salary);
    }
    
    public function testCalculateVeteranMinimumSalaryFor9Years()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(9);
        $this->assertEquals(100, $salary);
    }
    
    public function testCalculateVeteranMinimumSalaryFor8Years()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(8);
        $this->assertEquals(89, $salary);
    }
    
    public function testCalculateVeteranMinimumSalaryFor7Years()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(7);
        $this->assertEquals(82, $salary);
    }
    
    public function testCalculateVeteranMinimumSalaryFor6Years()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(6);
        $this->assertEquals(76, $salary);
    }
    
    public function testCalculateVeteranMinimumSalaryFor5Years()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(5);
        $this->assertEquals(70, $salary);
    }
    
    public function testCalculateVeteranMinimumSalaryFor4Years()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(4);
        $this->assertEquals(64, $salary);
    }
    
    public function testCalculateVeteranMinimumSalaryFor3Years()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(3);
        $this->assertEquals(61, $salary);
    }
    
    public function testCalculateVeteranMinimumSalaryForRookies()
    {
        $salary = $this->processor->calculateVeteranMinimumSalary(0);
        $this->assertEquals(51, $salary);
        
        $salary = $this->processor->calculateVeteranMinimumSalary(1);
        $this->assertEquals(51, $salary);
        
        $salary = $this->processor->calculateVeteranMinimumSalary(2);
        $this->assertEquals(51, $salary);
    }
    
    public function testGetPlayerContractDisplayWithNoSalary()
    {
        $playerData = [
            'cy1' => 0,
            'exp' => 5
        ];
        
        $contract = $this->processor->getPlayerContractDisplay($playerData);
        $this->assertEquals('70', $contract);
    }
    
    public function testGetPlayerContractDisplayWithExistingContract()
    {
        $playerData = [
            'cy1' => 500,
            'cy' => 1,
            'cyt' => 3,
            'cy1' => 500,
            'cy2' => 550,
            'cy3' => 600
        ];
        
        $contract = $this->processor->getPlayerContractDisplay($playerData);
        $this->assertEquals('500 550 600', $contract);
    }
    
    public function testGetPlayerContractDisplayWithPartialContract()
    {
        $playerData = [
            'cy1' => 500,
            'cy' => 2,
            'cyt' => 3,
            'cy2' => 550,
            'cy3' => 600
        ];
        
        $contract = $this->processor->getPlayerContractDisplay($playerData);
        $this->assertEquals('550 600', $contract);
    }
    
    public function testGetPlayerContractDisplayWithOneYearRemaining()
    {
        $playerData = [
            'cy1' => 500,
            'cy' => 3,
            'cyt' => 3,
            'cy3' => 600
        ];
        
        $contract = $this->processor->getPlayerContractDisplay($playerData);
        $this->assertEquals('600', $contract);
    }
    
    public function testGetWaiverWaitTimeReturnsEmptyWhenCleared()
    {
        $dropTime = time() - 90000; // More than 24 hours ago
        $currentTime = time();
        
        $waitTime = $this->processor->getWaiverWaitTime($dropTime, $currentTime);
        $this->assertEquals('', $waitTime);
    }
    
    public function testGetWaiverWaitTimeCalculatesRemainingTime()
    {
        $currentTime = time();
        $dropTime = $currentTime - 3600; // 1 hour ago
        
        $waitTime = $this->processor->getWaiverWaitTime($dropTime, $currentTime);
        $this->assertStringContainsString('Clears in', $waitTime);
        $this->assertStringContainsString('23 h', $waitTime); // Should be 23 hours remaining
    }
    
    public function testGetWaiverWaitTimeWithMinutes()
    {
        $currentTime = time();
        $dropTime = $currentTime - 82800; // 23 hours ago
        
        $waitTime = $this->processor->getWaiverWaitTime($dropTime, $currentTime);
        $this->assertStringContainsString('Clears in', $waitTime);
        $this->assertStringContainsString('1 h', $waitTime); // Should be 1 hour remaining
    }
    
    public function testPrepareContractDataForNewContract()
    {
        $playerData = [
            'cy1' => 0,
            'exp' => 8
        ];
        
        $contractData = $this->processor->prepareContractData($playerData);
        
        $this->assertTrue($contractData['isNewContract']);
        $this->assertEquals(89, $contractData['cy1']);
        $this->assertEquals('89', $contractData['finalContract']);
    }
    
    public function testPrepareContractDataForExistingContract()
    {
        $playerData = [
            'cy1' => 500,
            'cy' => 1,
            'cyt' => 3,
            'cy1' => 500,
            'cy2' => 550,
            'cy3' => 600
        ];
        
        $contractData = $this->processor->prepareContractData($playerData);
        
        $this->assertFalse($contractData['isNewContract']);
        $this->assertArrayNotHasKey('cy1', $contractData);
        $this->assertEquals('500 550 600', $contractData['finalContract']);
    }
    
    public function testPrepareContractDataForMidContract()
    {
        $playerData = [
            'cy1' => 500,
            'cy' => 2,
            'cyt' => 3,
            'cy2' => 550,
            'cy3' => 600
        ];
        
        $contractData = $this->processor->prepareContractData($playerData);
        
        $this->assertFalse($contractData['isNewContract']);
        $this->assertEquals('550 600', $contractData['finalContract']);
    }
    
    public function testGetPlayerContractDisplayWithMissingExperience()
    {
        $playerData = [
            'cy1' => 0
            // exp field missing
        ];
        
        $contract = $this->processor->getPlayerContractDisplay($playerData);
        $this->assertEquals('51', $contract); // Default to rookie minimum
    }
    
    public function testGetPlayerContractDisplayWithEmptyContract()
    {
        $playerData = [
            'cy1' => 0,
            'cy' => 1,
            'cyt' => 1
            // No cy1 field with value
        ];
        
        $contract = $this->processor->getPlayerContractDisplay($playerData);
        $this->assertEquals('51', $contract); // Should use vet min calculation
    }
}
