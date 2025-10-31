<?php

use PHPUnit\Framework\TestCase;
use Player\PlayerInjuryCalculator;
use Player\PlayerData;

class PlayerInjuryCalculatorTest extends TestCase
{
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PlayerInjuryCalculator();
    }

    public function testGetInjuryReturnDateWithInjury()
    {
        $playerData = new PlayerData();
        $playerData->daysRemainingForInjury = 5;
        
        $result = $this->calculator->getInjuryReturnDate($playerData, '2024-01-01');
        
        $this->assertEquals('2024-01-07', $result);
    }

    public function testGetInjuryReturnDateWithoutInjury()
    {
        $playerData = new PlayerData();
        $playerData->daysRemainingForInjury = 0;
        
        $result = $this->calculator->getInjuryReturnDate($playerData, '2024-01-01');
        
        $this->assertEquals("", $result);
    }

    public function testGetInjuryReturnDateWithOneDayInjury()
    {
        $playerData = new PlayerData();
        $playerData->daysRemainingForInjury = 1;
        
        $result = $this->calculator->getInjuryReturnDate($playerData, '2024-01-01');
        
        $this->assertEquals('2024-01-03', $result);
    }

    public function testGetInjuryReturnDateCrossingMonth()
    {
        $playerData = new PlayerData();
        $playerData->daysRemainingForInjury = 10;
        
        $result = $this->calculator->getInjuryReturnDate($playerData, '2024-01-25');
        
        $this->assertEquals('2024-02-05', $result);
    }
}
