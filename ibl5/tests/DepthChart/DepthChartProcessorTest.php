<?php

use PHPUnit\Framework\TestCase;
use DepthChart\DepthChartProcessor;

class DepthChartProcessorTest extends TestCase
{
    private $processor;
    
    protected function setUp(): void
    {
        $this->processor = new DepthChartProcessor();
    }
    
    public function testProcessesSubmissionCorrectly()
    {
        $postData = [
            'Name1' => 'Player One',
            'pg1' => '1',
            'sg1' => '0',
            'sf1' => '0',
            'pf1' => '0',
            'c1' => '0',
            'active1' => '1',
            'min1' => '30',
            'OF1' => '0',
            'DF1' => '1',
            'OI1' => '0',
            'DI1' => '0',
            'BH1' => '0',
            'Injury1' => '0',
            'Name2' => 'Player Two',
            'pg2' => '2',
            'sg2' => '0',
            'sf2' => '0',
            'pf2' => '0',
            'c2' => '0',
            'active2' => '1',
            'min2' => '25',
            'OF2' => '0',
            'DF2' => '0',
            'OI2' => '1',
            'DI2' => '-1',
            'BH2' => '0',
            'Injury2' => '0'
        ];
        
        $result = $this->processor->processSubmission($postData, 15);
        
        $this->assertEquals(2, count($result['playerData']));
        $this->assertEquals(2, $result['activePlayers']);
        $this->assertEquals(2, $result['pos_1']);  // Two players at PG
        $this->assertFalse($result['hasStarterAtMultiplePositions']);
    }
    
    public function testDetectsMultipleStartingPositions()
    {
        $postData = [
            'Name1' => 'Player One',
            'pg1' => '1',  // Starting at PG
            'sg1' => '1',  // Also starting at SG - INVALID
            'sf1' => '0',
            'pf1' => '0',
            'c1' => '0',
            'active1' => '1',
            'min1' => '30',
            'OF1' => '0',
            'DF1' => '0',
            'OI1' => '0',
            'DI1' => '0',
            'BH1' => '0',
            'Injury1' => '0'
        ];
        
        $result = $this->processor->processSubmission($postData, 15);
        
        $this->assertTrue($result['hasStarterAtMultiplePositions']);
        $this->assertEquals('Player One', $result['nameOfProblemStarter']);
    }
    
    public function testExcludesInjuredPlayersFromPositionCount()
    {
        $postData = [
            'Name1' => 'Player One',
            'pg1' => '1',
            'sg1' => '0',
            'sf1' => '0',
            'pf1' => '0',
            'c1' => '0',
            'active1' => '1',
            'min1' => '30',
            'OF1' => '0',
            'DF1' => '0',
            'OI1' => '0',
            'DI1' => '0',
            'BH1' => '0',
            'Injury1' => '15'  // Injured
        ];
        
        $result = $this->processor->processSubmission($postData, 15);
        
        $this->assertEquals(0, $result['pos_1']);  // Injured player not counted
        $this->assertEquals(1, $result['activePlayers']);  // Still counts as active
    }
    
    public function testGeneratesCsvContentCorrectly()
    {
        $playerData = [
            [
                'name' => 'Player One',
                'pg' => '1',
                'sg' => '0',
                'sf' => '0',
                'pf' => '0',
                'c' => '0',
                'active' => '1',
                'min' => '30',
                'of' => '0',
                'df' => '1',
                'oi' => '0',
                'di' => '0',
                'bh' => '0'
            ]
        ];
        
        $csv = $this->processor->generateCsvContent($playerData);
        
        $this->assertStringContainsString('Name,PG,SG,SF,PF,C,ACTIVE,MIN,OF,DF,OI,DI,BH', $csv);
        $this->assertStringContainsString('Player One,1,0,0,0,0,1,30,0,1,0,0,0', $csv);
    }
    

    
    public function testSanitizesInputWithMaliciousData()
    {
        $postData = [
            'Name1' => '<script>alert("XSS")</script>Player One',
            'pg1' => '10',  // Out of range (should be capped at 5)
            'sg1' => '-5',  // Negative (should be 0)
            'sf1' => '0',
            'pf1' => '0',
            'c1' => '0',
            'active1' => '2',  // Invalid (should be 0 or 1)
            'min1' => '100',  // Out of range (should be capped at 40)
            'OF1' => '10',  // Out of range (should be capped at 3)
            'DF1' => '-5',  // Out of range (should be 0)
            'OI1' => '10',  // Out of range (should be capped at 2)
            'DI1' => '-10',  // Out of range (should be capped at -2)
            'BH1' => '5',  // Out of range (should be capped at 2)
            'Injury1' => '0'
        ];
        
        $result = $this->processor->processSubmission($postData, 15);
        
        // Player name should have script tags removed (but not the content)
        $this->assertStringNotContainsString('<script>', $result['playerData'][0]['name']);
        $this->assertStringNotContainsString('</script>', $result['playerData'][0]['name']);
        
        // Depth values should be capped at 5
        $this->assertEquals(5, $result['playerData'][0]['pg']);
        $this->assertEquals(0, $result['playerData'][0]['sg']);
        
        // Active should be 0 (invalid value)
        $this->assertEquals(0, $result['playerData'][0]['active']);
        
        // Minutes should be capped at 40
        $this->assertEquals(40, $result['playerData'][0]['min']);
        
        // Focus values should be capped at 3 and 0
        $this->assertEquals(3, $result['playerData'][0]['of']);
        $this->assertEquals(0, $result['playerData'][0]['df']);
        
        // Setting values should be capped between -2 and 2
        $this->assertEquals(2, $result['playerData'][0]['oi']);
        $this->assertEquals(-2, $result['playerData'][0]['di']);
        $this->assertEquals(2, $result['playerData'][0]['bh']);
    }
    
    public function testHandlesMissingOptionalFields()
    {
        $postData = [
            'Name1' => 'Player One',
            'pg1' => '1',
            'sg1' => '0',
            'sf1' => '0',
            'pf1' => '0',
            'c1' => '0',
            'active1' => '1',
            'min1' => '30',
            'OF1' => '0',
            'DF1' => '0',
            'OI1' => '0',
            'DI1' => '0',
            'BH1' => '0'
            // Injury1 is missing
        ];
        
        $result = $this->processor->processSubmission($postData, 15);
        
        $this->assertEquals(1, count($result['playerData']));
        $this->assertEquals('Player One', $result['playerData'][0]['name']);
        $this->assertEquals(0, $result['playerData'][0]['injury']);
    }
    
    public function testGeneratesCsvWithSpecialCharacters()
    {
        $playerData = [
            [
                'name' => 'Player, Jr.',
                'pg' => 1,
                'sg' => 0,
                'sf' => 0,
                'pf' => 0,
                'c' => 0,
                'active' => 1,
                'min' => 30,
                'of' => 0,
                'df' => 1,
                'oi' => -1,
                'di' => 2,
                'bh' => -2
            ]
        ];
        
        $csv = $this->processor->generateCsvContent($playerData);
        
        $this->assertStringContainsString('Player, Jr.', $csv);
        $this->assertStringContainsString('1,0,0,0,0,1,30,0,1,-1,2,-2', $csv);
    }
    
    public function testCountsAllPositionTypesCorrectly()
    {
        $postData = [
            'Name1' => 'Player One',
            'pg1' => '1',
            'sg1' => '2',
            'sf1' => '3',
            'pf1' => '4',
            'c1' => '5',
            'active1' => '1',
            'min1' => '30',
            'OF1' => '0',
            'DF1' => '0',
            'OI1' => '0',
            'DI1' => '0',
            'BH1' => '0',
            'Injury1' => '0'
        ];
        
        $result = $this->processor->processSubmission($postData, 15);
        
        // Each position should have 1 player
        $this->assertEquals(1, $result['pos_1']);
        $this->assertEquals(1, $result['pos_2']);
        $this->assertEquals(1, $result['pos_3']);
        $this->assertEquals(1, $result['pos_4']);
        $this->assertEquals(1, $result['pos_5']);
    }
}
