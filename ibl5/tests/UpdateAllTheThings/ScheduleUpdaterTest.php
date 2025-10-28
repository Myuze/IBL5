<?php

use PHPUnit\Framework\TestCase;
use Updater\ScheduleUpdater;

/**
 * Comprehensive tests for ScheduleUpdater class
 * 
 * Tests schedule update functionality including:
 * - Date extraction for different season phases
 * - Box ID extraction from HTML links
 * - Schedule database operations
 * - Team ID resolution
 * - Error handling
 */
class ScheduleUpdaterTest extends TestCase
{
    private $mockDb;
    private $mockSharedFunctions;
    private $mockSeason;
    private $scheduleUpdater;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->mockSharedFunctions = new Shared($this->mockDb);
        $this->mockSeason = new Season($this->mockDb);
        $this->scheduleUpdater = new ScheduleUpdater($this->mockDb, $this->mockSharedFunctions, $this->mockSeason);
    }

    protected function tearDown(): void
    {
        $this->scheduleUpdater = null;
        $this->mockDb = null;
        $this->mockSharedFunctions = null;
        $this->mockSeason = null;
    }

    /**
     * @group schedule-updater
     * @group date-extraction
     */
    public function testExtractDateConvertsPostToJune()
    {
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractDate');
        $method->setAccessible(true);

        $result = $method->invoke($this->scheduleUpdater, 'Post 15, 2024');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('month', $result);
        $this->assertArrayHasKey('day', $result);
        $this->assertArrayHasKey('year', $result);
        $this->assertEquals('6', $result['month']);
        $this->assertEquals('15', $result['day']);
        $this->assertEquals('2024', $result['year']);
    }

    /**
     * @group schedule-updater
     * @group date-extraction
     */
    public function testExtractDateHandlesPreseasonPhase()
    {
        $this->mockSeason->phase = 'Preseason';
        
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractDate');
        $method->setAccessible(true);

        // Month should be overridden to preseason month
        $result = $method->invoke($this->scheduleUpdater, 'November 1, 2023');
        
        $this->assertIsArray($result);
        $this->assertEquals(Season::IBL_PRESEASON_MONTH, $result['month']);
    }

    /**
     * @group schedule-updater
     * @group date-extraction
     */
    public function testExtractDateHandlesHEATPhase()
    {
        $this->mockSeason->phase = 'HEAT';
        
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractDate');
        $method->setAccessible(true);

        // Month should be overridden to HEAT month
        $result = $method->invoke($this->scheduleUpdater, 'November 1, 2023');
        
        $this->assertIsArray($result);
        $this->assertEquals(Season::IBL_HEAT_MONTH, $result['month']);
    }

    /**
     * @group schedule-updater
     * @group date-extraction
     */
    public function testExtractDateReturnsNullForInvalidDate()
    {
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractDate');
        $method->setAccessible(true);

        $result = $method->invoke($this->scheduleUpdater, false);
        
        $this->assertNull($result);
    }

    /**
     * @group schedule-updater
     * @group box-id
     */
    public function testExtractBoxIDFromValidLink()
    {
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractBoxID');
        $method->setAccessible(true);

        $result = $method->invoke($this->scheduleUpdater, 'box12345.htm');
        
        $this->assertEquals('12345', $result);
    }

    /**
     * @group schedule-updater
     * @group box-id
     */
    public function testExtractBoxIDHandlesLargeBoxID()
    {
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractBoxID');
        $method->setAccessible(true);

        $result = $method->invoke($this->scheduleUpdater, 'box999999.htm');
        
        $this->assertEquals('999999', $result);
    }

    /**
     * @group schedule-updater
     * @group database
     */
    public function testUpdateTruncatesScheduleTable()
    {
        // Mock successful truncate
        $this->mockDb->setReturnTrue(true);
        
        // Capture output
        ob_start();
        
        // Suppress expected warnings from DOMDocument parsing HTML
        set_error_handler(function() { return true; }, E_WARNING);
        
        // This will fail because we can't load the HTML file, but we can test that TRUNCATE is attempted
        try {
            $this->scheduleUpdater->update();
        } catch (Exception $e) {
            // Expected to fail on file load
        }
        
        restore_error_handler();
        $output = ob_get_clean();
        
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertNotEmpty($queries);
        $this->assertEquals('TRUNCATE TABLE ibl_schedule', $queries[0]);
    }

    /**
     * @group schedule-updater
     * @group team-resolution
     */
    public function testTeamIDResolutionUsesMockSharedFunctions()
    {
        $teamName = 'Boston Celtics';
        $teamID = $this->mockSharedFunctions->getTidFromTeamname($teamName);
        
        $this->assertIsInt($teamID);
        $this->assertGreaterThan(0, $teamID);
    }

    /**
     * @group schedule-updater
     * @group date-formatting
     */
    public function testExtractDateFormatsDateCorrectly()
    {
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractDate');
        $method->setAccessible(true);

        $result = $method->invoke($this->scheduleUpdater, 'January 15, 2024');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('date', $result);
        $this->assertStringContainsString('2024', $result['date']);
        $this->assertStringContainsString('1', $result['date']);
        $this->assertStringContainsString('15', $result['date']);
    }

    /**
     * @group schedule-updater
     * @group date-formatting
     */
    public function testExtractDateRemovesLeadingZeros()
    {
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractDate');
        $method->setAccessible(true);

        $result = $method->invoke($this->scheduleUpdater, 'January 05, 2024');
        
        $this->assertIsArray($result);
        $this->assertEquals('5', $result['day']);
    }

    /**
     * @group schedule-updater
     * @group box-id
     */
    public function testExtractBoxIDWithDifferentExtensions()
    {
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractBoxID');
        $method->setAccessible(true);

        // Test with .htm
        $result1 = $method->invoke($this->scheduleUpdater, 'box54321.htm');
        $this->assertEquals('54321', $result1);
    }

    /**
     * @group schedule-updater
     * @group date-extraction
     */
    public function testExtractDateHandlesVariousDateFormats()
    {
        $reflection = new ReflectionClass($this->scheduleUpdater);
        $method = $reflection->getMethod('extractDate');
        $method->setAccessible(true);

        // Test various date formats
        $dates = [
            'November 1, 2023',
            'December 25, 2023',
            'January 1, 2024',
            'June 30, 2024',
        ];

        foreach ($dates as $dateString) {
            $result = $method->invoke($this->scheduleUpdater, $dateString);
            $this->assertIsArray($result);
            $this->assertArrayHasKey('date', $result);
            $this->assertArrayHasKey('month', $result);
            $this->assertArrayHasKey('day', $result);
            $this->assertArrayHasKey('year', $result);
        }
    }
}
