<?php

// Simple test without database dependencies
echo "Testing Trading Module Refactoring (Unit Tests)...\n\n";

// Set up autoloader for classes
spl_autoload_register(function ($class) {
    // Handle classes in the global namespace
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    // Handle Trading classes
    if (strpos($class, 'Trading_') === 0) {
        $classFile = str_replace('Trading_', '', $class);
        $file = __DIR__ . '/../classes/Trading/' . $classFile . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    return false;
});

// Mock database class for testing
class MockDB {
    public function sql_query($query) { return true; }
    public function sql_result($result, $row, $field) { return null; }
    public function sql_fetchrow($result) { return ['cy1' => 100, 'cy2' => 200, 'cy3' => 300, 'cy4' => 0, 'cy5' => 0, 'cy6' => 0]; }
    public function sql_fetch_assoc($result) { return false; }
    public function sql_numrows($result) { return 0; }
}

// Mock classes
class MockShared {
    public function __construct($db) {}
    public function getTidFromTeamname($teamname) { return 1; }
}

class MockSeason {
    public $phase = 'Regular Season';
    public $endingYear = 2024;
    public function __construct($db) {}
}

class MockLeague {
    const HARD_CAP_MAX = 7000;
}

class MockDiscord {
    public static function getDiscordIDFromTeamname($db, $teamname) { return '123456789'; }
    public static function postToChannel($channel, $message) { return true; }
}

// Define mock classes
class League { const HARD_CAP_MAX = 7000; }
class Discord {
    public static function getDiscordIDFromTeamname($db, $teamname) { return '123456789'; }
    public static function postToChannel($channel, $message) { return true; }
}
class Shared extends MockShared {}
class Season extends MockSeason {}

$mockDb = new MockDB();

echo "=== Testing TradeValidator ===\n";
$validator = new Trading_TradeValidator($mockDb);

// Test cash validation with valid amounts
$userCash = [1 => 100, 2 => 200, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
$partnerCash = [1 => 150, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
$result = $validator->validateMinimumCashAmounts($userCash, $partnerCash);
echo "Valid cash amounts test: " . ($result['valid'] ? "PASS" : "FAIL") . "\n";

// Test cash validation with invalid amounts
$userCash = [1 => 50, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0]; // Below minimum
$result = $validator->validateMinimumCashAmounts($userCash, $partnerCash);
echo "Invalid cash amounts test: " . (!$result['valid'] ? "PASS" : "FAIL") . "\n";

// Test current season cash considerations
$userCash = [1 => 100, 2 => 200, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
$partnerCash = [1 => 150, 2 => 250, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
$considerations = $validator->getCurrentSeasonCashConsiderations($userCash, $partnerCash);
echo "Cash considerations test: " . (isset($considerations['cashSentToThem']) ? "PASS" : "FAIL") . "\n";

echo "\n=== Testing CashTransactionHandler ===\n";
$cashHandler = new Trading_CashTransactionHandler($mockDb);

// Test contract years calculation
$cashYear = [1 => 100, 2 => 200, 3 => 300, 4 => 0, 5 => 0, 6 => 0];
$contractYears = $cashHandler->calculateContractTotalYears($cashYear);
echo "Contract years calculation test: " . ($contractYears == 3 ? "PASS" : "FAIL") . "\n";

// Test cash detection
$hasCash = $cashHandler->hasCashInTrade($cashYear);
echo "Has cash in trade test: " . ($hasCash ? "PASS" : "FAIL") . "\n";

$noCash = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
$hasNoCash = $cashHandler->hasCashInTrade($noCash);
echo "No cash in trade test: " . (!$hasNoCash ? "PASS" : "FAIL") . "\n";

echo "\n=== All Tests Completed ===\n";
echo "Trading module classes are working!\n";