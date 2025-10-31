<?php

use PHPUnit\Framework\TestCase;
use Player\PlayerNameDecorator;
use Player\PlayerData;

class PlayerNameDecoratorTest extends TestCase
{
    private $decorator;

    protected function setUp(): void
    {
        $this->decorator = new PlayerNameDecorator();
    }

    public function testDecoratePlayerNameWithNoTeam()
    {
        $playerData = new PlayerData();
        $playerData->name = "John Doe";
        $playerData->teamID = 0;
        $playerData->ordinal = 1;
        $playerData->contractCurrentYear = 1;
        $playerData->contractTotalYears = 3;
        
        $result = $this->decorator->decoratePlayerName($playerData);
        
        $this->assertEquals("John Doe", $result);
    }

    public function testDecoratePlayerNameOnWaivers()
    {
        $playerData = new PlayerData();
        $playerData->name = "John Doe";
        $playerData->teamID = 5;
        $playerData->ordinal = JSB::WAIVERS_ORDINAL + 1;
        $playerData->contractCurrentYear = 1;
        $playerData->contractTotalYears = 3;
        
        $result = $this->decorator->decoratePlayerName($playerData);
        
        $this->assertEquals("(John Doe)*", $result);
    }

    public function testDecoratePlayerNameEligibleForFreeAgency()
    {
        $playerData = new PlayerData();
        $playerData->name = "John Doe";
        $playerData->teamID = 5;
        $playerData->ordinal = 1;
        $playerData->contractCurrentYear = 3;
        $playerData->contractTotalYears = 3;
        
        $result = $this->decorator->decoratePlayerName($playerData);
        
        $this->assertEquals("John Doe^", $result);
    }

    public function testDecoratePlayerNameRegular()
    {
        $playerData = new PlayerData();
        $playerData->name = "John Doe";
        $playerData->teamID = 5;
        $playerData->ordinal = 1;
        $playerData->contractCurrentYear = 2;
        $playerData->contractTotalYears = 3;
        
        $result = $this->decorator->decoratePlayerName($playerData);
        
        $this->assertEquals("John Doe", $result);
    }
}
