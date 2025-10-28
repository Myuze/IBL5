<?php

namespace DepthChart;

/**
 * Handles database operations for depth chart data
 */
class DepthChartRepository
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    

    
    /**
     * Gets players on a team
     * 
     * @param string $teamName Team name
     * @param int $teamID Team ID
     * @return mixed Database result
     */
    public function getPlayersOnTeam(string $teamName, int $teamID)
    {
        $teamNameEscaped = \Services\DatabaseService::escapeString($this->db, $teamName);
        $teamID = (int) $teamID; // Cast to int for safety
        $query = "SELECT * FROM ibl_plr WHERE teamname = '$teamNameEscaped' AND tid = $teamID AND retired = '0' AND ordinal <= " . \JSB::WAIVERS_ORDINAL . " ORDER BY ordinal ASC";
        return $this->db->sql_query($query);
    }
    
    /**
     * Updates player depth chart data
     * 
     * @param string $playerName Player name
     * @param array $depthChartValues Array of depth chart values
     * @return bool Success status
     */
    public function updatePlayerDepthChart(string $playerName, array $depthChartValues): bool
    {
        $playerNameEscaped = \Services\DatabaseService::escapeString($this->db, $playerName);
        
        // Sanitize and validate all numeric values
        $pg = (int) $depthChartValues['pg'];
        $sg = (int) $depthChartValues['sg'];
        $sf = (int) $depthChartValues['sf'];
        $pf = (int) $depthChartValues['pf'];
        $c = (int) $depthChartValues['c'];
        $active = (int) $depthChartValues['active'];
        $min = (int) $depthChartValues['min'];
        $of = (int) $depthChartValues['of'];
        $df = (int) $depthChartValues['df'];
        $oi = (int) $depthChartValues['oi'];
        $di = (int) $depthChartValues['di'];
        $bh = (int) $depthChartValues['bh'];
        
        $queries = [
            "UPDATE ibl_plr SET dc_PGDepth = $pg WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_SGDepth = $sg WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_SFDepth = $sf WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_PFDepth = $pf WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_CDepth = $c WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_active = $active WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_minutes = $min WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_of = $of WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_df = $df WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_oi = $oi WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_di = $di WHERE name = '$playerNameEscaped'",
            "UPDATE ibl_plr SET dc_bh = $bh WHERE name = '$playerNameEscaped'"
        ];
        
        foreach ($queries as $query) {
            if (!$this->db->sql_query($query)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Updates team history with depth chart submission timestamps
     * 
     * @param string $teamName Team name
     * @return bool Success status
     */
    public function updateTeamHistory(string $teamName): bool
    {
        $teamNameEscaped = \Services\DatabaseService::escapeString($this->db, $teamName);
        
        $queries = [
            "UPDATE ibl_team_history SET depth = NOW() WHERE team_name = '$teamNameEscaped'",
            "UPDATE ibl_team_history SET sim_depth = NOW() WHERE team_name = '$teamNameEscaped'"
        ];
        
        foreach ($queries as $query) {
            if (!$this->db->sql_query($query)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Gets board configuration
     * 
     * @return array Board configuration
     */
    public function getBoardConfig(): array
    {
        $sql = "SELECT * FROM nuke_bbconfig";
        $result = $this->db->sql_query($sql);
        $config = [];
        while ($row = $this->db->sql_fetchrow($result)) {
            $config[$row['config_name']] = $row['config_value'];
        }
        return $config;
    }
}
