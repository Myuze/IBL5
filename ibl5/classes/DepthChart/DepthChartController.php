<?php

namespace DepthChart;

/**
 * Main controller for depth chart entry module
 */
class DepthChartController
{
    private $db;
    private $repository;
    private $processor;
    private $view;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->repository = new DepthChartRepository($db);
        $this->processor = new DepthChartProcessor();
        $this->view = new DepthChartView($this->processor);
    }
    
    /**
     * Displays the depth chart entry form
     * 
     * @param string $username Username of logged-in user
     * @return void Renders the form
     */
    public function displayForm(string $username): void
    {
        $sharedFunctions = new \Shared($this->db);
        $season = new \Season($this->db);
        
        // Get user's team information
        $teamName = $this->getUserTeamName($username);
        $teamID = $sharedFunctions->getTidFromTeamname($teamName);
        $team = \Team::initialize($this->db, $teamID);
        
        // Render header
        \Nuke\Header::header();
        OpenTable();
        \UI::displaytopmenu($this->db, $teamID);

        // Render team logo
        $this->view->renderTeamLogo($teamID);
        
        // Get team players
        $playersResult = $this->repository->getPlayersOnTeam($teamName, $teamID);
        
        // Display ratings table
        $table_ratings = \UI::ratings($this->db, $playersResult, $team, "", $season);
        echo $table_ratings;
        
        // Render form header with standard position names
        $slotNames = \JSB::PLAYER_POSITIONS;
        
        $this->view->renderFormHeader($teamName, $teamID, $slotNames);
        
        // Render player rows
        $depthCount = 1;
        mysqli_data_seek($playersResult, 0);
        while ($player = $this->db->sql_fetchrow($playersResult)) {
            $this->view->renderPlayerRow($player, $depthCount);
            $depthCount++;
        }
        
        // Render form footer
        $this->view->renderFormFooter();
        
        CloseTable();
        \Nuke\Footer::footer();
    }
    
    /**
     * Gets team name from username
     * 
     * @param string $username Username
     * @return string Team name
     */
    private function getUserTeamName(string $username): string
    {
        $usernameEscaped = \Services\DatabaseService::escapeString($this->db, $username);
        $sql = "SELECT user_ibl_team FROM nuke_users WHERE username='$usernameEscaped'";
        $result = $this->db->sql_query($sql);
        $userinfo = $this->db->sql_fetchrow($result);
        return $userinfo['user_ibl_team'] ?? '';
    }
}
