<?php

use PHPUnit\Framework\TestCase;
use Extension\ExtensionProcessor;

/**
 * Integration tests for complete contract extension workflows
 * 
 * Tests end-to-end scenarios combining validation, evaluation, and database operations:
 * - Successful extension scenarios
 * - Failed extension scenarios
 * - Edge cases and special conditions
 */
class ExtensionIntegrationTest extends TestCase
{
    private $mockDb;
    private $extensionProcessor;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->extensionProcessor = new ExtensionProcessor($this->mockDb);
    }

    protected function tearDown(): void
    {
        $this->extensionProcessor = null;
        $this->mockDb = null;
    }

    /**
     * @group integration
     * @group success-scenarios
     */
    public function testCompleteSuccessfulExtensionWorkflow()
    {
        // Arrange - Setup complete player and team data
        $this->setupSuccessfulExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Test Player',
            'offer' => [
                'year1' => 1000,
                'year2' => 1100,
                'year3' => 1200,
                'year4' => 1300,
                'year5' => 1400
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertTrue($result['accepted']);
        $this->assertStringContainsString('accept', $result['message']);
        
        // Verify database operations were performed
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertGreaterThan(0, count($queries));
        
        $allQueries = implode(' ', $queries);
        
        // Verify sim flag was set
        $this->assertStringContainsString('Used_Extension_This_Chunk = 1', $allQueries);
        
        // Verify season flag was set (for accepted extension)
        $this->assertStringContainsString('Used_Extension_This_Season = 1', $allQueries);
        
        // Verify player contract was updated
        $this->assertStringContainsString('UPDATE ibl_plr', $allQueries);
    }

    /**
     * @group integration
     * @group rejection-scenarios
     */
    public function testCompleteRejectedExtensionWorkflow()
    {
        // Arrange - Setup player who will reject
        $this->setupRejectedExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Seattle SuperSonics',
            'playerID' => 1, // 'playerName' => 'Demanding Player',
            'offer' => [
                'year1' => 800,
                'year2' => 850,
                'year3' => 900,
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertTrue($result['success']); // Extension attempt was legal
        $this->assertFalse($result['accepted']); // But offer was rejected
        $this->assertStringContainsString('refuse', $result['message']);
        
        // Verify sim flag was set but NOT season flag
        $queries = $this->mockDb->getExecutedQueries();
        $allQueries = implode(' ', $queries);
        $this->assertStringContainsString('Used_Extension_This_Chunk = 1', $allQueries);
        $this->assertStringNotContainsString('Used_Extension_This_Season = 1', $allQueries);
    }

    /**
     * @group integration
     * @group validation-failures
     */
    public function testRejectsExtensionWithZeroAmountInYear1()
    {
        // Arrange
        $this->setupBasicExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Test Player',
            'offer' => [
                'year1' => 0, // Invalid!
                'year2' => 1000,
                'year3' => 1100,
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Year1', $result['error']);
        $this->assertStringContainsString('zero', $result['error']);
        
        // Verify NO database changes were made
        $queries = $this->mockDb->getExecutedQueries();
        $allQueries = implode(' ', $queries);
        $this->assertStringNotContainsString('Used_Extension_This_Chunk', $allQueries);
    }

    /**
     * @group integration
     * @group validation-failures
     */
    public function testRejectsExtensionWhenAlreadyUsedThisSeason()
    {
        // Arrange
        $this->setupAlreadyExtendedScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Test Player',
            'offer' => [
                'year1' => 1000,
                'year2' => 1100,
                'year3' => 1200,
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('already used your extension for this season', $result['error']);
        
        // Verify only eligibility check query was made, no extension processing
        $queries = $this->mockDb->getExecutedQueries();
        // Should have at least one query for eligibility check
        $this->assertGreaterThanOrEqual(1, count($queries));
        // But should NOT have marked sim as used or updated contract
        $allQueries = implode(' ', $queries);
        $this->assertStringNotContainsString('UPDATE ibl_plr', $allQueries);
    }

    /**
     * @group integration
     * @group validation-failures
     */
    public function testRejectsExtensionWithExcessiveRaise()
    {
        // Arrange
        $this->setupBasicExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Test Player',
            'offer' => [
                'year1' => 1000,
                'year2' => 1200, // 20% raise, but max is 10% without Bird rights
                'year3' => 1400,
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('larger raise than is permitted', $result['error']);
    }

    /**
     * @group integration
     * @group validation-failures
     */
    public function testRejectsExtensionOverMaximumSalary()
    {
        // Arrange
        $this->setupBasicExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Young Player', // Only 3 years experience
            'offer' => [
                'year1' => 1500, // Over max of 1063 for 0-6 years exp
                'year2' => 1600,
                'year3' => 1700,
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('over the maximum allowed', $result['error']);
    }

    /**
     * @group integration
     * @group bird-rights
     */
    public function testAllowsHigherRaisesWithBirdRights()
    {
        // Arrange - Player with Bird rights
        $this->setupBirdRightsExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Veteran Player',
            'offer' => [
                'year1' => 1000,
                'year2' => 1125, // 12.5% raise allowed with Bird rights
                'year3' => 1250, // Another ~11% raise
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertTrue($result['accepted']);
    }

    /**
     * @group integration
     * @group player-preferences
     */
    public function testPlayerWithHighLoyaltyAcceptsLowerOffer()
    {
        // Arrange - Player with high loyalty to current team
        $this->setupHighLoyaltyPlayerScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Loyal Player',
            'offer' => [
                'year1' => 900,
                'year2' => 950,
                'year3' => 1000,
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertTrue($result['accepted']);
        $this->assertArrayHasKey('modifier', $result);
        $this->assertGreaterThan(1.0, $result['modifier']); // Loyalty increases modifier
    }

    /**
     * @group integration
     * @group player-preferences
     */
    public function testPlayerRejectsOfferDueToLackOfPlayingTime()
    {
        // Arrange - Player values playing time, team has lots of money at position
        $this->setupPlayingTimeScenario();
        
        $extensionData = [
            'teamName' => 'Stacked Team',
            'playerID' => 1, // 'playerName' => 'Rotation Player',
            'offer' => [
                'year1' => 950,  // Under max of 1063 for 4 years exp
                'year2' => 1000, // Small raises
                'year3' => 1050,
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertFalse($result['accepted']); // Rejects due to playing time concerns
    }

    /**
     * @group integration
     * @group edge-cases
     */
    public function testHandles3YearMinimumExtension()
    {
        // Arrange
        $this->setupSuccessfulExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Test Player',
            'offer' => [
                'year1' => 1000,
                'year2' => 1100,
                'year3' => 1200,
                'year4' => 0, // 3-year extension
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['extensionYears']);
    }

    /**
     * @group integration
     * @group edge-cases
     */
    public function testHandles5YearMaximumExtension()
    {
        // Arrange
        $this->setupSuccessfulExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Test Player',
            'offer' => [
                'year1' => 1000,
                'year2' => 1100,
                'year3' => 1200,
                'year4' => 1300,
                'year5' => 1400 // 5-year extension
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals(5, $result['extensionYears']);
    }

    /**
     * @group integration
     * @group notifications
     */
    public function testSendsDiscordNotificationOnAcceptedExtension()
    {
        // Arrange
        $this->setupSuccessfulExtensionScenario();
        
        $extensionData = [
            'teamName' => 'Miami Cyclones',
            'playerID' => 1, // 'playerName' => 'Test Player',
            'offer' => [
                'year1' => 1000,
                'year2' => 1100,
                'year3' => 1200,
                'year4' => 0,
                'year5' => 0
            ]
        ];

        // Act
        $result = $this->extensionProcessor->processExtension($extensionData);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertTrue($result['discordNotificationSent']);
        $this->assertStringContainsString('#extensions', $result['discordChannel']);
    }

    // ==== HELPER METHODS TO SET UP TEST SCENARIOS ====

    private function setupSuccessfulExtensionScenario()
    {
        // Combined data that works for all queries in the scenario
        $this->mockDb->setMockData([
            array_merge($this->getBasePlayerData(), [
                // Team info fields
                'Used_Extension_This_Season' => 0,
                'Used_Extension_This_Chunk' => 0,
                'Contract_Wins' => 50,
                'Contract_Losses' => 32,
                'Contract_AvgW' => 2500,
                'Contract_AvgL' => 2000,
                'money_committed_at_position' => 0,
                // Player info fields (scenario-specific overrides)
                'name' => 'Test Player',
                'nickname' => 'Tester',
                'teamname' => 'Miami Cyclones',
                // Free agency preferences
                'winner' => 3,
                'tradition' => 3,
                'loyalty' => 3,
                'playingTime' => 3,
                'security' => 3,
                // Contract details
                'exp' => 5,
                'bird' => 2,
                'cy' => 1,
                'cyt' => 1,
                'cy1' => 800,
                'cy2' => 0,
                'cy3' => 0,
                'cy4' => 0,
                'cy5' => 0,
                'cy6' => 0,
                // News story fields
                'catid' => 1,
                'counter' => 10,
                'topicid' => 5
            ])
        ]);
    }

    private function setupRejectedExtensionScenario()
    {
        // Combined data that works for all queries in the scenario
        $this->mockDb->setMockData([
            array_merge($this->getBasePlayerData(), [
                // Team info fields - losing team
                'Used_Extension_This_Season' => 0,
                'Used_Extension_This_Chunk' => 0,
                'Contract_Wins' => 25,
                'Contract_Losses' => 57,
                'Contract_AvgW' => 1500,
                'Contract_AvgL' => 3500,
                'money_committed_at_position' => 0,
                // Player info fields - high demands (scenario-specific overrides)
                'pid' => 2,
                'ordinal' => 2,
                'name' => 'Demanding Player',
                'nickname' => 'Diva',
                'age' => 28,
                'tid' => 2,
                'teamname' => 'Seattle SuperSonics',
                'pos' => 'SG',
                // Ratings (higher than base)
                'r_fga' => 60,
                'r_fgp' => 60,
                'r_fta' => 60,
                'r_ftp' => 60,
                'r_tga' => 60,
                'r_tgp' => 60,
                'r_orb' => 60,
                'r_drb' => 60,
                'r_ast' => 60,
                'r_stl' => 60,
                'r_to' => 60,
                'r_blk' => 60,
                'r_foul' => 60,
                'oo' => 60,
                'od' => 60,
                'do' => 60,
                'dd' => 60,
                'po' => 60,
                'pd' => 60,
                'to' => 60,
                'td' => 60,
                'Clutch' => 60,
                'Consistency' => 60,
                'talent' => 60,
                'skill' => 60,
                'intangibles' => 60,
                // Free agency preferences
                'winner' => 5,
                'tradition' => 5,
                'loyalty' => 1,
                'playingTime' => 5,
                'security' => 1,
                // Contract details
                'exp' => 8,
                'bird' => 3,
                'cy' => 1,
                'cyt' => 1,
                'cy1' => 1200,
                'cy2' => 0,
                'cy3' => 0,
                'cy4' => 0,
                'cy5' => 0,
                'cy6' => 0,
                'draftyear' => 2015,
                // News story fields
                'catid' => 1,
                'counter' => 10,
                'topicid' => 5
            ])
        ]);
    }

    private function setupBasicExtensionScenario()
    {
        // Combined data that works for all queries in the scenario
        $this->mockDb->setMockData([
            array_merge($this->getBasePlayerData(), [
                'Used_Extension_This_Season' => 0,
                'Used_Extension_This_Chunk' => 0,
                'name' => 'Test Player',
                'pid' => 3,
                'ordinal' => 3,
                'exp' => 5,
                'bird' => 2,
                'cy' => 1,
                'cyt' => 1,
                'cy1' => 800,
                'cy2' => 0,
                'cy3' => 0,
                'cy4' => 0,
                'cy5' => 0,
                'cy6' => 0,
                'catid' => 1,
                'counter' => 10
            ])
        ]);
    }

    private function setupAlreadyExtendedScenario()
    {
        $this->mockDb->setMockData([
            array_merge($this->getBasePlayerData(), [
                'Used_Extension_This_Season' => 1,
                'Used_Extension_This_Chunk' => 0,
                'name' => 'Test Player',
                'pid' => 4,
                'ordinal' => 4,
                'exp' => 5,
                'bird' => 2
            ])
        ]);
    }

    private function setupBirdRightsExtensionScenario()
    {
        // Combined data that works for all queries in the scenario
        $this->mockDb->setMockData([
            array_merge($this->getBasePlayerData(), [
                // Team info fields
                'Used_Extension_This_Season' => 0,
                'Used_Extension_This_Chunk' => 0,
                'Contract_Wins' => 50,
                'Contract_Losses' => 32,
                'Contract_AvgW' => 2500,
                'Contract_AvgL' => 2000,
                'money_committed_at_position' => 0,
                // Player info fields (combined in same row for mock)
                'pid' => 5,
                'ordinal' => 5,
                'name' => 'Veteran Player',
                'nickname' => 'Vet',
                'age' => 30,
                'exp' => 8,
                'bird' => 4,
                'cy' => 1,
                'cyt' => 1,
                'winner' => 3,
                'tradition' => 3,
                'loyalty' => 3,
                'playingTime' => 3,
                'security' => 3,
                'cy1' => 900,
                'cy2' => 0,
                'cy3' => 0,
                'cy4' => 0,
                'cy5' => 0,
                'cy6' => 0,
                // News story fields
                'catid' => 1,
                'counter' => 10,
                'topicid' => 5
            ])
        ]);
    }

    private function setupHighLoyaltyPlayerScenario()
    {
        // Combined data that works for all queries in the scenario
        $this->mockDb->setMockData([
            array_merge($this->getBasePlayerData(), [
                // Team info fields
                'Used_Extension_This_Season' => 0,
                'Used_Extension_This_Chunk' => 0,
                'Contract_Wins' => 45,
                'Contract_Losses' => 37,
                'Contract_AvgW' => 2300,
                'Contract_AvgL' => 2100,
                'money_committed_at_position' => 0,
                // Player info fields (combined in same row for mock)
                'pid' => 6,
                'ordinal' => 6,
                'name' => 'Loyal Player',
                'nickname' => 'Loyal',
                'age' => 27,
                'exp' => 6,
                'bird' => 3,
                'winner' => 3,
                'tradition' => 3,
                'loyalty' => 5,
                'playingTime' => 3,
                'security' => 3,
                'cy' => 1,
                'cyt' => 1,
                'cy1' => 850,
                'cy2' => 0,
                'cy3' => 0,
                'cy4' => 0,
                'cy5' => 0,
                'cy6' => 0,
                // News story fields
                'catid' => 1,
                'counter' => 10,
                'topicid' => 5
            ])
        ]);
    }

    private function setupPlayingTimeScenario()
    {
        // Combined data that works for all queries in the scenario
        $this->mockDb->setMockData([
            array_merge($this->getBasePlayerData(), [
                // Team info fields
                'Used_Extension_This_Season' => 0,
                'Used_Extension_This_Chunk' => 0,
                'money_committed_at_position' => 8000,
                'Contract_Wins' => 55,
                'Contract_Losses' => 27,
                'Contract_AvgW' => 2700,
                'Contract_AvgL' => 1900,
                // Player info fields (combined in same row for mock)
                'pid' => 7,
                'ordinal' => 7,
                'name' => 'Rotation Player',
                'nickname' => 'Roto',
                'age' => 24,
                'pos' => 'G',  // Position field required
                'exp' => 4,
                'bird' => 2,
                'winner' => 3,
                'tradition' => 3,
                'loyalty' => 2,
                'playingTime' => 5,
                'security' => 3,
                'cy' => 1,
                'cyt' => 1,
                'cy1' => 800,
                'cy2' => 0,
                'cy3' => 0,
                'cy4' => 0,
                'cy5' => 0,
                'cy6' => 0,
                // News story fields
                'catid' => 1,
                'counter' => 10,
                'topicid' => 5
            ])
        ]);
    }

    /**
     * Helper method that returns base Player data fields to avoid duplication
     * and ensure all required fields are present in mock data
     */
    private function getBasePlayerData()
    {
        return [
            // Basic info
            'pid' => 1,
            'ordinal' => 1,
            'nickname' => 'Test',
            'age' => 25,
            'tid' => 1,
            'teamname' => 'Test Team',
            'pos' => 'SF',
            // Ratings
            'r_fga' => 50,
            'r_fgp' => 50,
            'r_fta' => 50,
            'r_ftp' => 50,
            'r_tga' => 50,
            'r_tgp' => 50,
            'r_orb' => 50,
            'r_drb' => 50,
            'r_ast' => 50,
            'r_stl' => 50,
            'r_to' => 50,
            'r_blk' => 50,
            'r_foul' => 50,
            'oo' => 50,
            'od' => 50,
            'do' => 50,
            'dd' => 50,
            'po' => 50,
            'pd' => 50,
            'to' => 50,
            'td' => 50,
            'Clutch' => 50,
            'Consistency' => 50,
            'talent' => 50,
            'skill' => 50,
            'intangibles' => 50,
            // Draft info
            'draftyear' => 2018,
            'draftround' => 1,
            'draftpickno' => 15,
            'draftedby' => 'Test Team',
            'draftedbycurrentname' => 'Test Team',
            'college' => 'Test University',
            // Physical attributes
            'htft' => 6,
            'htin' => 8,
            'wt' => 210,
            // Status
            'injured' => 0,
            'retired' => 0,
            'droptime' => 0,
            // Team info fields
            'teamid' => 1,
            'team_city' => 'Test',
            'team_name' => 'Team',
            'color1' => 'Blue',
            'color2' => 'White',
            'arena' => 'Test Arena',
            'capacity' => 20000,
            'formerly_known_as' => '',
            'owner_name' => 'Test Owner',
            'owner_email' => 'owner@test.com',
            'discordID' => '123456',
            'HasMLE' => 0,
            'HasLLE' => 0,
            'leagueRecord' => '0-0',
            'SERVER_NAME' => 'test.ibl.com',
            // Free agency preferences (required by Player.php line 162-163)
            'loyalty' => 3,
            'playingTime' => 3,
            'winner' => 3,
            'tradition' => 3,
            'security' => 3,
            // Contract details
            'exp' => 5,
            'bird' => 2,
            'cy' => 1,
            'cyt' => 1,
            'cy1' => 800,
            'cy2' => 0,
            'cy3' => 0,
            'cy4' => 0,
            'cy5' => 0,
            'cy6' => 0,
        ];
    }
}
