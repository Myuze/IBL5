<?php

use PHPUnit\Framework\TestCase;
use Player\PlayerContractValidator;
use Player\PlayerData;

class PlayerContractValidatorTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new PlayerContractValidator();
    }

    public function testCanRenegotiateContractWhenInLastYear()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 6;
        
        $result = $this->validator->canRenegotiateContract($playerData);
        
        $this->assertTrue($result);
    }

    public function testCanRenegotiateContractWhenYear1WithNoYear2()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 1;
        $playerData->contractYear2Salary = 0;
        
        $result = $this->validator->canRenegotiateContract($playerData);
        
        $this->assertTrue($result);
    }

    public function testCanRenegotiateContractWhenYear2WithNoYear3()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 2;
        $playerData->contractYear3Salary = 0;
        
        $result = $this->validator->canRenegotiateContract($playerData);
        
        $this->assertTrue($result);
    }

    public function testCannotRenegotiateContractWhenYearHasNext()
    {
        $playerData = new PlayerData();
        $playerData->contractCurrentYear = 2;
        $playerData->contractYear3Salary = 1000;
        
        $result = $this->validator->canRenegotiateContract($playerData);
        
        $this->assertFalse($result);
    }

    public function testCanRookieOptionFirstRoundDuringFreeAgency()
    {
        $playerData = new PlayerData();
        $playerData->draftRound = 1;
        $playerData->yearsOfExperience = 2;
        $playerData->contractYear4Salary = 0;
        
        $result = $this->validator->canRookieOption($playerData, 'Free Agency');
        
        $this->assertTrue($result);
    }

    public function testCanRookieOptionSecondRoundDuringFreeAgency()
    {
        $playerData = new PlayerData();
        $playerData->draftRound = 2;
        $playerData->yearsOfExperience = 1;
        $playerData->contractYear3Salary = 0;
        
        $result = $this->validator->canRookieOption($playerData, 'Free Agency');
        
        $this->assertTrue($result);
    }

    public function testCanRookieOptionFirstRoundDuringPreseason()
    {
        $playerData = new PlayerData();
        $playerData->draftRound = 1;
        $playerData->yearsOfExperience = 3;
        $playerData->contractYear4Salary = 0;
        
        $result = $this->validator->canRookieOption($playerData, 'Preseason');
        
        $this->assertTrue($result);
    }

    public function testCanRookieOptionSecondRoundDuringHEAT()
    {
        $playerData = new PlayerData();
        $playerData->draftRound = 2;
        $playerData->yearsOfExperience = 2;
        $playerData->contractYear3Salary = 0;
        
        $result = $this->validator->canRookieOption($playerData, 'HEAT');
        
        $this->assertTrue($result);
    }

    public function testCannotRookieOptionDuringRegularSeason()
    {
        $playerData = new PlayerData();
        $playerData->draftRound = 1;
        $playerData->yearsOfExperience = 2;
        $playerData->contractYear4Salary = 0;
        
        $result = $this->validator->canRookieOption($playerData, 'Regular Season');
        
        $this->assertFalse($result);
    }

    public function testWasRookieOptionedFirstRound()
    {
        $playerData = new PlayerData();
        $playerData->yearsOfExperience = 4;
        $playerData->draftRound = 1;
        $playerData->contractYear3Salary = 369;
        $playerData->contractYear4Salary = 738;
        
        $result = $this->validator->wasRookieOptioned($playerData);
        
        $this->assertTrue($result);
    }

    public function testWasRookieOptionedSecondRound()
    {
        $playerData = new PlayerData();
        $playerData->yearsOfExperience = 3;
        $playerData->draftRound = 2;
        $playerData->contractYear2Salary = 51;
        $playerData->contractYear3Salary = 102;
        
        $result = $this->validator->wasRookieOptioned($playerData);
        
        $this->assertTrue($result);
    }

    public function testWasNotRookieOptioned()
    {
        $playerData = new PlayerData();
        $playerData->yearsOfExperience = 4;
        $playerData->draftRound = 1;
        $playerData->contractYear3Salary = 369;
        $playerData->contractYear4Salary = 0;
        
        $result = $this->validator->wasRookieOptioned($playerData);
        
        $this->assertFalse($result);
    }
}
