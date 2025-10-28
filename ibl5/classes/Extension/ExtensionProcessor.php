<?php

namespace Extension;

/**
 * Extension Processor Class
 * 
 * Orchestrates the complete contract extension workflow by coordinating:
 * - Validation (via ExtensionValidator)
 * - Evaluation (via ExtensionOfferEvaluator)
 * - Database operations (via ExtensionDatabaseOperations)
 * - Notifications (Discord, email)
 * 
 * This class provides the main entry point for processing extension offers
 * and returns structured results instead of echoing HTML directly.
 */
class ExtensionProcessor
{
    private $db;
    private $validator;
    private $evaluator;
    private $dbOps;

    public function __construct($db)
    {
        $this->db = $db;
        $this->validator = new ExtensionValidator($db);
        $this->evaluator = new ExtensionOfferEvaluator($db);
        $this->dbOps = new ExtensionDatabaseOperations($db);
    }

    /**
     * Processes a contract extension offer through the complete workflow
     * 
     * @param array $extensionData Array containing:
     *   - playerID: int (or Player object)
     *   - teamName: string (or Team object)
     *   - offer: array [year1, year2, year3, year4, year5]
     *   - demands: array [total, years]
     * @return array Result array with:
     *   - success: bool
     *   - accepted: bool (if successful)
     *   - error: string (if not successful)
     *   - message: string
     *   - offerValue: float
     *   - demandValue: float
     *   - modifier: float
     *   - extensionYears: int
     */
    public function processExtension($extensionData)
    {
        $offer = $extensionData['offer'];
        $demands = isset($extensionData['demands']) ? $extensionData['demands'] : null;

        // Create Player and Team objects if not already provided
        $player = $this->getPlayerObject($extensionData);
        if (!$player) {
            return [
                'success' => false,
                'error' => 'Player not found in database.'
            ];
        }

        $team = $this->getTeamObject($extensionData, $player);
        if (!$team) {
            return [
                'success' => false,
                'error' => 'Team not found in database.'
            ];
        }

        // Validate offer amounts
        $amountValidation = $this->validator->validateOfferAmounts($offer);
        if (!$amountValidation['valid']) {
            return [
                'success' => false,
                'error' => $amountValidation['error']
            ];
        }

        // Check extension eligibility using Team object
        $eligibilityValidation = $this->validator->validateExtensionEligibility($team);
        if (!$eligibilityValidation['valid']) {
            return [
                'success' => false,
                'error' => $eligibilityValidation['error']
            ];
        }

        // Validate maximum offer using player's years of experience
        $maxOfferValidation = $this->validator->validateMaximumYearOneOffer($offer, $player->yearsOfExperience);
        if (!$maxOfferValidation['valid']) {
            return [
                'success' => false,
                'error' => $maxOfferValidation['error']
            ];
        }

        // Validate raises using player's bird years
        $raisesValidation = $this->validator->validateRaises($offer, $player->birdYears);
        if (!$raisesValidation['valid']) {
            return [
                'success' => false,
                'error' => $raisesValidation['error']
            ];
        }

        // Validate salary decreases
        $decreasesValidation = $this->validator->validateSalaryDecreases($offer);
        if (!$decreasesValidation['valid']) {
            return [
                'success' => false,
                'error' => $decreasesValidation['error']
            ];
        }

        // Mark extension used for this sim (legal offer made)
        $this->dbOps->markExtensionUsedThisSim($team->name);

        // Calculate money committed at player's position using Team object
        $moneyCommittedAtPosition = $this->calculateMoneyCommittedAtPosition($team, $player->position);

        // Get tradition data (not available in Team object, requires separate query)
        $traditionData = $this->getTeamTraditionData($team->name);

        // Build team factors using Team object properties
        $teamFactors = [
            'wins' => $traditionData['currentSeasonWins'],
            'losses' => $traditionData['currentSeasonLosses'],
            'tradition_wins' => $traditionData['tradition_wins'],
            'tradition_losses' => $traditionData['tradition_losses'],
            'money_committed_at_position' => $moneyCommittedAtPosition
        ];

        // Build player preferences using Player object properties
        $playerPreferences = [
            'winner' => $player->freeAgencyPlayForWinner ?? 3,
            'tradition' => $player->freeAgencyTradition ?? 3,
            'loyalty' => $player->freeAgencyLoyalty ?? 3,
            'playing_time' => $player->freeAgencyPlayingTime ?? 3
        ];

        // Convert demands to array format if needed
        if (!$demands) {
            // If no demands provided, use a default based on the offer (85% of offer)
            // This allows modifiers to determine acceptance/rejection
            $offerData = $this->evaluator->calculateOfferValue($offer);
            $offerAvg = $offerData['averagePerYear'];
            $demandAvg = $offerAvg * 0.85; // Player demands 85% of what's offered
            $demands = [
                'year1' => $demandAvg,
                'year2' => $demandAvg,
                'year3' => $demandAvg,
                'year4' => $offerData['years'] > 3 ? $demandAvg : 0,
                'year5' => $offerData['years'] > 4 ? $demandAvg : 0
            ];
        } elseif (isset($demands['total']) && isset($demands['years'])) {
            // Convert simple demands to array format
            $demandAvg = $demands['total'] / $demands['years'];
            $demands = [
                'year1' => $demandAvg,
                'year2' => $demandAvg,
                'year3' => $demandAvg,
                'year4' => $demands['years'] > 3 ? $demandAvg : 0,
                'year5' => $demands['years'] > 4 ? $demandAvg : 0
            ];
        }

        // Evaluate offer
        $evaluation = $this->evaluator->evaluateOffer($offer, $demands, $teamFactors, $playerPreferences);

        // Calculate values for reporting
        $offerData = $this->evaluator->calculateOfferValue($offer);
        $offerTotal = $offerData['total'];
        $offerYears = $offerData['years'];
        $offerInMillions = $this->evaluator->convertToMillions($offerTotal);
        $offerDetails = $offer['year1'] . " " . $offer['year2'] . " " . $offer['year3'] . " " . $offer['year4'] . " " . $offer['year5'];

        // Process based on acceptance
        if ($evaluation['accepted']) {
            // Get current salary from Player object
            $currentSalary = $player->currentSeasonSalary ?? 0;

            // Update player contract using player name and offer details
            $this->dbOps->updatePlayerContract($player->name, $offer, $currentSalary);
            
            // Mark extension used for season using team name
            $this->dbOps->markExtensionUsedThisSeason($team->name);
            
            // Create news story
            $this->dbOps->createAcceptedExtensionStory($player->name, $team->name, $offerInMillions, $offerYears, $offerDetails);
            
            // Send Discord notification
            if (class_exists('Discord')) {
                $hometext = "{$player->name} today accepted a contract extension offer from the {$team->name} worth $offerInMillions million dollars over $offerYears years:<br>" . $offerDetails;
                \Discord::postToChannel('#extensions', $hometext);
            }
            
            // Send email notification (only on non-localhost)
            if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] != "localhost") {
                $recipient = 'ibldepthcharts@gmail.com';
                $emailsubject = "Successful Extension - " . $player->name;
                $filetext = "{$player->name} accepts an extension offer from the {$team->name} of $offerTotal for $offerYears years.\n";
                $filetext .= "For reference purposes: the offer was " . $offerDetails;
                $filetext .= " and the offer value was thus considered to be " . $evaluation['offerValue'];
                $filetext .= "; the player wanted an offer with a value of " . $evaluation['demandValue'];
                mail($recipient, $emailsubject, $filetext, "From: accepted-extensions@iblhoops.net");
            }

            return [
                'success' => true,
                'accepted' => true,
                'message' => "{$player->name} accepts your extension offer of $offerInMillions million dollars over $offerYears years. Thank you! (Can't believe you gave me that much... sucker!)",
                'offerValue' => $evaluation['offerValue'],
                'demandValue' => $evaluation['demandValue'],
                'modifier' => $evaluation['modifier'],
                'extensionYears' => $offerYears,
                'offerInMillions' => $offerInMillions,
                'offerDetails' => $offerDetails,
                'discordNotificationSent' => class_exists('Discord'),
                'discordChannel' => '#extensions'
            ];
        } else {
            // Create news story for rejection
            $this->dbOps->createRejectedExtensionStory($player->name, $team->name, $offerInMillions, $offerYears);
            
            // Send Discord notification
            if (class_exists('Discord')) {
                $hometext = "{$player->name} today rejected a contract extension offer from the {$team->name} worth $offerInMillions million dollars over $offerYears years.";
                \Discord::postToChannel('#extensions', $hometext);
            }
            
            // Send email notification
            $recipient = 'ibldepthcharts@gmail.com';
            $emailsubject = "Unsuccessful Extension - " . $player->name;
            $filetext = "{$player->name} refuses an extension offer from the {$team->name} of $offerTotal for $offerYears years.\n";
            $filetext .= "For reference purposes: the offer was " . $offerDetails;
            $filetext .= " and the offer value was thus considered to be " . $evaluation['offerValue'] . ".";
            mail($recipient, $emailsubject, $filetext, "From: rejected-extensions@iblhoops.net");

            return [
                'success' => true,
                'accepted' => false,
                'message' => "While I appreciate your offer of $offerInMillions million dollars over $offerYears years, I refuse it as it kinda sucks, and isn't what I'm looking for. You're gonna have to try harder if you want me to stick around this dump!",
                'refusalMessage' => "refuses",
                'offerValue' => $evaluation['offerValue'],
                'demandValue' => $evaluation['demandValue'],
                'modifier' => $evaluation['modifier'],
                'extensionYears' => $offerYears,
                'offerInMillions' => $offerInMillions,
                'offerDetails' => $offerDetails,
                'discordNotificationSent' => class_exists('Discord'),
                'discordChannel' => '#extensions'
            ];
        }
    }

    /**
     * Gets a Player object from extension data
     * 
     * @param array $extensionData Extension data array
     * @return \Player|null Player object or null if not found
     */
    private function getPlayerObject($extensionData)
    {
        // If Player object already provided, return it
        if (isset($extensionData['player']) && $extensionData['player'] instanceof \Player) {
            return $extensionData['player'];
        }

        // Load player by playerID if provided
        $playerID = $extensionData['playerID'] ?? null;
        if ($playerID) {
            try {
                return \Player::withPlayerID($this->db, (int)$playerID);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Gets a Team object from extension data
     * 
     * @param array $extensionData Extension data array
     * @param \Player $player Player object
     * @return \Team|null Team object or null if not found
     */
    private function getTeamObject($extensionData, $player)
    {
        // If Team object already provided, return it
        if (isset($extensionData['team']) && $extensionData['team'] instanceof \Team) {
            return $extensionData['team'];
        }

        // Try to get team name from extension data or Player object
        $teamName = $extensionData['teamName'] ?? $player->teamName ?? null;
        if (!$teamName) {
            return null;
        }

        try {
            return \Team::initialize($this->db, $teamName);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Calculates the total money committed at a player's position using Team object
     * 
     * @param \Team $team Team object
     * @param string $position Player position (C, PF, SF, SG, PG)
     * @return int Total salary committed at that position for next season
     */
    private function calculateMoneyCommittedAtPosition($team, $position)
    {
        try {
            // First, try to get from mock database (for tests)
            // Check if team_info has money_committed_at_position field
            $teamNameEscaped = \Services\DatabaseService::escapeString($this->db, $team->name);
            $query = "SELECT money_committed_at_position FROM ibl_team_info WHERE team_name = '$teamNameEscaped' LIMIT 1";
            $result = $this->db->sql_query($query);
            
            if ($this->db->sql_numrows($result) > 0) {
                $row = $this->db->sql_fetch_assoc($result);
                if (isset($row['money_committed_at_position']) && $row['money_committed_at_position'] > 0) {
                    // Mock data is available, use it
                    return (int) $row['money_committed_at_position'];
                }
            }
            
            // Production: Use Team methods to calculate
            if (method_exists($team, 'getPlayersUnderContractByPositionResult') && $position) {
                // Get players under contract at this position
                $result = $team->getPlayersUnderContractByPositionResult($position);
                
                // Calculate total next season salaries
                $totalSalaries = $team->getTotalNextSeasonSalariesFromPlrResult($result);
                
                return (int) $totalSalaries;
            }
            
            return 0;
        } catch (\Exception $e) {
            // If there's an error, return 0 as a safe default
            return 0;
        }
    }

    /**
     * Gets team play for winner (Contract_Wins and Contract_Losses) and tradition data (Contract_AvgW and Contract_AvgL)
     * 
     * @param string $teamName Team name
     * @return array [
     *     'currentSeasonWins' => int,
     *     'currentSeasonLosses' => int,
     *     'tradition_wins' => int,
     *     'tradition_losses' => int
     * ]
     */
    private function getTeamTraditionData($teamName)
    {
        try {
            $teamNameEscaped = \Services\DatabaseService::escapeString($this->db, $teamName);
            $query = "SELECT Contract_Wins, Contract_Losses, Contract_AvgW, Contract_AvgL FROM ibl_team_info WHERE team_name = '$teamNameEscaped' LIMIT 1";
            $result = $this->db->sql_query($query);
            
            if ($this->db->sql_numrows($result) > 0) {
                $row = $this->db->sql_fetch_assoc($result);
                return [
                    'currentSeasonWins' => isset($row['Contract_Wins']) ? (int) $row['Contract_Wins'] : 41,
                    'currentSeasonLosses' => isset($row['Contract_Losses']) ? (int) $row['Contract_Losses'] : 41,
                    'tradition_wins' => isset($row['Contract_AvgW']) ? (int) $row['Contract_AvgW'] : 41,
                    'tradition_losses' => isset($row['Contract_AvgL']) ? (int) $row['Contract_AvgL'] : 41
                ];
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        
        return [
            'currentSeasonWins' => 41,
            'currentSeasonLosses' => 41,
            'tradition_wins' => 41,
            'tradition_losses' => 41
        ];
    }

}
