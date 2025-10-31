<?php

use Player\Player;

class Team
{
    protected $db;

    public $teamID;

    public $city;
    public $name;
    public $color1;
    public $color2;
    public $arena;
    public $capacity;
    public $formerlyKnownAs;

    public $ownerName;
    public $ownerEmail;
    public $discordID;

    public $hasUsedExtensionThisSim;
    public $hasUsedExtensionThisSeason;
    public $hasMLE;
    public $hasLLE;

    public $numberOfPlayers;
    public $numberOfHealthyPlayers;
    public $numberOfOpenRosterSpots;
    public $numberOfHealthyOpenRosterSpots;

    public $seasonRecord;

    const BUYOUT_PERCENTAGE_MAX = 0.40;

    public function __construct()
    {
    }

    public static function initialize($db, $identifier)
    {
        $instance = new self();
        $instance->load($db, $identifier);
        return $instance;
    }

    protected function load($db, $identifier)
    {
        ($identifier) ? $identifier : $identifier = League::FREE_AGENTS_TEAMID;

        if (is_numeric($identifier)) {
            $teamID = (int) $identifier; // Ensure teamID is an integer
            $joinWhereCondition = "ibl_team_info.teamid = $teamID";
        } elseif (is_string($identifier)) {
            $joinWhereCondition = "ibl_team_info.team_name = '$identifier'";
        } elseif (is_array($identifier)) {
            $this->fill($db, $identifier);
            return $this;
        }

        $query = "SELECT * 
            FROM ibl_team_info
                LEFT JOIN ibl_standings
                ON ibl_team_info.teamid = ibl_standings.tid
            WHERE $joinWhereCondition
            LIMIT 1;";
        $result = $db->sql_query($query);
        $teamRow = $db->sql_fetch_assoc($result);
        $this->fill($db, $teamRow);
    }

    protected function fill($db, array $teamRow)
    {
        $this->db = $db;

        $this->teamID = (int) $teamRow['teamid']; // Ensure teamID is an integer

        $this->city = $teamRow['team_city'];
        $this->name = $teamRow['team_name'];
        $this->color1 = $teamRow['color1'];
        $this->color2 = $teamRow['color2'];
        $this->arena = $teamRow['arena'];
        $this->capacity = $teamRow['capacity'];
        $this->formerlyKnownAs = $teamRow['formerly_known_as'];
    
        $this->ownerName = $teamRow['owner_name'];
        $this->ownerEmail = $teamRow['owner_email'];
        $this->discordID = $teamRow['discordID'];
    
        $this->hasUsedExtensionThisSim = $teamRow['Used_Extension_This_Chunk'];
        $this->hasUsedExtensionThisSeason = $teamRow['Used_Extension_This_Season'];
        $this->hasMLE = $teamRow['HasMLE'];
        $this->hasLLE = $teamRow['HasLLE'];

        $this->seasonRecord = $teamRow['leagueRecord']; // only available if the object is not loaded from an array
    }

    public function getBuyoutsResult()
    {
        $query = "SELECT *
            FROM ibl_plr
            WHERE tid = '$this->teamID'
              AND name LIKE '%Buyout%'
            ORDER BY name ASC";
        $result = $this->db->sql_query($query);
        return $result;
    }
    
    public function getDraftHistoryResult()
    {
        $query = "SELECT *
            FROM ibl_plr
            WHERE draftedby
             LIKE '$this->name'
            ORDER BY draftyear DESC,
                     draftround,
                     draftpickno ASC";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getDraftPicksResult()
    {
        $query = "SELECT *
            FROM ibl_draft_picks
            WHERE ownerofpick = '$this->name'
            ORDER BY year, round, teampick ASC;";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getFreeAgencyOffersResult()
    {
        $query = "SELECT *
            FROM ibl_fa_offers
            WHERE team = '$this->name'
            ORDER BY name ASC";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getFreeAgencyRosterOrderedByNameResult()
    {
        $query = "SELECT *
            FROM ibl_plr
            WHERE tid = '$this->teamID'
              AND retired = 0
              AND cyt != cy
            ORDER BY name ASC";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getHealthyAndInjuredPlayersOrderedByNameResult()
    {
        $query = "SELECT *
            FROM ibl_plr
            WHERE teamname = '$this->name'
              AND retired = '0'
              AND ordinal <= '" . JSB::WAIVERS_ORDINAL . "'
            ORDER BY name ASC";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getHealthyPlayersOrderedByNameResult()
    {
        $query = "SELECT *
            FROM ibl_plr
            WHERE teamname = '$this->name'
              AND retired = '0'
              AND ordinal <= '" . JSB::WAIVERS_ORDINAL ."'
              AND injured = '0'
            ORDER BY name ASC";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getLastSimStarterPlayerIDForPosition(string $position)
    {
        $query = "SELECT pid
            FROM ibl_plr
            WHERE tid = $this->teamID
              AND retired = 0
              AND " . $position . "Depth = 1";
        $result = $this->db->sql_query($query);
        return $this->db->sql_result($result, 0, 'pid');
    }

    public function getCurrentlySetStarterPlayerIDForPosition(string $position)
    {
        $query = "SELECT pid
            FROM ibl_plr
            WHERE tid = $this->teamID
              AND retired = 0
              AND dc_" . $position . "Depth = 1";
        $result = $this->db->sql_query($query);
        return $this->db->sql_result($result, 0, 'pid');
    }

    public function getPlayersUnderContractByPositionResult($position)
    {
        $query = "SELECT * 
            FROM ibl_plr
            WHERE teamname = '$this->name'
              AND pos = '$position'
              AND cy1 != 0
              AND retired = 0";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getRosterUnderContractOrderedByNameResult()
    {
        $query = "SELECT *
            FROM ibl_plr
            WHERE tid = '$this->teamID'
              AND retired = 0
            ORDER BY name ASC";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getRosterUnderContractOrderedByOrdinalResult()
    {
        $query = "SELECT *
            FROM ibl_plr
            WHERE tid = '$this->teamID'
              AND retired = 0
            ORDER BY ordinal ASC";
        $result = $this->db->sql_query($query);
        return $result;
    }

    public function getSalaryCapArray(Season $season)
    {  
        $salaryCapSpent[] = 0;
        $resultContracts = $this->getRosterUnderContractOrderedByNameResult();
    
        foreach ($resultContracts as $contract) {
            $yearUnderContract = $contract['cy'];
            if ($season->phase == "Free Agency") {
                $yearUnderContract++;
            }
            
            $i = 1;
            while ($yearUnderContract <= $contract['cyt']) {
                $fieldString = "cy" . $yearUnderContract;
                $salaryCapSpent["year" . $i] += $contract["$fieldString"];
                $yearUnderContract++;
                $i++;
            }
        }

        return $salaryCapSpent;
    }

    public function getTotalCurrentSeasonSalariesFromPlrResult($result)
    {
        $totalCurrentSeasonSalaries = 0;

        $playerArray = $this->convertPlrResultIntoPlayerArray($result);
        foreach ($playerArray as $player) {
            $totalCurrentSeasonSalaries += $player->getCurrentSeasonSalary();
        }
        return $totalCurrentSeasonSalaries;
    }

    public function getTotalNextSeasonSalariesFromPlrResult($result)
    {
        $totalNextSeasonSalaries = 0;

        $playerArray = $this->convertPlrResultIntoPlayerArray($result);
        foreach ($playerArray as $player) {
            $totalNextSeasonSalaries += $player->getNextSeasonSalary();
        }
        return $totalNextSeasonSalaries;
    }

    public function canAddContractWithoutGoingOverHardCap($currentSeasonContractValueToBeAdded)
    {
        $teamResult = $this->getRosterUnderContractOrderedByNameResult();
        $totalCurrentSeasonSalaries = $this->getTotalCurrentSeasonSalariesFromPlrResult($teamResult);
        $projectedTotalCurrentSeasonSalaries = $totalCurrentSeasonSalaries + $currentSeasonContractValueToBeAdded;

        if ($projectedTotalCurrentSeasonSalaries <= League::HARD_CAP_MAX) {
            return TRUE;
        }
        return FALSE;
    }

    public function canAddBuyoutWithoutExceedingBuyoutLimit($currentSeasonBuyoutValueToBeAdded)
    {
        $buyoutsResult = $this->getBuyoutsResult();
        $totalCurrentSeasonBuyouts = $this->getTotalCurrentSeasonSalariesFromPlrResult($buyoutsResult);
        $projectedTotalCurrentSeasonBuyouts = $totalCurrentSeasonBuyouts + $currentSeasonBuyoutValueToBeAdded;
        $buyoutLimit = League::HARD_CAP_MAX * self::BUYOUT_PERCENTAGE_MAX;

        if ($projectedTotalCurrentSeasonBuyouts <= $buyoutLimit) {
            return TRUE;
        }
        return FALSE;
    }

    public function convertPlrResultIntoPlayerArray($result)
    {
        $array = array();
        foreach ($result as $plrRow) {
            $playerID = $plrRow['pid'];
            $array[$playerID] = Player::withPlayerID($this->db, $playerID);
        }
        return $array;
    }
}