<?php

namespace Waivers;

use Player\Player;

/**
 * Main controller for waiver wire operations
 */
class WaiversController
{
    // Configuration constants
    private const DISCORD_BUGS_CHANNEL_URL = 'https://discord.com/channels/666986450889474053/671435182502576169';
    private const NOTIFICATION_EMAIL_RECIPIENT = 'ibldepthcharts@gmail.com';
    private const NOTIFICATION_EMAIL_SENDER = 'waivers@iblhoops.net';
    public const WAIVER_POOL_MOVES_CATEGORY_ID = 1;
    
    private $db;
    private $repository;
    private $processor;
    private $validator;
    private $view;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->repository = new WaiversRepository($db);
        $this->processor = new WaiversProcessor();
        $this->validator = new WaiversValidator();
        $this->view = new WaiversView();
    }
    
    /**
     * Main entry point for waiver operations
     * 
     * @param mixed $user Current user
     * @param string $action Action to perform (add or drop)
     */
    public function handleWaiverRequest($user, string $action): void
    {
        $season = new \Season($this->db);
        
        if (!is_user($user)) {
            $this->handleNotLoggedIn();
            return;
        }
        
        if ($season->allowWaivers !== "Yes") {
            $this->view->renderWaiversClosed();
            return;
        }
        
        global $cookie;
        $this->executeWaiverOperation($cookie[1], $action);
    }
    
    /**
     * Handles not logged in state
     */
    private function handleNotLoggedIn(): void
    {
        global $stop;
        
        $message = $stop ? _LOGININCOR : _USERREGLOGIN;
        $this->view->renderNotLoggedIn($message);
    }
    
    /**
     * Executes waiver wire operations (add or drop)
     * 
     * @param string $username Username
     * @param string $action Action to perform
     */
    public function executeWaiverOperation(string $username, string $action): void
    {
        $userInfo = $this->repository->getUserByUsername($username);
        
        if (!$userInfo) {
            $this->view->renderNotLoggedIn(_USERREGLOGIN);
            return;
        }
        
        $errorMessage = '';
        
        // Process submission if data was posted
        if (isset($_POST['Action']) && ($_POST['Action'] === 'add' || $_POST['Action'] === 'drop')) {
            $errorMessage = $this->processWaiverSubmission($_POST);
        }
        
        // Display the waiver form
        $this->displayWaiverForm($userInfo, $action, $errorMessage);
    }
    
    /**
     * Processes a waiver wire submission
     * 
     * @param array $postData POST data
     * @return string Error or success message
     */
    private function processWaiverSubmission(array $postData): string
    {
        $teamName = $postData['Team_Name'] ?? '';
        $action = $postData['Action'] ?? '';
        $playerID = isset($postData['Player_ID']) ? (int) $postData['Player_ID'] : null;
        $rosterSlots = isset($postData['rosterslots']) ? (int) $postData['rosterslots'] : 0;
        $healthyRosterSlots = isset($postData['healthyrosterslots']) ? (int) $postData['healthyrosterslots'] : 0;
        
        if (empty($teamName) || !in_array($action, ['add', 'drop'])) {
            return "Invalid submission data.";
        }
        
        $totalSalary = $this->repository->getTeamTotalSalary($teamName);
        
        if ($action === 'drop') {
            return $this->processDrop($playerID, $teamName, $rosterSlots, $totalSalary);
        } else {
            return $this->processAdd($playerID, $teamName, $healthyRosterSlots, $totalSalary);
        }
    }
    
    /**
     * Processes dropping a player to waivers
     * 
     * @param int|null $playerID Player ID
     * @param string $teamName Team name
     * @param int $rosterSlots Roster slots
     * @param int $totalSalary Total salary
     * @return string Status message
     */
    private function processDrop(?int $playerID, string $teamName, int $rosterSlots, int $totalSalary): string
    {
        if (!$this->validator->validateDrop($rosterSlots, $totalSalary)) {
            return implode(' ', $this->validator->getErrors());
        }
        
        if ($playerID === null || $playerID === 0) {
            return "You didn't select a valid player. Please select a player and try again.";
        }
        
        $player = $this->repository->getPlayerByID($playerID);
        if (!$player) {
            return "Player not found.";
        }
        
        $timestamp = time();
        
        if (!$this->repository->dropPlayerToWaivers($playerID, $timestamp)) {
            return "Failed to drop player to waivers. Please try again.";
        }
        
        // Create news story
        $this->createWaiverNewsStory($teamName, $player['name'], 'drop', '');
        
        // Send Discord notification
        $hometext = "The " . $teamName . " cut " . $player['name'] . " to waivers.";
        \Discord::postToChannel('#waiver-wire', $hometext);
        
        return "Your waiver move should now be processed. " . $player['name'] . " has been cut to waivers.";
    }
    
    /**
     * Processes adding a player from waivers
     * 
     * @param int|null $playerID Player ID
     * @param string $teamName Team name
     * @param int $healthyRosterSlots Healthy roster slots available
     * @param int $totalSalary Total salary
     * @return string Status message
     */
    private function processAdd(?int $playerID, string $teamName, int $healthyRosterSlots, int $totalSalary): string
    {
        if ($playerID === null || $playerID === 0) {
            return "You didn't select a valid player. Please select a player and try again.";
        }
        
        $player = $this->repository->getPlayerByID($playerID);
        if (!$player) {
            return "Player not found.";
        }
        
        $contractData = $this->processor->prepareContractData($player);
        $playerSalary = isset($contractData['cy1']) ? (int) $contractData['cy1'] : (int) ($player['cy1'] ?? 0);
        
        if (!$this->validator->validateAdd($playerID, $healthyRosterSlots, $totalSalary, $playerSalary)) {
            return implode(' ', $this->validator->getErrors());
        }
        
        $team = $this->repository->getTeamByName($teamName);
        if (!$team) {
            return "Team not found.";
        }
        
        $teamID = (int) $team['teamid'];
        
        if (!$this->repository->signPlayerFromWaivers($playerID, $teamName, $teamID, $contractData)) {
            return "Oops, something went wrong. Post what you were trying to do in <A HREF=\"" . self::DISCORD_BUGS_CHANNEL_URL . "\">#site-bugs-and-to-do</A> and we'll fix it asap. Sorry!";
        }
        
        // Create news story
        $this->createWaiverNewsStory($teamName, $player['name'], 'add', $contractData['finalContract']);
        
        // Send email notification
        $storytitle = $teamName . " make waiver additions";
        $hometext = "The " . $teamName . " sign " . $player['name'] . " from waivers for " . $contractData['finalContract'] . ".";
        mail(self::NOTIFICATION_EMAIL_RECIPIENT, $storytitle, $hometext, "From: " . self::NOTIFICATION_EMAIL_SENDER);
        
        // Send Discord notification
        \Discord::postToChannel('#waiver-wire', $hometext);
        
        return "Your waiver move should now be processed. " . $player['name'] . " has been signed from waivers and added to your roster.";
    }
    
    /**
     * Creates a news story for a waiver transaction
     * 
     * @param string $teamName Team name
     * @param string $playerName Player name
     * @param string $action Action (add or drop)
     * @param string $contract Contract (for adds)
     */
    private function createWaiverNewsStory(string $teamName, string $playerName, string $action, string $contract): void
    {
        $this->repository->incrementWaiverPoolMovesCounter();
        
        if ($action === 'drop') {
            $topicID = 32;
            $storytitle = $teamName . " make waiver cuts";
            $hometext = "The " . $teamName . " cut " . $playerName . " to waivers.";
        } else {
            $topicID = 33;
            $storytitle = $teamName . " make waiver additions";
            $hometext = "The " . $teamName . " sign " . $playerName . " from waivers for " . $contract . ".";
        }
        
        $this->repository->createNewsStory($topicID, $storytitle, $hometext);
    }
    
    /**
     * Displays the waiver wire form
     * 
     * @param array $userInfo User information
     * @param string $action Action (add or drop)
     * @param string $errorMessage Error message to display
     */
    private function displayWaiverForm(array $userInfo, string $action, string $errorMessage): void
    {
        \Nuke\Header::header();
        OpenTable();
        
        $team = \Team::initialize($this->db, $userInfo['user_ibl_team']);
        \UI::displaytopmenu($this->db, $team->teamID);
        
        $players = $this->getPlayersForAction($team, $action);
        
        $openRosterSpots = 15 - $team->getHealthyAndInjuredPlayersOrderedByNameResult()->num_rows;
        $healthyOpenRosterSpots = 15 - $team->getHealthyPlayersOrderedByNameResult()->num_rows;
        
        $this->view->renderWaiverForm(
            $team->name,
            $team->teamID,
            $action,
            $players,
            $openRosterSpots,
            $healthyOpenRosterSpots,
            $errorMessage
        );
        
        // Display player ratings table
        $league = new \League($this->db);
        $season = new \Season($this->db);
        
        if ($action === 'drop') {
            $result = $team->getHealthyAndInjuredPlayersOrderedByNameResult();
        } else {
            $result = $league->getWaivedPlayersResult();
        }
        
        $teamFreeAgency = \Team::initialize($this->db, \League::FREE_AGENTS_TEAMID);
        $tableRatings = \UI::ratings($this->db, $result, $teamFreeAgency, "", $season);
        echo $tableRatings;
        
        CloseTable();
        \Nuke\Footer::footer();
    }
    
    /**
     * Gets players for dropdown based on action
     * 
     * @param object $team Team object
     * @param string $action Action (add or drop)
     * @return array Array of player option data
     */
    private function getPlayersForAction($team, string $action): array
    {
        $league = new \League($this->db);
        $timeNow = time();
        $players = [];
        
        if ($action === 'drop') {
            $result = $team->getHealthyAndInjuredPlayersOrderedByNameResult();
        } else {
            $result = $league->getWaivedPlayersResult();
        }
        
        while ($playerRow = $this->db->sql_fetchrow($result)) {
            $player = Player::withPlrRow($this->db, $playerRow);
            $contract = $this->processor->getPlayerContractDisplay($playerRow);
            $waitTime = '';
            
            if ($action === 'add' && $player->timeDroppedOnWaivers > 0) {
                $waitTime = $this->processor->getWaiverWaitTime($player->timeDroppedOnWaivers, $timeNow);
            }
            
            $players[] = $this->view->buildPlayerOption(
                $player->playerID,
                $player->name,
                $contract,
                $waitTime
            );
        }
        
        return $players;
    }
}
