<?php

require_once __DIR__ . '/../bootstrap.php';

/**
 * Integration tests for TradeProcessor ensuring refactored code behaves identically to original
 */
class TradeProcessorIntegrationTest extends PHPUnit\Framework\TestCase
{
    private $mockDb;
    private $processor;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->processor = new Trading_TradeProcessor($this->mockDb);
    }

    /**
     * Test complete trade processing workflow
     * @group integration
     */
    public function testCompleteTradeProcessingWorkflow()
    {
        // Arrange - Set up a complete trade scenario
        $offerId = 123;
        
        // Set SERVER_NAME to avoid undefined key error
        $_SERVER['SERVER_NAME'] = 'localhost';
        
        // Mock trade info data (1 player only to simplify)
        $this->mockDb->setMockTradeInfo([
            ['itemid' => 1001, 'itemtype' => 1, 'from' => 'Lakers', 'to' => 'Celtics'], // Player
        ]);
        
        // Mock player data
        $this->mockDb->setMockData([
            ['pos' => 'PG', 'name' => 'Test Player']
        ]);

        // Act
        $result = $this->processor->processTrade($offerId);

        // Assert
        $this->assertTrue($result['success'], 'Trade processing should succeed');
        $this->assertArrayHasKey('storytext', $result);
        $this->assertArrayHasKey('storytitle', $result);
        $this->assertStringContainsString('Lakers', $result['storytext']);
        $this->assertStringContainsString('Celtics', $result['storytext']);
    }

    /**
     * Test that invalid trade offer ID returns error
     * @group integration
     */
    public function testInvalidTradeOfferIdReturnsError()
    {
        // Arrange
        $offerId = 999;
        $this->mockDb->setNumRows(0); // No trade data

        // Act
        $result = $this->processor->processTrade($offerId);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('No trade data found', $result['error']);
    }

    /**
     * Test player transfer maintains all original behavior
     * @group integration
     */
    public function testPlayerTransferMaintainsOriginalBehavior()
    {
        // Arrange
        $_SERVER['SERVER_NAME'] = 'localhost';
        $offerId = 123;
        $this->mockDb->setMockTradeInfo([
            ['itemid' => 1001, 'itemtype' => 1, 'from' => 'Bulls', 'to' => 'Heat']
        ]);
        $this->mockDb->setMockData([
            ['pos' => 'SF', 'name' => 'Michael Jordan']
        ]);

        // Act
        $result = $this->processor->processTrade($offerId);

        // Assert - Check that story text follows original format
        $this->assertTrue($result['success']);
        $expectedText = "The Bulls send SF Michael Jordan to the Heat.";
        $this->assertStringContainsString($expectedText, $result['storytext']);
        
        // Verify UPDATE query was executed with correct format
        $queries = $this->mockDb->getExecutedQueries();
        $hasPlayerUpdate = false;
        foreach ($queries as $query) {
            if (strpos($query, 'UPDATE ibl_plr') !== false 
                && strpos($query, 'teamname') !== false 
                && strpos($query, 'tid') !== false) {
                $hasPlayerUpdate = true;
                break;
            }
        }
        $this->assertTrue($hasPlayerUpdate, 'Should execute player UPDATE query');
    }

    /**
     * Test draft pick transfer maintains all original behavior
     * @group integration
     */
    public function testDraftPickTransferMaintainsOriginalBehavior()
    {
        // Arrange
        $_SERVER['SERVER_NAME'] = 'localhost';
        $offerId = 124;
        $this->mockDb->setMockTradeInfo([
            ['itemid' => 2001, 'itemtype' => 0, 'from' => 'Knicks', 'to' => 'Nets']
        ]);
        $this->mockDb->setMockData([
            ['year' => 2024, 'teampick' => 'Knicks', 'round' => 1]
        ]);

        // Act
        $result = $this->processor->processTrade($offerId);

        // Assert - Check that story text follows original format
        $this->assertTrue($result['success']);
        $expectedText = "The Knicks send the 2024 Knicks Round 1 draft pick to the Nets.";
        $this->assertStringContainsString($expectedText, $result['storytext']);
        
        // Verify UPDATE query was executed
        $queries = $this->mockDb->getExecutedQueries();
        $hasPickUpdate = false;
        foreach ($queries as $query) {
            if (strpos($query, 'UPDATE ibl_draft_picks') !== false) {
                $hasPickUpdate = true;
                break;
            }
        }
        $this->assertTrue($hasPickUpdate, 'Should execute pick UPDATE query');
    }

    /**
     * Test cash transaction maintains all original behavior
     * @group integration
     */
    public function testCashTransactionMaintainsOriginalBehavior()
    {
        // Arrange
        $_SERVER['SERVER_NAME'] = 'localhost';
        $offerId = 125;
        $this->mockDb->setMockTradeInfo([
            ['itemid' => 30013000, 'itemtype' => 'cash', 'from' => 'Warriors', 'to' => 'Spurs']
        ]);
        
        // Mock cash details - set mock data to return associative array with cash year keys
        $this->mockDb->setMockData([
            [
                'cy1' => 1000, 
                'cy2' => 1500, 
                'cy3' => 0, 
                'cy4' => 0, 
                'cy5' => 0, 
                'cy6' => 0,
                0 => 1000,  // Add indexed values too for fetchRow compatibility
                1 => 1500,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0
            ]
        ]);

        // Act
        $result = $this->processor->processTrade($offerId);

        // Assert - Check cash transaction creates proper entries
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('cash', $result['storytext']);
        $this->assertStringContainsString('Warriors', $result['storytext']);
        $this->assertStringContainsString('Spurs', $result['storytext']);
        
        // Verify two INSERT queries for positive and negative cash entries
        $queries = $this->mockDb->getExecutedQueries();
        $cashInsertCount = 0;
        foreach ($queries as $query) {
            if (strpos($query, 'INSERT INTO `ibl_plr`') !== false 
                && strpos($query, 'Cash') !== false) {
                $cashInsertCount++;
            }
        }
        $this->assertEquals(2, $cashInsertCount, 'Should insert both positive and negative cash entries');
    }

    /**
     * Test that notifications are sent correctly
     * @group integration
     */
    public function testNotificationsAreSentCorrectly()
    {
        // Arrange
        $_SERVER['SERVER_NAME'] = 'localhost';
        $offerId = 126;
        $this->mockDb->setMockTradeInfo([
            ['itemid' => 1001, 'itemtype' => 1, 'from' => 'Rockets', 'to' => 'Mavs']
        ]);
        $this->mockDb->setMockData([
            ['pos' => 'C', 'name' => 'Hakeem Olajuwon']
        ]);

        // Act
        $result = $this->processor->processTrade($offerId);

        // Assert
        $this->assertTrue($result['success']);
        
        // Verify news story creation
        $queries = $this->mockDb->getExecutedQueries();
        $hasNewsStory = false;
        foreach ($queries as $query) {
            if (strpos($query, 'INSERT INTO nuke_stories') !== false) {
                $hasNewsStory = true;
                break;
            }
        }
        $this->assertTrue($hasNewsStory, 'Should create news story');
    }

    /**
     * Test that trade cleanup occurs
     * @group integration
     */
    public function testTradeCleanupOccurs()
    {
        // Arrange
        $_SERVER['SERVER_NAME'] = 'localhost';
        $offerId = 127;
        $this->mockDb->setMockTradeInfo([
            ['itemid' => 1001, 'itemtype' => 1, 'from' => 'Jazz', 'to' => 'Suns']
        ]);
        $this->mockDb->setMockData([
            ['pos' => 'PF', 'name' => 'Karl Malone']
        ]);

        // Act
        $result = $this->processor->processTrade($offerId);

        // Assert
        $this->assertTrue($result['success']);
        
        // Verify cleanup queries
        $queries = $this->mockDb->getExecutedQueries();
        $hasInfoDelete = false;
        $hasCashDelete = false;
        foreach ($queries as $query) {
            if (strpos($query, 'DELETE FROM ibl_trade_info') !== false) {
                $hasInfoDelete = true;
            }
            if (strpos($query, 'DELETE FROM ibl_trade_cash') !== false) {
                $hasCashDelete = true;
            }
        }
        $this->assertTrue($hasInfoDelete, 'Should delete trade info');
        $this->assertTrue($hasCashDelete, 'Should delete trade cash');
    }
}
