<?php

namespace Waivers;

/**
 * Handles database operations for waiver wire transactions
 */
class WaiversRepository
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Gets user information by username
     * 
     * @param string $username Username to look up
     * @return array|null User information or null if not found
     */
    public function getUserByUsername(string $username): ?array
    {
        $usernameEscaped = \Services\DatabaseService::escapeString($this->db, $username);
        $query = "SELECT * FROM nuke_users WHERE username = '$usernameEscaped'";
        $result = $this->db->sql_query($query);
        
        if (!$result || $this->db->sql_numrows($result) === 0) {
            return null;
        }
        
        return $this->db->sql_fetchrow($result);
    }
    
    /**
     * Gets team information by team name
     * 
     * @param string $teamName Team name to look up
     * @return array|null Team information or null if not found
     */
    public function getTeamByName(string $teamName): ?array
    {
        $teamNameEscaped = \Services\DatabaseService::escapeString($this->db, $teamName);
        $query = "SELECT * FROM ibl_team_info WHERE team_name = '$teamNameEscaped'";
        $result = $this->db->sql_query($query);
        
        if (!$result || $this->db->sql_numrows($result) === 0) {
            return null;
        }
        
        $row = $this->db->sql_fetchrow($result);
        return $row;
    }
    
    /**
     * Gets total salary for a team for the current year
     * 
     * @param string $teamName Team name
     * @return int Total salary in thousands
     */
    public function getTeamTotalSalary(string $teamName): int
    {
        $teamNameEscaped = \Services\DatabaseService::escapeString($this->db, $teamName);
        $query = "SELECT * FROM ibl_plr WHERE teamname = '$teamNameEscaped' AND retired = 0";
        $result = $this->db->sql_query($query);
        
        if (!$result) {
            return 0;
        }
        
        $totalSalary = 0;
        $numPlayers = $this->db->sql_numrows($result);
        
        for ($i = 0; $i < $numPlayers; $i++) {
            $row = $this->db->sql_fetchrow($result);
            $cy = (int) $row['cy'];
            $contractYearField = "cy$cy";
            if (isset($row[$contractYearField])) {
                $totalSalary += (int) $row[$contractYearField];
            }
        }
        
        return $totalSalary;
    }
    
    /**
     * Gets player information by player ID
     * 
     * @param int $playerID Player ID
     * @return array|null Player information or null if not found
     */
    public function getPlayerByID(int $playerID): ?array
    {
        $playerID = (int) $playerID;
        $query = "SELECT * FROM ibl_plr WHERE pid = $playerID";
        $result = $this->db->sql_query($query);
        
        if (!$result || $this->db->sql_numrows($result) === 0) {
            return null;
        }
        
        return $this->db->sql_fetchrow($result);
    }
    
    /**
     * Drops a player to waivers
     * 
     * @param int $playerID Player ID to drop
     * @param int $timestamp Current timestamp
     * @return bool Success status
     */
    public function dropPlayerToWaivers(int $playerID, int $timestamp): bool
    {
        $playerID = (int) $playerID;
        $timestamp = (int) $timestamp;
        
        $query = "UPDATE ibl_plr 
                  SET `ordinal` = '1000', 
                      `droptime` = '$timestamp' 
                  WHERE `pid` = $playerID 
                  LIMIT 1";
        
        return $this->db->sql_query($query) !== false;
    }
    
    /**
     * Signs a player from waivers
     * 
     * @param int $playerID Player ID to sign
     * @param string $teamName Team name
     * @param int $teamID Team ID
     * @param array $contractData Contract data including cy1, cy (optional)
     * @return bool Success status
     */
    public function signPlayerFromWaivers(int $playerID, string $teamName, int $teamID, array $contractData): bool
    {
        $playerID = (int) $playerID;
        $teamNameEscaped = \Services\DatabaseService::escapeString($this->db, $teamName);
        $teamID = (int) $teamID;
        
        $query = "UPDATE ibl_plr
                  SET `ordinal` = '800',
                      `bird` = 0, ";
        
        if (isset($contractData['cy1']) && $contractData['cy1'] > 0) {
            $cy1 = (int) $contractData['cy1'];
            $query .= "`cy1` = $cy1,
                       `cy` = 1, ";
        }
        
        $query .= "`teamname` = '$teamNameEscaped',
                   `tid` = $teamID,
                   `droptime` = 0
                   WHERE `pid` = $playerID
                   LIMIT 1";
        
        return $this->db->sql_query($query) !== false;
    }
    
    /**
     * Gets the category ID for waiver pool moves
     * 
     * @return int|null Category ID or null if not found
     */
    public function getWaiverPoolMovesCategory(): ?int
    {
        $query = "SELECT * FROM nuke_stories_cat WHERE title = 'Waiver Pool Moves'";
        $result = $this->db->sql_query($query);
        
        if (!$result || $this->db->sql_numrows($result) === 0) {
            return null;
        }
        
        return (int) $this->db->sql_result($result, 0, "catid");
    }
    
    /**
     * Increments the waiver pool moves counter
     * 
     * @return bool Success status
     */
    public function incrementWaiverPoolMovesCounter(): bool
    {
        $query = "UPDATE nuke_stories_cat 
                  SET counter = counter + 1 
                  WHERE title = 'Waiver Pool Moves'";
        
        return $this->db->sql_query($query) !== false;
    }
    
    /**
     * Creates a news story for a waiver transaction
     * 
     * @param int $topicID Topic ID
     * @param string $title Story title
     * @param string $hometext Story content
     * @return bool Success status
     */
    public function createNewsStory(int $topicID, string $title, string $hometext): bool
    {
        $topicID = (int) $topicID;
        $titleEscaped = \Services\DatabaseService::escapeString($this->db, $title);
        $hometextEscaped = \Services\DatabaseService::escapeString($this->db, $hometext);
        $timestamp = date('Y-m-d H:i:s', time());
        
        $query = "INSERT INTO nuke_stories
                  (catid,
                   aid,
                   title,
                   time,
                   hometext,
                   topic,
                   informant,
                   counter,
                   alanguage)
                  VALUES
                  (" . WaiversController::WAIVER_POOL_MOVES_CATEGORY_ID . ",
                   'Associated Press',
                   '$titleEscaped',
                   '$timestamp',
                   '$hometextEscaped',
                   $topicID,
                   'Associated Press',
                   0,
                   'english')";
        
        return $this->db->sql_query($query) !== false;
    }
}
