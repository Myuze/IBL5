<?php

use PHPUnit\Framework\TestCase;
use Updater\StandingsUpdater;

/**
 * Comprehensive tests for StandingsUpdater class
 * 
 * Tests standings update functionality including:
 * - Extracting wins and losses from records
 * - Assigning groupings for regions
 * - Magic number calculations
 * - Conference and division standings updates
 * - Playoff clinching logic
 */
class StandingsUpdaterTest extends TestCase
{
    private $mockDb;
    private $mockSharedFunctions;
    private $standingsUpdater;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->mockSharedFunctions = new Shared($this->mockDb);
        $this->standingsUpdater = new StandingsUpdater($this->mockDb, $this->mockSharedFunctions);
    }

    protected function tearDown(): void
    {
        $this->standingsUpdater = null;
        $this->mockDb = null;
        $this->mockSharedFunctions = null;
    }

    /**
     * @group standings-updater
     * @group record-parsing
     */
    public function testExtractWinsFromSingleDigitRecord()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('extractWins');
        $method->setAccessible(true);

        $result = $method->invoke($this->standingsUpdater, '5-3');
        
        $this->assertEquals('5', $result);
    }

    /**
     * @group standings-updater
     * @group record-parsing
     */
    public function testExtractWinsFromDoubleDigitRecord()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('extractWins');
        $method->setAccessible(true);

        $result = $method->invoke($this->standingsUpdater, '45-37');
        
        $this->assertEquals('45', $result);
    }

    /**
     * @group standings-updater
     * @group record-parsing
     */
    public function testExtractLossesFromSingleDigitRecord()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('extractLosses');
        $method->setAccessible(true);

        $result = $method->invoke($this->standingsUpdater, '5-3');
        
        $this->assertEquals('3', $result);
    }

    /**
     * @group standings-updater
     * @group record-parsing
     */
    public function testExtractLossesFromDoubleDigitRecord()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('extractLosses');
        $method->setAccessible(true);

        $result = $method->invoke($this->standingsUpdater, '45-37');
        
        $this->assertEquals('37', $result);
    }

    /**
     * @group standings-updater
     * @group record-parsing
     */
    public function testExtractWinsHandlesMixedDigitRecords()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('extractWins');
        $method->setAccessible(true);

        // Test single digit wins with double digit losses
        $result1 = $method->invoke($this->standingsUpdater, '5-37');
        $this->assertEquals('5', $result1);

        // Test double digit wins with single digit losses
        $result2 = $method->invoke($this->standingsUpdater, '45-3');
        $this->assertEquals('45', $result2);
    }

    /**
     * @group standings-updater
     * @group grouping
     */
    public function testAssignGroupingsReturnsArrayWithCorrectStructure()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('assignGroupingsFor');
        $method->setAccessible(true);

        $regions = ['Eastern', 'Western', 'Atlantic', 'Central', 'Midwest', 'Pacific'];
        
        foreach ($regions as $region) {
            $result = $method->invoke($this->standingsUpdater, $region);
            
            $this->assertIsArray($result);
            $this->assertCount(3, $result);
            $this->assertIsString($result[0]); // grouping
            $this->assertIsString($result[1]); // groupingGB
            $this->assertIsString($result[2]); // groupingMagicNumber
        }
    }

    /**
     * @group standings-updater
     * @group grouping
     */
    public function testAssignGroupingsForAllConferences()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('assignGroupingsFor');
        $method->setAccessible(true);

        foreach (League::CONFERENCE_NAMES as $conference) {
            $result = $method->invoke($this->standingsUpdater, $conference);
            $this->assertIsArray($result);
            $this->assertEquals('conference', $result[0]);
            $this->assertEquals('confGB', $result[1]);
            $this->assertEquals('confMagicNumber', $result[2]);
        }
    }

    /**
     * @group standings-updater
     * @group grouping
     */
    public function testAssignGroupingsForAllDivisions()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('assignGroupingsFor');
        $method->setAccessible(true);

        foreach (League::DIVISION_NAMES as $division) {
            $result = $method->invoke($this->standingsUpdater, $division);
            $this->assertIsArray($result);
            $this->assertEquals('division', $result[0]);
            $this->assertEquals('divGB', $result[1]);
            $this->assertEquals('divMagicNumber', $result[2]);
        }
    }

    /**
     * @group standings-updater
     * @group database
     */
    public function testUpdateTruncatesStandingsTable()
    {
        $this->mockDb->setReturnTrue(true);

        // Create a StandingsUpdater with extractStandingsValues stubbed to do nothing
        $this->standingsUpdater = new class($this->mockDb, $this->mockSharedFunctions) extends \Updater\StandingsUpdater {
            protected function extractStandingsValues() {
                // Do nothing
            }
        };

        ob_start();
        $this->standingsUpdater->update();
        ob_end_clean();

        $queries = $this->mockDb->getExecutedQueries();
        $this->assertNotEmpty($queries);
        $this->assertEquals('TRUNCATE TABLE ibl_standings', $queries[0]);
    }

    /**
     * @group standings-updater
     * @group magic-numbers
     */
    public function testMagicNumberCalculationExecutesQuery()
    {
        // Set up mock data for a magic number scenario - must match the SELECT query structure
        $mockStandingsData = [
            [
                'tid' => 1,
                'team_name' => 'Boston Celtics',
                'homeWins' => 25,
                'homeLosses' => 5,
                'awayWins' => 20,
                'awayLosses' => 10,
                'conference' => 'Eastern'
            ],
            [
                'tid' => 2,
                'team_name' => 'Miami Heat',
                'homeWins' => 20,
                'homeLosses' => 10,
                'awayWins' => 15,
                'awayLosses' => 15,
                'conference' => 'Eastern'
            ],
        ];
        
        $this->mockDb->setMockData($mockStandingsData);
        $this->mockDb->setReturnTrue(true);
        $this->mockDb->clearQueries();
        
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('updateMagicNumbers');
        $method->setAccessible(true);

        ob_start();
        $method->invoke($this->standingsUpdater, 'Eastern');
        ob_end_clean();
        
        $queries = $this->mockDb->getExecutedQueries();
        
        // At minimum, should have the SELECT query
        $this->assertNotEmpty($queries);
        $this->assertGreaterThanOrEqual(1, count($queries));
    }

    /**
     * @group standings-updater
     * @group record-parsing
     */
    public function testExtractWinsHandlesZeroWins()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('extractWins');
        $method->setAccessible(true);

        $result = $method->invoke($this->standingsUpdater, '0-82');
        
        $this->assertEquals('0', $result);
    }

    /**
     * @group standings-updater
     * @group record-parsing
     */
    public function testExtractLossesHandlesZeroLosses()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('extractLosses');
        $method->setAccessible(true);

        $result = $method->invoke($this->standingsUpdater, '82-0');
        
        $this->assertEquals('0', $result);
    }

    /**
     * @group standings-updater
     * @group record-parsing
     */
    public function testExtractWinsAndLossesFromPerfectRecord()
    {
        $reflection = new ReflectionClass($this->standingsUpdater);
        $winsMethod = $reflection->getMethod('extractWins');
        $winsMethod->setAccessible(true);
        $lossesMethod = $reflection->getMethod('extractLosses');
        $lossesMethod->setAccessible(true);

        // Perfect 82-0 season
        $wins = $winsMethod->invoke($this->standingsUpdater, '82-0');
        $losses = $lossesMethod->invoke($this->standingsUpdater, '82-0');
        
        $this->assertEquals('82', $wins);
        $this->assertEquals('0', $losses);
    }

    /**
     * @group standings-updater
     * @group database
     */
    public function testUpdateMagicNumbersForAllRegions()
    {
        $this->mockDb->setReturnTrue(true);
        $this->mockDb->setMockData([]);
        
        $reflection = new ReflectionClass($this->standingsUpdater);
        $method = $reflection->getMethod('updateMagicNumbers');
        $method->setAccessible(true);

        $regions = ['Eastern', 'Western', 'Atlantic', 'Central', 'Midwest', 'Pacific'];
        
        foreach ($regions as $region) {
            ob_start();
            $method->invoke($this->standingsUpdater, $region);
            $output = ob_get_clean();
            
            $this->assertStringContainsString($region, $output);
        }
    }
}
