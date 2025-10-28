<?php

use PHPUnit\Framework\TestCase;
use Updater\PowerRankingsUpdater;

/**
 * Comprehensive tests for PowerRankingsUpdater class
 * 
 * Tests power rankings update functionality including:
 * - Month determination for different phases
 * - Team statistics calculation
 * - Win/loss tracking
 * - Streak tracking
 * - Games back calculation
 * - Ranking score calculation
 * - Season and HEAT records updates
 */
class PowerRankingsUpdaterTest extends TestCase
{
    private $mockDb;
    private $mockSeason;
    private $powerRankingsUpdater;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->mockSeason = new Season($this->mockDb);
        $this->powerRankingsUpdater = new PowerRankingsUpdater($this->mockDb, $this->mockSeason);
    }

    protected function tearDown(): void
    {
        $this->powerRankingsUpdater = null;
        $this->mockDb = null;
        $this->mockSeason = null;
    }

    /**
     * @group power-rankings
     * @group month-determination
     */
    public function testDetermineMonthForPreseason()
    {
        $this->mockSeason->phase = 'Preseason';
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('determineMonth');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater);
        
        $this->assertEquals(Season::IBL_PRESEASON_MONTH, $result);
    }

    /**
     * @group power-rankings
     * @group month-determination
     */
    public function testDetermineMonthForHEAT()
    {
        $this->mockSeason->phase = 'HEAT';
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('determineMonth');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater);
        
        $this->assertEquals(Season::IBL_HEAT_MONTH, $result);
    }

    /**
     * @group power-rankings
     * @group month-determination
     */
    public function testDetermineMonthForRegularSeason()
    {
        $this->mockSeason->phase = 'Regular Season';
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('determineMonth');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater);
        
        $this->assertEquals(Season::IBL_REGULAR_SEASON_STARTING_MONTH, $result);
    }

    /**
     * @group power-rankings
     * @group query-building
     */
    public function testBuildGamesQueryForRegularSeason()
    {
        $this->mockSeason->phase = 'Regular Season';
        $this->mockSeason->beginningYear = 2023;
        $this->mockSeason->endingYear = 2024;
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('buildGamesQuery');
        $method->setAccessible(true);

        $tid = 1;
        $month = Season::IBL_REGULAR_SEASON_STARTING_MONTH;
        $result = $method->invoke($this->powerRankingsUpdater, $tid, $month);
        
        $this->assertIsString($result);
        $this->assertStringContainsString('SELECT', $result);
        $this->assertStringContainsString('ibl_schedule', $result);
        $this->assertStringContainsString("Visitor = $tid OR Home = $tid", $result);
        $this->assertStringContainsString('2023', $result);
        $this->assertStringContainsString('2024', $result);
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsInitializesCorrectly()
    {
        // Mock empty result set
        $mockResult = new MockDatabaseResult([]);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 0, 1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('wins', $result);
        $this->assertArrayHasKey('losses', $result);
        $this->assertArrayHasKey('homeWins', $result);
        $this->assertArrayHasKey('homeLosses', $result);
        $this->assertArrayHasKey('awayWins', $result);
        $this->assertArrayHasKey('awayLosses', $result);
        $this->assertArrayHasKey('streak', $result);
        $this->assertArrayHasKey('streakType', $result);
        
        $this->assertEquals(0, $result['wins']);
        $this->assertEquals(0, $result['losses']);
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsWithWinningGame()
    {
        // Mock a single winning game
        $mockGames = [
            ['Visitor' => 1, 'VScore' => 100, 'Home' => 2, 'HScore' => 95]
        ];
        $mockResult = new MockDatabaseResult($mockGames);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 1, 1);
        
        $this->assertEquals(1, $result['wins']);
        $this->assertEquals(0, $result['losses']);
        $this->assertEquals(0, $result['homeWins']);
        $this->assertEquals(1, $result['awayWins']);
        $this->assertEquals('W', $result['streakType']);
        $this->assertEquals(1, $result['streak']);
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsWithLosingGame()
    {
        // Mock a single losing game
        $mockGames = [
            ['Visitor' => 1, 'VScore' => 85, 'Home' => 2, 'HScore' => 95]
        ];
        $mockResult = new MockDatabaseResult($mockGames);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 1, 1);
        
        $this->assertEquals(0, $result['wins']);
        $this->assertEquals(1, $result['losses']);
        $this->assertEquals(0, $result['homeLosses']);
        $this->assertEquals(1, $result['awayLosses']);
        $this->assertEquals('L', $result['streakType']);
        $this->assertEquals(1, $result['streak']);
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsWithHomeGame()
    {
        // Mock a home winning game
        $mockGames = [
            ['Visitor' => 2, 'VScore' => 90, 'Home' => 1, 'HScore' => 100]
        ];
        $mockResult = new MockDatabaseResult($mockGames);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 1, 1);
        
        $this->assertEquals(1, $result['wins']);
        $this->assertEquals(1, $result['homeWins']);
        $this->assertEquals(0, $result['awayWins']);
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsTracksWinningStreak()
    {
        // Mock multiple winning games
        $mockGames = [
            ['Visitor' => 1, 'VScore' => 100, 'Home' => 2, 'HScore' => 95],
            ['Visitor' => 3, 'VScore' => 85, 'Home' => 1, 'HScore' => 90],
            ['Visitor' => 1, 'VScore' => 105, 'Home' => 4, 'HScore' => 100],
        ];
        $mockResult = new MockDatabaseResult($mockGames);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 3, 1);
        
        $this->assertEquals(3, $result['wins']);
        $this->assertEquals('W', $result['streakType']);
        $this->assertEquals(3, $result['streak']);
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsTracksLosingStreak()
    {
        // Mock multiple losing games
        $mockGames = [
            ['Visitor' => 1, 'VScore' => 80, 'Home' => 2, 'HScore' => 95],
            ['Visitor' => 3, 'VScore' => 100, 'Home' => 1, 'HScore' => 90],
        ];
        $mockResult = new MockDatabaseResult($mockGames);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 2, 1);
        
        $this->assertEquals(0, $result['wins']);
        $this->assertEquals(2, $result['losses']);
        $this->assertEquals('L', $result['streakType']);
        $this->assertEquals(2, $result['streak']);
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsHandlesStreakChange()
    {
        // Mock games with win then loss (streak resets)
        $mockGames = [
            ['Visitor' => 1, 'VScore' => 100, 'Home' => 2, 'HScore' => 95],
            ['Visitor' => 3, 'VScore' => 100, 'Home' => 1, 'HScore' => 90],
        ];
        $mockResult = new MockDatabaseResult($mockGames);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 2, 1);
        
        $this->assertEquals(1, $result['wins']);
        $this->assertEquals(1, $result['losses']);
        $this->assertEquals('L', $result['streakType']);
        $this->assertEquals(1, $result['streak']); // Streak resets to 1
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsIgnoresTies()
    {
        // Mock a tie game (same score)
        $mockGames = [
            ['Visitor' => 1, 'VScore' => 95, 'Home' => 2, 'HScore' => 95]
        ];
        $mockResult = new MockDatabaseResult($mockGames);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 1, 1);
        
        // Tie should not count as win or loss
        $this->assertEquals(0, $result['wins']);
        $this->assertEquals(0, $result['losses']);
    }

    /**
     * @group power-rankings
     * @group database
     */
    public function testUpdateResetsDepthChartStatus()
    {
        // Set up minimal mock data
        $mockTeams = [
            ['TeamID' => 1, 'Team' => 'Boston Celtics', 'streak_type' => 'W', 'streak' => 2]
        ];
        $this->mockDb->setMockData($mockTeams);
        $this->mockDb->setReturnTrue(true);
        
        ob_start();
        try {
            $this->powerRankingsUpdater->update();
        } catch (Exception $e) {
            // May fail on subsequent queries, but we check the depth chart reset
        }
        ob_end_clean();
        
        $queries = $this->mockDb->getExecutedQueries();
        $depthChartResetQuery = array_filter($queries, function($q) {
            return stripos($q, 'sim_depth') !== false;
        });
        
        $this->assertNotEmpty($depthChartResetQuery);
    }

    /**
     * @group power-rankings
     * @group stats-calculation
     */
    public function testCalculateTeamStatsTracksLast10Games()
    {
        // Mock 15 games where team wins last 7
        $mockGames = [];
        for ($i = 0; $i < 15; $i++) {
            if ($i < 8) {
                // First 8 games are losses
                $mockGames[] = ['Visitor' => 1, 'VScore' => 80, 'Home' => 2, 'HScore' => 90];
            } else {
                // Last 7 games are wins
                $mockGames[] = ['Visitor' => 1, 'VScore' => 100, 'Home' => 2, 'HScore' => 90];
            }
        }
        $mockResult = new MockDatabaseResult($mockGames);
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('calculateTeamStats');
        $method->setAccessible(true);

        $result = $method->invoke($this->powerRankingsUpdater, $mockResult, 15, 1);
        
        // Games 6-15 are the last 10, so: games 6-8 are losses (3), games 9-15 are wins (7)
        $this->assertEquals(7, $result['wins']);
        $this->assertEquals(8, $result['losses']);
        $this->assertEquals(7, $result['winsInLast10Games']);
        $this->assertEquals(3, $result['lossesInLast10Games']);
    }

    /**
     * @group power-rankings
     * @group ranking-calculation
     */
    public function testRankingScoreCalculation()
    {
        // Create stats with specific win/loss points
        $stats = [
            'wins' => 40,
            'losses' => 20,
            'homeWins' => 25,
            'homeLosses' => 5,
            'awayWins' => 15,
            'awayLosses' => 15,
            'winPoints' => 800,  // Will be added to wins
            'lossPoints' => 400, // Will be added to losses
            'winsInLast10Games' => 7,
            'lossesInLast10Games' => 3,
            'streak' => 3,
            'streakType' => 'W'
        ];
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('updateTeamStats');
        $method->setAccessible(true);

        // Mock the database operations
        $this->mockDb->setReturnTrue(true);
        
        ob_start();
        $result = $method->invoke($this->powerRankingsUpdater, 1, 'Boston Celtics', $stats);
        ob_end_clean();
        
        // Verify that update query was executed
        $queries = $this->mockDb->getExecutedQueries();
        $updateQueries = array_filter($queries, function($q) {
            return stripos($q, 'UPDATE ibl_power SET') !== false;
        });
        
        $this->assertNotEmpty($updateQueries);
    }

    /**
     * @group power-rankings
     * @group heat-season
     */
    public function testHEATSeasonRecordsUpdate()
    {
        $this->mockSeason->phase = 'HEAT';
        $this->mockSeason->beginningYear = 2023;
        
        $stats = [
            'wins' => 10,
            'losses' => 5,
            'homeWins' => 6,
            'homeLosses' => 2,
            'awayWins' => 4,
            'awayLosses' => 3,
            'winPoints' => 150,
            'lossPoints' => 75,
            'winsInLast10Games' => 7,
            'lossesInLast10Games' => 3,
            'streak' => 2,
            'streakType' => 'W'
        ];
        
        $reflection = new ReflectionClass($this->powerRankingsUpdater);
        $method = $reflection->getMethod('updateTeamStats');
        $method->setAccessible(true);

        $this->mockDb->setReturnTrue(true);
        
        ob_start();
        $method->invoke($this->powerRankingsUpdater, 1, 'Boston Celtics', $stats);
        ob_end_clean();
        
        $queries = $this->mockDb->getExecutedQueries();
        $heatQueries = array_filter($queries, function($q) {
            return stripos($q, 'ibl_heat_win_loss') !== false;
        });
        
        $this->assertNotEmpty($heatQueries);
    }
}
