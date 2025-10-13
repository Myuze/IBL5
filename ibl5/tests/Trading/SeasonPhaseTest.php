<?php

require_once __DIR__ . '/../bootstrap.php';

/**
 * Test season phase specific behavior in trading
 */
class SeasonPhaseTest extends PHPUnit\Framework\TestCase
{
    private $mockDb;
    private $validator;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->validator = new Trading_TradeValidator($this->mockDb);
    }

    /**
     * Test that cash considerations use cy2 during Playoffs, Draft, and Free Agency
     * @group season-phase
     * @dataProvider playoffPhaseProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('playoffPhaseProvider')]
    public function testCashConsiderationsUseCy2DuringOffseasonPhases($phase)
    {
        // Arrange
        $mockSeason = new MockSeason($this->mockDb);
        $mockSeason->phase = $phase;
        
        // Create validator with mocked season
        $validator = new Trading_TradeValidator($this->mockDb);
        // Override the season property through reflection
        $reflection = new ReflectionClass($validator);
        $seasonProperty = $reflection->getProperty('season');
        $seasonProperty->setAccessible(true);
        $seasonProperty->setValue($validator, $mockSeason);
        
        $userCash = [1 => 100, 2 => 200, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $partnerCash = [1 => 150, 2 => 250, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

        // Act
        $result = $validator->getCurrentSeasonCashConsiderations($userCash, $partnerCash);

        // Assert - Should use cy2 (index 2) during these phases
        $this->assertEquals(200, $result['cashSentToThem'], "Should use cy2 during $phase");
        $this->assertEquals(250, $result['cashSentToMe'], "Should use cy2 during $phase");
    }

    /**
     * Test that cash considerations use cy1 during regular season
     * @group season-phase
     */
    public function testCashConsiderationsUseCy1DuringRegularSeason()
    {
        // Arrange
        $mockSeason = new MockSeason($this->mockDb);
        $mockSeason->phase = "Regular Season";
        
        $validator = new Trading_TradeValidator($this->mockDb);
        $reflection = new ReflectionClass($validator);
        $seasonProperty = $reflection->getProperty('season');
        $seasonProperty->setAccessible(true);
        $seasonProperty->setValue($validator, $mockSeason);
        
        $userCash = [1 => 100, 2 => 200, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $partnerCash = [1 => 150, 2 => 250, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

        // Act
        $result = $validator->getCurrentSeasonCashConsiderations($userCash, $partnerCash);

        // Assert - Should use cy1 (index 1) during regular season
        $this->assertEquals(100, $result['cashSentToThem'], "Should use cy1 during Regular Season");
        $this->assertEquals(150, $result['cashSentToMe'], "Should use cy1 during Regular Season");
    }

    /**
     * Test that trade queries are queued during playoff phases
     * @group season-phase
     * @dataProvider playoffPhaseProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('playoffPhaseProvider')]
    public function testTradeQueriesAreQueuedDuringOffseasonPhases($phase)
    {
        // Arrange
        $mockSeason = new MockSeason($this->mockDb);
        $mockSeason->phase = $phase;
        
        $processor = new Trading_TradeProcessor($this->mockDb);
        $reflection = new ReflectionClass($processor);
        $seasonProperty = $reflection->getProperty('season');
        $seasonProperty->setAccessible(true);
        $seasonProperty->setValue($processor, $mockSeason);
        
        // Set up a mock query tracker
        $this->mockDb->clearQueries();
        
        // Call the queueTradeQuery method via reflection
        $queueMethod = $reflection->getMethod('queueTradeQuery');
        $queueMethod->setAccessible(true);

        // Act
        $queueMethod->invoke($processor, 'UPDATE ibl_plr SET teamname="Test"', 'Test trade line');

        // Assert - Should insert into trade queue during these phases
        $queries = $this->mockDb->getExecutedQueries();
        $hasQueueInsert = false;
        foreach ($queries as $query) {
            if (strpos($query, 'INSERT INTO ibl_trade_queue') !== false) {
                $hasQueueInsert = true;
                break;
            }
        }
        $this->assertTrue($hasQueueInsert, "Trade should be queued during $phase");
    }

    /**
     * Test that trade queries execute immediately during regular season
     * @group season-phase
     */
    public function testTradeQueriesExecuteImmediatelyDuringRegularSeason()
    {
        // Arrange
        $mockSeason = new MockSeason($this->mockDb);
        $mockSeason->phase = "Regular Season";
        
        $processor = new Trading_TradeProcessor($this->mockDb);
        $reflection = new ReflectionClass($processor);
        $seasonProperty = $reflection->getProperty('season');
        $seasonProperty->setAccessible(true);
        $seasonProperty->setValue($processor, $mockSeason);
        
        $this->mockDb->clearQueries();
        
        $queueMethod = $reflection->getMethod('queueTradeQuery');
        $queueMethod->setAccessible(true);

        // Act
        $queueMethod->invoke($processor, 'UPDATE ibl_plr SET teamname="Test"', 'Test trade line');

        // Assert - Should NOT insert into trade queue during regular season
        $queries = $this->mockDb->getExecutedQueries();
        $hasQueueInsert = false;
        foreach ($queries as $query) {
            if (strpos($query, 'INSERT INTO ibl_trade_queue') !== false) {
                $hasQueueInsert = true;
                break;
            }
        }
        $this->assertFalse($hasQueueInsert, "Trade should NOT be queued during Regular Season");
    }

    /**
     * Data provider for playoff/offseason phases
     */
    public static function playoffPhaseProvider()
    {
        return [
            'Playoffs' => ['Playoffs'],
            'Draft' => ['Draft'],
            'Free Agency' => ['Free Agency']
        ];
    }
}

/**
 * Mock Season class for testing
 */
class MockSeason
{
    public $phase;
    public $allowTrades = 'Yes';
    public $allowWaivers = 'Yes';

    public function __construct($db)
    {
        $this->phase = "Regular Season";
    }
}
