<?php

namespace Player;

/**
 * PlayerRepository - Handles data loading and persistence for players
 * 
 * This class follows the Repository pattern, encapsulating data access logic.
 * It's responsible for loading player data from the database and populating PlayerData objects.
 */
class PlayerRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Load a player by their ID from the current player table
     */
    public function loadByID(int $playerID): PlayerData
    {
        $query = "SELECT *
            FROM ibl_plr
            WHERE pid = $playerID
            LIMIT 1;";
        $result = $this->db->sql_query($query);
        $plrRow = $this->db->sql_fetch_assoc($result);
        return $this->fillFromCurrentRow($plrRow);
    }

    /**
     * Fill a PlayerData object from a current player row
     */
    public function fillFromCurrentRow(array $plrRow): PlayerData
    {
        $playerData = new PlayerData();

        $playerData->playerID = $plrRow['pid'];
        $playerData->ordinal = $plrRow['ordinal'];
        $playerData->name = stripslashes($plrRow['name']); // Remove SQL escaping
        $playerData->nickname = (isset($plrRow['nickname']) && $plrRow['nickname'] !== '') ? stripslashes($plrRow['nickname']) : null; // Remove SQL escaping
        $playerData->age = $plrRow['age'];

        $playerData->teamID = $plrRow['tid'];
        $playerData->teamName = stripslashes($plrRow['teamname']); // Remove SQL escaping
        $playerData->position = $plrRow['pos'];
        
        $playerData->ratingFieldGoalAttempts = $plrRow['r_fga'];
        $playerData->ratingFieldGoalPercentage = $plrRow['r_fgp'];
        $playerData->ratingFreeThrowAttempts = $plrRow['r_fta'];
        $playerData->ratingFreeThrowPercentage = $plrRow['r_ftp'];
        $playerData->ratingThreePointAttempts = $plrRow['r_tga'];
        $playerData->ratingThreePointPercentage = $plrRow['r_tgp'];
        $playerData->ratingOffensiveRebounds = $plrRow['r_orb'];
        $playerData->ratingDefensiveRebounds = $plrRow['r_drb'];
        $playerData->ratingAssists = $plrRow['r_ast'];
        $playerData->ratingSteals = $plrRow['r_stl'];
        $playerData->ratingTurnovers = $plrRow['r_to'];
        $playerData->ratingBlocks = $plrRow['r_blk'];
        $playerData->ratingFouls = $plrRow['r_foul'];
        $playerData->ratingOutsideOffense = $plrRow['oo'];
        $playerData->ratingOutsideDefense = $plrRow['od'];
        $playerData->ratingDriveOffense = $plrRow['do'];
        $playerData->ratingDriveDefense = $plrRow['dd'];
        $playerData->ratingPostOffense = $plrRow['po'];
        $playerData->ratingPostDefense = $plrRow['pd'];
        $playerData->ratingTransitionOffense = $plrRow['to'];
        $playerData->ratingTransitionDefense = $plrRow['td'];
        $playerData->ratingClutch = $plrRow['Clutch'];
        $playerData->ratingConsistency = $plrRow['Consistency'];
        $playerData->ratingTalent = $plrRow['talent'];
        $playerData->ratingSkill = $plrRow['skill'];
        $playerData->ratingIntangibles = $plrRow['intangibles'];

        $playerData->freeAgencyLoyalty = $plrRow['loyalty'];
        $playerData->freeAgencyPlayingTime = $plrRow['playingTime'];
        $playerData->freeAgencyPlayForWinner = $plrRow['winner'];
        $playerData->freeAgencyTradition = $plrRow['tradition'];
        $playerData->freeAgencySecurity = $plrRow['security'];

        $playerData->yearsOfExperience = $plrRow['exp'];
        $playerData->birdYears = $plrRow['bird'];
        $playerData->contractCurrentYear = $plrRow['cy'];
        $playerData->contractTotalYears = $plrRow['cyt'];
        $playerData->contractYear1Salary = $plrRow['cy1'];
        $playerData->contractYear2Salary = $plrRow['cy2'];
        $playerData->contractYear3Salary = $plrRow['cy3'];
        $playerData->contractYear4Salary = $plrRow['cy4'];
        $playerData->contractYear5Salary = $plrRow['cy5'];
        $playerData->contractYear6Salary = $plrRow['cy6'];
    
        $playerData->draftYear = $plrRow['draftyear'];
        $playerData->draftRound = $plrRow['draftround'];
        $playerData->draftPickNumber = $plrRow['draftpickno'];
        $playerData->draftTeamOriginalName = (isset($plrRow['draftedby']) && $plrRow['draftedby'] !== '') ? stripslashes($plrRow['draftedby']) : null; // Remove SQL escaping
        $playerData->draftTeamCurrentName = (isset($plrRow['draftedbycurrentname']) && $plrRow['draftedbycurrentname'] !== '') ? stripslashes($plrRow['draftedbycurrentname']) : null; // Remove SQL escaping
        $playerData->collegeName = (isset($plrRow['college']) && $plrRow['college'] !== '') ? stripslashes($plrRow['college']) : null; // Remove SQL escaping
    
        $playerData->daysRemainingForInjury = $plrRow['injured'];
    
        $playerData->heightFeet = $plrRow['htft'];
        $playerData->heightInches = $plrRow['htin'];
        $playerData->weightPounds = $plrRow['wt'];
    
        $playerData->isRetired = $plrRow['retired'];
    
        $playerData->timeDroppedOnWaivers = $plrRow['droptime'];

        return $playerData;
    }

    /**
     * Fill a PlayerData object from a historical player row
     */
    public function fillFromHistoricalRow(array $plrRow): PlayerData
    {
        $playerData = new PlayerData();

        $playerData->playerID = $plrRow['pid'];
        $playerData->historicalYear = $plrRow['year'];
        $playerData->name = stripslashes($plrRow['name']); // Remove SQL escaping

        $playerData->teamName = stripslashes($plrRow['team']); // Remove SQL escaping
        $playerData->teamID = $plrRow['teamid'];
        
        $playerData->ratingFieldGoalAttempts = $plrRow['r_2ga'];
        $playerData->ratingFieldGoalPercentage = $plrRow['r_2gp'];
        $playerData->ratingFreeThrowAttempts = $plrRow['r_fta'];
        $playerData->ratingFreeThrowPercentage = $plrRow['r_ftp'];
        $playerData->ratingThreePointAttempts = $plrRow['r_3ga'];
        $playerData->ratingThreePointPercentage = $plrRow['r_3gp'];
        $playerData->ratingOffensiveRebounds = $plrRow['r_orb'];
        $playerData->ratingDefensiveRebounds = $plrRow['r_drb'];
        $playerData->ratingAssists = $plrRow['r_ast'];
        $playerData->ratingSteals = $plrRow['r_stl'];
        $playerData->ratingBlocks = $plrRow['r_blk'];
        $playerData->ratingTurnovers = $plrRow['r_tvr'];

        $playerData->ratingOutsideOffense = $plrRow['r_oo'];
        $playerData->ratingOutsideDefense = $plrRow['r_od'];
        $playerData->ratingDriveOffense = $plrRow['r_do'];
        $playerData->ratingDriveDefense = $plrRow['r_dd'];
        $playerData->ratingPostOffense = $plrRow['r_po'];
        $playerData->ratingPostDefense = $plrRow['r_pd'];
        $playerData->ratingTransitionOffense = $plrRow['r_to'];
        $playerData->ratingTransitionDefense = $plrRow['r_td'];

        $playerData->salaryJSB = $plrRow['salary'];

        return $playerData;
    }

    /**
     * Query free agency demands for a player
     */
    public function getFreeAgencyDemands(string $playerName)
    {
        $query = "SELECT *
            FROM ibl_demands
            WHERE name='$playerName'";
        return $this->db->sql_query($query);
    }
}
