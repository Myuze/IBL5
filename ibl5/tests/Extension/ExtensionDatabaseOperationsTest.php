<?php

use PHPUnit\Framework\TestCase;
use Extension\ExtensionDatabaseOperations;

/**
 * Tests for contract extension database operations
 * 
 * Tests all database interactions including:
 * - Player contract updates
 * - Team extension usage flags
 * - News story creation
 * - Discord notifications (mocked)
 * - Email notifications (mocked)
 */
class ExtensionDatabaseOperationsTest extends TestCase
{
    private $mockDb;
    private $extensionDbOps;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->extensionDbOps = new ExtensionDatabaseOperations($this->mockDb);
    }

    protected function tearDown(): void
    {
        $this->extensionDbOps = null;
        $this->mockDb = null;
    }

    /**
     * @group database
     * @group player-update
     */
    public function testUpdatesPlayerContractOnAcceptedExtension()
    {
        // Arrange
        $playerName = 'Test Player';
        $offer = [
            'year1' => 1000,
            'year2' => 1100,
            'year3' => 1200,
            'year4' => 1300,
            'year5' => 1400
        ];
        $currentSalary = 800;

        // Act
        $result = $this->extensionDbOps->updatePlayerContract($playerName, $offer, $currentSalary);

        // Assert
        $this->assertTrue($result);
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertCount(1, $queries);
        
        // Verify the UPDATE query contains correct values
        $updateQuery = $queries[0];
        $this->assertStringContainsString('UPDATE ibl_plr', $updateQuery);
        $this->assertStringContainsString('cy = 1', $updateQuery); // Reset to year 1
        $this->assertStringContainsString('cyt = 6', $updateQuery); // Total years (1 current + 5 extension)
        $this->assertStringContainsString("cy1 = $currentSalary", $updateQuery);
        $this->assertStringContainsString('cy2 = 1000', $updateQuery);
        $this->assertStringContainsString('cy3 = 1100', $updateQuery);
        $this->assertStringContainsString('cy4 = 1200', $updateQuery);
        $this->assertStringContainsString('cy5 = 1300', $updateQuery);
        $this->assertStringContainsString('cy6 = 1400', $updateQuery);
        $this->assertStringContainsString("WHERE name = '$playerName'", $updateQuery);
    }

    /**
     * @group database
     * @group player-update
     */
    public function testUpdatesPlayerContractWith3YearExtension()
    {
        // Arrange
        $playerName = 'Test Player';
        $offer = [
            'year1' => 1000,
            'year2' => 1100,
            'year3' => 1200,
            'year4' => 0,
            'year5' => 0
        ];
        $currentSalary = 800;

        // Act
        $result = $this->extensionDbOps->updatePlayerContract($playerName, $offer, $currentSalary);

        // Assert
        $this->assertTrue($result);
        $queries = $this->mockDb->getExecutedQueries();
        $updateQuery = $queries[0];
        
        // Verify years 4 and 5 are set to 0
        $this->assertStringContainsString('cyt = 4', $updateQuery); // 1 current + 3 extension
        $this->assertStringContainsString('cy5 = 0', $updateQuery);
        $this->assertStringContainsString('cy6 = 0', $updateQuery);
    }

    /**
     * @group database
     * @group team-flags
     */
    public function testMarksExtensionUsedThisSim()
    {
        // Arrange
        $teamName = 'Test Team';

        // Act
        $result = $this->extensionDbOps->markExtensionUsedThisSim($teamName);

        // Assert
        $this->assertTrue($result);
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertCount(1, $queries);
        
        $updateQuery = $queries[0];
        $this->assertStringContainsString('UPDATE ibl_team_info', $updateQuery);
        $this->assertStringContainsString('Used_Extension_This_Chunk = 1', $updateQuery);
        $this->assertStringContainsString("WHERE team_name = '$teamName'", $updateQuery);
    }

    /**
     * @group database
     * @group team-flags
     */
    public function testMarksExtensionUsedThisSeason()
    {
        // Arrange
        $teamName = 'Test Team';

        // Act
        $result = $this->extensionDbOps->markExtensionUsedThisSeason($teamName);

        // Assert
        $this->assertTrue($result);
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertCount(1, $queries);
        
        $updateQuery = $queries[0];
        $this->assertStringContainsString('UPDATE ibl_team_info', $updateQuery);
        $this->assertStringContainsString('Used_Extension_This_Season = 1', $updateQuery);
        $this->assertStringContainsString("WHERE team_name = '$teamName'", $updateQuery);
    }

    /**
     * @group database
     * @group news-story
     */
    public function testCreatesNewsStoryForAcceptedExtension()
    {
        // Arrange
        $playerName = 'Test Player';
        $teamName = 'Test Team';
        $offerInMillions = 120;
        $offerYears = 5;
        $offerDetails = '1000 1100 1200 1300 1400';
        
        // Mock the category and topic queries
        $this->mockDb->setMockData([
            ['catid' => 1, 'counter' => 10], // category data
            ['topicid' => 5] // topic data
        ]);

        // Act
        $result = $this->extensionDbOps->createAcceptedExtensionStory(
            $playerName,
            $teamName,
            $offerInMillions,
            $offerYears,
            $offerDetails
        );

        // Assert
        $this->assertTrue($result);
        $queries = $this->mockDb->getExecutedQueries();
        
        // Should have multiple queries: SELECT category, INSERT story
        $this->assertGreaterThanOrEqual(2, count($queries));
        
        // Check for SELECT category query
        $foundSelect = false;
        foreach ($queries as $query) {
            if (strpos($query, 'SELECT catid, counter FROM nuke_stories_cat') !== false) {
                $foundSelect = true;
                $this->assertStringContainsString('Contract Extensions', $query);
            }
        }
        $this->assertTrue($foundSelect, 'Should have queried for Contract Extensions category');
        
        // Check for INSERT INTO nuke_stories
        $foundInsert = false;
        foreach ($queries as $query) {
            if (strpos($query, 'INSERT INTO nuke_stories') !== false) {
                $foundInsert = true;
                $this->assertStringContainsString($playerName, $query);
                $this->assertStringContainsString($teamName, $query);
                $this->assertStringContainsString('accepted', $query);
            }
        }
        $this->assertTrue($foundInsert, 'Should have created news story INSERT query');
    }

    /**
     * @group database
     * @group news-story
     */
    public function testCreatesNewsStoryForRejectedExtension()
    {
        // Arrange
        $playerName = 'Test Player';
        $teamName = 'Test Team';
        $offerInMillions = 100;
        $offerYears = 5;
        
        // Mock the category and topic queries
        $this->mockDb->setMockData([
            ['catid' => 1, 'counter' => 10],
            ['topicid' => 5]
        ]);

        // Act
        $result = $this->extensionDbOps->createRejectedExtensionStory(
            $playerName,
            $teamName,
            $offerInMillions,
            $offerYears
        );

        // Assert
        $this->assertTrue($result);
        $queries = $this->mockDb->getExecutedQueries();
        
        // Check for INSERT INTO nuke_stories
        $foundInsert = false;
        foreach ($queries as $query) {
            if (strpos($query, 'INSERT INTO nuke_stories') !== false) {
                $foundInsert = true;
                $this->assertStringContainsString($playerName, $query);
                $this->assertStringContainsString($teamName, $query);
                $this->assertStringContainsString('rejected', $query);
            }
        }
        $this->assertTrue($foundInsert, 'Should have created news story INSERT query');
    }

    /**
     * @group database
     * @group counter
     */
    public function testIncrementsContractExtensionsCounter()
    {
        // Arrange
        $this->mockDb->setMockData([
            ['catid' => 1, 'counter' => 10]
        ]);

        // Act
        $result = $this->extensionDbOps->incrementExtensionsCounter();

        // Assert
        $this->assertTrue($result);
        $queries = $this->mockDb->getExecutedQueries();
        
        // Should find UPDATE query for counter
        $foundUpdate = false;
        foreach ($queries as $query) {
            if (strpos($query, 'UPDATE nuke_stories_cat') !== false) {
                $foundUpdate = true;
                $this->assertStringContainsString('counter =', $query);
                $this->assertStringContainsString('Contract Extensions', $query);
            }
        }
        $this->assertTrue($foundUpdate, 'Should have incremented extensions counter');
    }

    /**
     * @group database
     * @group retrieval
     */
    public function testRetrievesPlayerPreferences()
    {
        // Arrange
        $playerName = 'Test Player';
        $this->mockDb->setMockData([
            [
                'name' => 'Test Player',
                'teamname' => 'Test Team',
                'winner' => 4,
                'tradition' => 3,
                'coach' => 4,
                'security' => 3,
                'loyalty' => 5,
                'playingTime' => 4
            ]
        ]);

        // Act
        $result = $this->extensionDbOps->getPlayerPreferences($playerName);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('Test Player', $result['name']);
        $this->assertEquals(4, $result['winner']);
        $this->assertEquals(3, $result['tradition']);
        $this->assertEquals(5, $result['loyalty']);
        $this->assertEquals(4, $result['playingTime']);
    }

    /**
     * @group database
     * @group retrieval
     */
    public function testRetrievesPlayerCurrentContract()
    {
        // Arrange
        $playerName = 'Test Player';
        $this->mockDb->setMockData([
            [
                'cy' => 2,
                'cy1' => 800,
                'cy2' => 900,
                'cy3' => 1000,
                'cy4' => 0,
                'cy5' => 0,
                'cy6' => 0
            ]
        ]);

        // Act
        $result = $this->extensionDbOps->getPlayerCurrentContract($playerName);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(2, $result['cy']);
        $this->assertEquals(900, $result['currentSalary']); // cy2 since cy = 2
    }

    /**
     * @group database
     * @group batch-operations
     */
    public function testPerformsCompleteAcceptedExtensionWorkflow()
    {
        // Arrange
        $playerName = 'Test Player';
        $teamName = 'Test Team';
        $offer = [
            'year1' => 1000,
            'year2' => 1100,
            'year3' => 1200,
            'year4' => 0,
            'year5' => 0
        ];
        $currentSalary = 800;
        
        $this->mockDb->setMockData([
            ['catid' => 1, 'counter' => 10],
            ['topicid' => 5]
        ]);

        // Act
        $result = $this->extensionDbOps->processAcceptedExtension(
            $playerName,
            $teamName,
            $offer,
            $currentSalary
        );

        // Assert
        $this->assertTrue($result['success']);
        $queries = $this->mockDb->getExecutedQueries();
        
        // Should have multiple queries:
        // 1. Update player contract
        // 2. Mark extension used this season
        // 3. Get category info
        // 4. Update counter
        // 5. Insert news story
        $this->assertGreaterThanOrEqual(3, count($queries));
        
        // Verify player contract was updated
        $hasPlayerUpdate = false;
        foreach ($queries as $query) {
            if (strpos($query, 'UPDATE ibl_plr') !== false) {
                $hasPlayerUpdate = true;
            }
        }
        $this->assertTrue($hasPlayerUpdate, 'Should have updated player contract');
    }

    /**
     * @group database
     * @group batch-operations
     */
    public function testPerformsCompleteRejectedExtensionWorkflow()
    {
        // Arrange
        $playerName = 'Test Player';
        $teamName = 'Test Team';
        $offer = [
            'year1' => 800,
            'year2' => 850,
            'year3' => 900,
            'year4' => 0,
            'year5' => 0
        ];
        
        $this->mockDb->setMockData([
            ['catid' => 1, 'counter' => 10],
            ['topicid' => 5]
        ]);

        // Act
        $result = $this->extensionDbOps->processRejectedExtension(
            $playerName,
            $teamName,
            $offer
        );

        // Assert
        $this->assertTrue($result['success']);
        $queries = $this->mockDb->getExecutedQueries();
        
        // Should NOT update player contract or mark extension used for season
        // Should only create news story and NOT mark extension used this sim (already done)
        $hasPlayerUpdate = false;
        $hasSeasonFlag = false;
        foreach ($queries as $query) {
            if (strpos($query, 'UPDATE ibl_plr') !== false && strpos($query, 'cy =') !== false) {
                $hasPlayerUpdate = true;
            }
            if (strpos($query, 'Used_Extension_This_Season = 1') !== false) {
                $hasSeasonFlag = true;
            }
        }
        
        $this->assertFalse($hasPlayerUpdate, 'Should NOT update player contract on rejection');
        $this->assertFalse($hasSeasonFlag, 'Should NOT mark season extension used on rejection');
    }
}
