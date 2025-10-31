<?php

use PHPUnit\Framework\TestCase;

class TradeApprovalTest extends TestCase
{
    private $db;
    
    protected function setUp(): void
    {
        $this->db = new MockDatabase();
        
        // Set up mock data for team ID lookups and player data
        $this->db->setMockData([
            ['counter' => 1000],
            ['name' => 'Test Player', 'pos' => 'PG']
        ]);
    }
    
    /**
     * Test that approval field is always set to the listening team (receiving team)
     * This test verifies the fix for the bug where the offering team could accept
     * their own trade offer when cash was involved.
     */
    public function testApprovalAlwaysSetToListeningTeam()
    {
        $tradeOffer = new Trading_TradeOffer($this->db);
        
        // Prepare trade data: Team A offers to Team B
        // Team A sends cash only (no players or picks)
        // Team B sends cash back
        $tradeData = [
            'offeringTeam' => 'Atlanta Hawks',
            'listeningTeam' => 'Boston Celtics',
            'switchCounter' => 0,      // No items from offering team (only cash)
            'fieldsCounter' => 0,      // No items from listening team (only cash)
            'userSendsCash' => [0, 150, 150, 0, 0, 0, 0],    // Team A sends cash
            'partnerSendsCash' => [0, 100, 200, 0, 0, 0, 0], // Team B sends cash
            'check' => [],             // No items to check
            'contract' => [],          // No contracts
            'index' => [],             // No items
            'type' => []               // No item types
        ];
        
        $result = $tradeOffer->createTradeOffer($tradeData);
        
        $this->assertTrue($result['success'], 'Trade offer should be created successfully');
        
        // Get all executed queries
        $queries = $this->db->getExecutedQueries();
        
        // Filter to INSERT INTO ibl_trade_info queries
        $tradeInfoInserts = array_filter($queries, function($query) {
            return stripos($query, 'INSERT INTO ibl_trade_info') !== false;
        });
        
        // Check each trade info insert - all should be cash items with approval = Boston Celtics
        foreach ($tradeInfoInserts as $query) {
            // For cash items, extract the from, to, and approval teams
            if (strpos($query, "'cash'") !== false) {
                // Pattern matches: VALUES ('tradeid', 'itemid', 'cash', 'from', 'to', 'approval')
                if (preg_match("/VALUES\s*\(\s*'[^']+'\s*,\s*'[^']+'\s*,\s*'cash'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*\)/i", $query, $matches)) {
                    $from = $matches[1];
                    $to = $matches[2];
                    $approval = $matches[3];
                    
                    // Approval should ALWAYS be 'Boston Celtics' (the listening team), 
                    // regardless of whether the cash is from Atlanta or Boston
                    $this->assertEquals('Boston Celtics', $approval, 
                        "For cash from {$from} to {$to}, approval should always be the listening team (Boston Celtics), but got {$approval}");
                }
            }
        }
    }
    
    /**
     * Test the specific bug scenario: when listening team sends cash back to offering team,
     * the approval should still be the listening team, not the offering team
     */
    public function testCashFromListeningTeamHasCorrectApproval()
    {
        $tradeOffer = new Trading_TradeOffer($this->db);
        
        // Team A offers cash to Team B
        // Team B sends cash back to Team A
        // Expected: approval should be Team B (listening team) for ALL cash items
        $tradeData = [
            'offeringTeam' => 'Atlanta Hawks',
            'listeningTeam' => 'Boston Celtics',
            'switchCounter' => 0,
            'fieldsCounter' => 0,
            'userSendsCash' => [0, 300, 200, 0, 0, 0, 0],       // Atlanta sends cash
            'partnerSendsCash' => [0, 500, 500, 0, 0, 0, 0],    // Boston sends cash back
            'check' => [],
            'contract' => [],
            'index' => [],
            'type' => []
        ];
        
        $result = $tradeOffer->createTradeOffer($tradeData);
        $this->assertTrue($result['success']);
        
        $queries = $this->db->getExecutedQueries();
        
        // Find all cash inserts and verify approval is always Boston Celtics
        foreach ($queries as $query) {
            if (strpos($query, "'cash'") !== false) {
                // Extract approval value
                // Pattern matches: VALUES ('tradeid', 'itemid', 'cash', 'from', 'to', 'approval')
                if (preg_match("/VALUES\s*\(\s*'[^']+'\s*,\s*'[^']+'\s*,\s*'cash'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*\)/i", $query, $matches)) {
                    $from = $matches[1];
                    $to = $matches[2];
                    $approval = $matches[3];
                    
                    // This test verifies the fix: approval should always be the listening team (Boston Celtics)
                    $this->assertEquals('Boston Celtics', $approval, 
                        "When {$from} sends cash to {$to}, " .
                        "approval must be Boston Celtics (the listening team). Got: {$approval}");
                }
            }
        }
    }
    
    /**
     * Test trade with only players (no cash)
     * Verifies that approval is set correctly when only players are traded
     */
    public function testTradeWithOnlyPlayers()
    {
        $db = new QueryAwareMockDatabase();
        $tradeOffer = new Trading_TradeOffer($db);
        
        // Team A sends Player 1 to Team B
        // Team B sends Player 2 back to Team A
        $tradeData = [
            'offeringTeam' => 'Atlanta Hawks',
            'listeningTeam' => 'Boston Celtics',
            'switchCounter' => 1,      // 1 player from offering team
            'fieldsCounter' => 2,      // 2 total (1 from each team)
            'userSendsCash' => [0, 0, 0, 0, 0, 0, 0],
            'partnerSendsCash' => [0, 0, 0, 0, 0, 0, 0],
            'check' => ['on', 'on'],   // Both players checked
            'contract' => [1000, 1500], // Player salaries
            'index' => [101, 102],      // Player IDs
            'type' => [1, 1]            // Both are players
        ];
        
        $result = $tradeOffer->createTradeOffer($tradeData);
        $this->assertTrue($result['success'], 'Trade with only players should succeed');
        
        // Verify all trade info inserts have correct approval
        $queries = $db->getExecutedQueries();
        $tradeInfoInserts = array_filter($queries, function($query) {
            return stripos($query, 'INSERT INTO ibl_trade_info') !== false;
        });
        
        foreach ($tradeInfoInserts as $query) {
            if (preg_match("/VALUES\s*\(\s*'[^']+'\s*,\s*'[^']+'\s*,\s*'[^']+'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*\)/i", $query, $matches)) {
                $approval = $matches[3];
                $this->assertEquals('Boston Celtics', $approval, 
                    "Player trade approval should be Boston Celtics (listening team), got {$approval}");
            }
        }
    }
    
    /**
     * Test trade with mix of players and cash
     * Verifies that approval is set correctly for both player and cash items
     */
    public function testTradeWithPlayersAndCash()
    {
        $db = new QueryAwareMockDatabase();
        $tradeOffer = new Trading_TradeOffer($db);
        
        // Team A sends Player 1 + cash to Team B
        // Team B sends Player 2 + cash back to Team A
        $tradeData = [
            'offeringTeam' => 'Atlanta Hawks',
            'listeningTeam' => 'Boston Celtics',
            'switchCounter' => 1,      // 1 player from offering team
            'fieldsCounter' => 2,      // 2 total (1 from each team)
            'userSendsCash' => [0, 200, 200, 0, 0, 0, 0],      // Atlanta sends cash
            'partnerSendsCash' => [0, 150, 150, 0, 0, 0, 0],   // Boston sends cash
            'check' => ['on', 'on'],   // Both players checked
            'contract' => [2000, 1800], // Player salaries
            'index' => [201, 202],      // Player IDs
            'type' => [1, 1]            // Both are players
        ];
        
        $result = $tradeOffer->createTradeOffer($tradeData);
        $this->assertTrue($result['success'], 'Trade with players and cash should succeed');
        
        // Verify all items (players and cash) have correct approval
        $queries = $db->getExecutedQueries();
        $tradeInfoInserts = array_filter($queries, function($query) {
            return stripos($query, 'INSERT INTO ibl_trade_info') !== false;
        });
        
        $this->assertGreaterThanOrEqual(4, count($tradeInfoInserts), 
            'Should have at least 4 trade items (2 players + 2 cash)');
        
        foreach ($tradeInfoInserts as $query) {
            if (preg_match("/VALUES\s*\(\s*'[^']+'\s*,\s*'[^']+'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*\)/i", $query, $matches)) {
                $itemType = $matches[1];
                $from = $matches[2];
                $to = $matches[3];
                $approval = $matches[4];
                
                $this->assertEquals('Boston Celtics', $approval, 
                    "Mixed trade: {$itemType} from {$from} to {$to} should have approval=Boston Celtics, got {$approval}");
            }
        }
    }
    
    /**
     * Test trade with draft picks only (no cash or players)
     * Verifies that approval is set correctly for pick trades
     */
    public function testTradeWithOnlyDraftPicks()
    {
        $db = new QueryAwareMockDatabase();
        $tradeOffer = new Trading_TradeOffer($db);
        
        // Team A sends 2025 1st round pick to Team B
        // Team B sends 2026 1st round pick back to Team A
        $tradeData = [
            'offeringTeam' => 'Atlanta Hawks',
            'listeningTeam' => 'Boston Celtics',
            'switchCounter' => 1,      // 1 pick from offering team
            'fieldsCounter' => 2,      // 2 total (1 from each team)
            'userSendsCash' => [0, 0, 0, 0, 0, 0, 0],
            'partnerSendsCash' => [0, 0, 0, 0, 0, 0, 0],
            'check' => ['on', 'on'],   // Both picks checked
            'contract' => [0, 0],       // Picks have no salary
            'index' => [501, 502],      // Pick IDs
            'type' => [0, 0]            // Both are picks (0 = pick)
        ];
        
        $result = $tradeOffer->createTradeOffer($tradeData);
        $this->assertTrue($result['success'], 'Trade with only picks should succeed');
        
        // Verify all pick inserts have correct approval
        $queries = $db->getExecutedQueries();
        $tradeInfoInserts = array_filter($queries, function($query) {
            return stripos($query, 'INSERT INTO ibl_trade_info') !== false;
        });
        
        foreach ($tradeInfoInserts as $query) {
            if (preg_match("/VALUES\s*\(\s*'[^']+'\s*,\s*'[^']+'\s*,\s*'[^']+'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*\)/i", $query, $matches)) {
                $approval = $matches[3];
                $this->assertEquals('Boston Celtics', $approval, 
                    "Pick trade approval should be Boston Celtics (listening team), got {$approval}");
            }
        }
    }
}

/**
 * Enhanced MockDatabase that can return different data based on query type
 */
class QueryAwareMockDatabase extends MockDatabase
{
    public function sql_query($query)
    {
        // Track all executed queries
        $queries = $this->getExecutedQueries();
        $queries[] = $query;
        $this->clearQueries();
        foreach ($queries as $q) {
            parent::sql_query($q);
        }
        
        // For queries that expect boolean return (INSERT, UPDATE, DELETE)
        if (stripos($query, 'INSERT') === 0 || 
            stripos($query, 'UPDATE') === 0 || 
            stripos($query, 'DELETE') === 0) {
            return true;
        }
        
        // Return appropriate mock data based on query type
        if (stripos($query, 'ibl_trade_autocounter') !== false) {
            return new MockDatabaseResult([['counter' => 1000]]);
        }
        
        if (stripos($query, 'ibl_plr') !== false) {
            // Return player data
            return new MockDatabaseResult([
                ['name' => 'Test Player', 'pos' => 'PG']
            ]);
        }
        
        if (stripos($query, 'ibl_draft_picks') !== false) {
            // Return draft pick data
            return new MockDatabaseResult([
                ['teampick' => 'Test Team', 'year' => 2025, 'round' => 1, 'notes' => '']
            ]);
        }
        
        if (stripos($query, 'ibl_trade_cash') !== false) {
            // Return cash data
            return new MockDatabaseResult([
                ['cy1' => 100, 'cy2' => 200, 'cy3' => 0, 'cy4' => 0, 'cy5' => 0, 'cy6' => 0]
            ]);
        }
        
        // Default: return empty result
        return new MockDatabaseResult([]);
    }
}
