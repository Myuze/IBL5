<?php
/**
 * PHPUnit Bootstrap for Trading Module Tests
 * 
 * This bootstrap file sets up the testing environment for the Trading module
 * without requiring the full IBL5 application context.
 */

// Define constants that might be needed
if (!defined('DIRECTORY_SEPARATOR')) {
    define('DIRECTORY_SEPARATOR', '/');
}

// Mock the autoloader for Trading classes
spl_autoload_register(function ($class) {
    // Handle Trading classes
    if (strpos($class, 'Trading_') === 0) {
        $classFile = str_replace('Trading_', '', $class);
        $file = __DIR__ . '/../classes/Trading/' . $classFile . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Handle Extension classes with namespace
    if (strpos($class, 'Extension\\') === 0) {
        // Remove namespace prefix to get just the class name
        $className = str_replace('Extension\\', '', $class);
        $file = __DIR__ . '/../classes/Extension/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Handle DepthChart classes with namespace
    if (strpos($class, 'DepthChart\\') === 0) {
        // Remove namespace prefix to get just the class name
        $className = str_replace('DepthChart\\', '', $class);
        $file = __DIR__ . '/../classes/DepthChart/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Handle Updater classes with namespace
    if (strpos($class, 'Updater\\') === 0) {
        // Remove namespace prefix to get just the class name
        $className = str_replace('Updater\\', '', $class);
        $file = __DIR__ . '/../classes/Updater/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Handle Services namespace (for DatabaseService and future services)
    if (strpos($class, 'Services\\') === 0) {
        $className = str_replace('Services\\', '', $class);
        $file = __DIR__ . '/../classes/Services/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }

    // Handle Voting namespace
    if (strpos($class, 'Voting\\') === 0) {
        $className = str_replace('Voting\\', '', $class);
        $file = __DIR__ . '/../classes/Voting/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Handle Waivers namespace
    if (strpos($class, 'Waivers\\') === 0) {
        $className = str_replace('Waivers\\', '', $class);
        $file = __DIR__ . '/../classes/Waivers/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Handle Player namespace classes
    if (strpos($class, 'Player\\') === 0) {
        $className = substr($class, 7); // Remove "Player\" prefix
        $file = __DIR__ . '/../classes/Player/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Handle global namespace classes like Team, League, and JSB
    if (in_array($class, ['Team', 'League', 'JSB'])) {
        $file = __DIR__ . '/../classes/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // For other classes, don't auto-load them if we have mocks
    // This prevents loading the real Season, Shared, etc. classes
    return false;
});

// Define mock classes for testing
class MockDatabase
{
    private $mockData = [];
    private $mockTradeInfo = [];
    private $numRows = null;
    private $returnTrue = false;
    private $executedQueries = [];
    
    public function sql_query($query)
    {
        // Track all executed queries for verification
        $this->executedQueries[] = $query;
        
        // For queries that expect boolean return (INSERT, UPDATE, DELETE)
        if ($this->returnTrue || 
            stripos($query, 'INSERT') === 0 || 
            stripos($query, 'UPDATE') === 0 || 
            stripos($query, 'DELETE') === 0) {
            return true;
        }
        
        // Special handling for trade info queries
        if (stripos($query, 'ibl_trade_info') !== false && !empty($this->mockTradeInfo)) {
            return new MockDatabaseResult($this->mockTradeInfo);
        }
        
        // Return a mock result for SELECT queries
        return new MockDatabaseResult($this->mockData);
    }
    
    public function sql_result($result, $row, $field = null)
    {
        if ($result instanceof MockDatabaseResult) {
            return $result->getResult($row, $field);
        }
        return null;
    }
    
    public function sql_fetchrow($result)
    {
        if ($result instanceof MockDatabaseResult) {
            return $result->fetchRow();
        }
        return false;
    }
    
    public function sql_fetch_assoc($result)
    {
        if ($result instanceof MockDatabaseResult) {
            return $result->fetchAssoc();
        }
        return false;
    }
    
    public function sql_numrows($result)
    {
        // Allow manual override for testing
        if ($this->numRows !== null) {
            return $this->numRows;
        }
        
        if ($result instanceof MockDatabaseResult) {
            return $result->numRows();
        }
        return 0;
    }
    
    public function setMockData($data)
    {
        $this->mockData = $data;
    }
    
    public function setMockTradeInfo($data)
    {
        $this->mockTradeInfo = $data;
        // Also set numRows to match trade info count
        $this->numRows = count($data);
    }
    
    public function setNumRows($numRows)
    {
        $this->numRows = $numRows;
    }
    
    public function setReturnTrue($returnTrue = true)
    {
        $this->returnTrue = $returnTrue;
    }
    
    public function getExecutedQueries()
    {
        return $this->executedQueries;
    }
    
    public function clearQueries()
    {
        $this->executedQueries = [];
    }
    
    public function sql_escape_string($string)
    {
        // Simple escaping for mock - in production this would use mysqli_real_escape_string
        return addslashes($string);
    }
}

class MockDatabaseResult
{
    private $data;
    private $position = 0;
    
    public function __construct($data = [])
    {
        $this->data = is_array($data) ? $data : [];
    }
    
    public function getResult($row, $field)
    {
        // Handle numeric field access
        if (is_numeric($field)) {
            $values = array_values($this->data[$row] ?? []);
            return $values[$field] ?? null;
        }
        // Handle associative field access
        return isset($this->data[$row][$field]) ? $this->data[$row][$field] : null;
    }
    
    public function fetchRow()
    {
        if ($this->position < count($this->data)) {
            $row = $this->data[$this->position++];
            // Return both numeric and associative keys for compatibility
            if (is_array($row) && !isset($row[0])) {
                // Only has associative keys, add numeric ones
                $row = array_merge(array_values($row), $row);
            }
            return $row;
        }
        return false;
    }
    
    public function fetchAssoc()
    {
        if ($this->position < count($this->data)) {
            return $this->data[$this->position++];
        }
        return false;
    }
    
    public function numRows()
    {
        return count($this->data);
    }
}

if (!class_exists('Discord')) {
    class Discord
    {
        public static function getDiscordIDFromTeamname($db, $teamname)
        {
            return '123456789';
        }
        
        public static function postToChannel($channel, $message)
        {
            return true;
        }
    }
}

if (!class_exists('Shared')) {
    class Shared
    {
        protected $db;
        
        public function __construct($db)
        {
            $this->db = $db;
        }
        
        public function getTidFromTeamname($teamname)
        {
            // Mock team ID mapping
            static $teamMap = [
                'Atlanta Hawks' => 1,
                'Boston Celtics' => 2,
                'Charlotte Hornets' => 3,
                'Chicago Bulls' => 4,
                'Cleveland Cavaliers' => 5,
                'Dallas Mavericks' => 6,
                'Denver Nuggets' => 7,
                'Detroit Pistons' => 8,
                'Golden State Warriors' => 9,
                'Houston Rockets' => 10,
                'Indiana Pacers' => 11,
                'LA Clippers' => 12,
                'LA Lakers' => 13,
                'Memphis Grizzlies' => 14,
                'Miami Heat' => 15,
                'Milwaukee Bucks' => 16,
                'Minnesota Timberwolves' => 17,
                'New Jersey Nets' => 18,
                'New Orleans Hornets' => 19,
                'New York Knicks' => 20,
                'Orlando Magic' => 21,
                'Philadelphia 76ers' => 22,
                'Phoenix Suns' => 23,
                'Portland Trail Blazers' => 24,
                'Sacramento Kings' => 25,
                'San Antonio Spurs' => 26,
                'Seattle SuperSonics' => 27,
                'Toronto Raptors' => 28,
                'Utah Jazz' => 29,
                'Vancouver Grizzlies' => 30,
                'Washington Wizards' => 31,
            ];
            
            return $teamMap[$teamname] ?? 1;
        }
        
        public function resetSimContractExtensionAttempts()
        {
            echo '<p>Resetting sim contract extension attempts...</p>';
            $sqlQueryString = "UPDATE ibl_team_info SET Used_Extension_This_Chunk = 0;";
            if ($this->db->sql_query($sqlQueryString)) {
                echo '<p>Sim contract extension attempts have been reset.</p>';
                return;
            }
        }
    }
}

if (!class_exists('UI')) {
    class UI
    {
        public static function displayDebugOutput($content, $title = 'Debug Output')
        {
            // In test mode, don't output anything
            // This prevents test output pollution
            if (defined('PHPUNIT_RUNNING') || php_sapi_name() === 'cli') {
                return;
            }
            
            // Otherwise, output normally (though this shouldn't happen in tests)
            static $debugId = 0;
            $debugId++;
            
            echo "<div style='margin: 10px 0; border: 1px solid #ccc; border-radius: 4px;'>
                <div style='padding: 8px; background-color: #f5f5f5; border-bottom: 1px solid #ccc; cursor: pointer;'
                     onclick='toggleDebug$debugId()'>
                    <span id='debugIcon$debugId'>▶</span> $title
                </div>
                <pre id='debugContent$debugId' style='display: none; margin: 0; padding: 8px; background-color: #fff; overflow: auto;'>$content</pre>
            </div>
            <script>
                function toggleDebug$debugId() {
                    var content = document.getElementById('debugContent$debugId');
                    var icon = document.getElementById('debugIcon$debugId');
                    if (content.style.display === 'none') {
                        content.style.display = 'block';
                        icon.textContent = '▼';
                    } else {
                        content.style.display = 'none';
                        icon.textContent = '▶';
                    }
                }
            </script>";
        }
    }
}

if (!class_exists('Season')) {
    class Season
    {
        public $phase = 'Regular Season';
        public $endingYear = 2024;
        public $beginningYear = 2023;
        public $regularSeasonStartDate;
        public $postAllStarStartDate;
        public $playoffsStartDate;
        public $playoffsEndDate;
        public $lastSimNumber = 1;
        public $lastSimStartDate = '2024-01-01';
        public $lastSimEndDate = '2024-01-02';
        public $projectedNextSimEndDate = '2024-01-03';
        public $allowTrades = 'Yes';
        public $allowWaivers = 'Yes';
        public $freeAgencyNotificationsState = 'Off';
        
        const IBL_PRESEASON_MONTH = 9;
        const IBL_HEAT_MONTH = 10;
        const IBL_REGULAR_SEASON_STARTING_MONTH = 11;
        const IBL_ALL_STAR_MONTH = 2;
        const IBL_REGULAR_SEASON_ENDING_MONTH = 5;
        const IBL_PLAYOFF_MONTH = 6;
        
        protected $db;
        
        public function __construct($db)
        {
            $this->db = $db;
            // Initialize properties without database calls for testing
            $this->phase = 'Regular Season';
            $this->endingYear = 2024;
            $this->beginningYear = 2023;
            $this->regularSeasonStartDate = date_create("2023-11-01");
            $this->postAllStarStartDate = date_create("2024-02-04");
            $this->playoffsStartDate = date_create("2024-06-01");
            $this->playoffsEndDate = date_create("2024-06-30");
        }
        
        // Mock the methods that would normally query the database
        private function getSeasonPhase()
        {
            return $this->phase;
        }
        
        private function getSeasonEndingYear()
        {
            return $this->endingYear;
        }
        
        private function getLastSimDatesArray()
        {
            return [
                'Sim' => $this->lastSimNumber,
                'Start Date' => $this->lastSimStartDate,
                'End Date' => $this->lastSimEndDate
            ];
        }
        
        private function getProjectedNextSimEndDate($db, $lastSimEndDate)
        {
            return $this->projectedNextSimEndDate;
        }
    }
}