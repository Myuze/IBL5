<?php

namespace Player;

/**
 * Player - Facade for player-related operations
 * 
 * This class now acts as a facade, delegating responsibilities to specialized classes
 * while maintaining backward compatibility with existing code.
 */
class Player
{
    protected $db;
    protected $playerData;
    protected $repository;
    protected $contractCalculator;
    protected $contractValidator;
    protected $nameDecorator;
    protected $injuryCalculator;

    // Keep all public properties for backward compatibility
    public $playerID;
    public $plr;

    public $ordinal;
    public $name;
    public $nickname;
    public $age;
    public $historicalYear;

    public $teamID;
    public $teamName;
    public $position;

    public $ratingFieldGoalAttempts;
    public $ratingFieldGoalPercentage;
    public $ratingFreeThrowAttempts;
    public $ratingFreeThrowPercentage;
    public $ratingThreePointAttempts;
    public $ratingThreePointPercentage;
    public $ratingOffensiveRebounds;
    public $ratingDefensiveRebounds;
    public $ratingAssists;
    public $ratingSteals;
    public $ratingTurnovers;
    public $ratingBlocks;
    public $ratingFouls;
    public $ratingOutsideOffense;
    public $ratingOutsideDefense;
    public $ratingDriveOffense;
    public $ratingDriveDefense;
    public $ratingPostOffense;
    public $ratingPostDefense;
    public $ratingTransitionOffense;
    public $ratingTransitionDefense;
    public $ratingClutch;
    public $ratingConsistency;
    public $ratingTalent;
    public $ratingSkill;
    public $ratingIntangibles;

    public $freeAgencyLoyalty;
    public $freeAgencyPlayingTime;
    public $freeAgencyPlayForWinner;
    public $freeAgencyTradition;
    public $freeAgencySecurity;

    public $yearsOfExperience;
    public $birdYears;
    public $contractCurrentYear;
    public $contractTotalYears;
    public $contractYear1Salary;
    public $contractYear2Salary;
    public $contractYear3Salary;
    public $contractYear4Salary;
    public $contractYear5Salary;
    public $contractYear6Salary;
    public $currentSeasonSalary;
    public $salaryJSB;

    public $draftYear;
    public $draftRound;
    public $draftPickNumber;
    public $draftTeamOriginalName;
    public $draftTeamCurrentName;
    public $collegeName;

    public $daysRemainingForInjury;

    public $heightFeet;
    public $heightInches;
    public $weightPounds;

    public $isRetired;

    public $timeDroppedOnWaivers;
    
    public $decoratedName;

    public function __construct()
    {
        $this->contractCalculator = new PlayerContractCalculator();
        $this->contractValidator = new PlayerContractValidator();
        $this->nameDecorator = new PlayerNameDecorator();
        $this->injuryCalculator = new PlayerInjuryCalculator();
    }

    public static function withPlayerID($db, int $playerID)
    {
        $instance = new self();
        $instance->loadByID($db, $playerID);
        return $instance;
    }

    public static function withPlrRow($db, array $plrRow)
    {
        $instance = new self();
        $instance->fill($db, $plrRow);
        return $instance;
    }

    public static function withHistoricalPlrRow($db, array $plrRow)
    {
        $instance = new self();
        $instance->fillHistorical($db, $plrRow);
        return $instance;
    }

    protected function loadByID($db, int $playerID)
    {
        $this->db = $db;
        $this->repository = new PlayerRepository($db);
        $this->playerData = $this->repository->loadByID($playerID);
        $this->syncPropertiesFromPlayerData();
    }

    protected function fill($db, array $plrRow)
    {
        $this->db = $db;
        $this->repository = new PlayerRepository($db);
        $this->playerData = $this->repository->fillFromCurrentRow($plrRow);
        $this->syncPropertiesFromPlayerData();
    }

    protected function fillHistorical($db, array $plrRow)
    {
        $this->db = $db;
        $this->repository = new PlayerRepository($db);
        $this->playerData = $this->repository->fillFromHistoricalRow($plrRow);
        $this->syncPropertiesFromPlayerData();
    }

    /**
     * Sync all public properties from PlayerData for backward compatibility
     */
    protected function syncPropertiesFromPlayerData()
    {
        $this->playerID = $this->playerData->playerID;
        $this->ordinal = $this->playerData->ordinal;
        $this->name = $this->playerData->name;
        $this->nickname = $this->playerData->nickname;
        $this->age = $this->playerData->age;
        $this->historicalYear = $this->playerData->historicalYear;

        $this->teamID = $this->playerData->teamID;
        $this->teamName = $this->playerData->teamName;
        $this->position = $this->playerData->position;
        
        $this->ratingFieldGoalAttempts = $this->playerData->ratingFieldGoalAttempts;
        $this->ratingFieldGoalPercentage = $this->playerData->ratingFieldGoalPercentage;
        $this->ratingFreeThrowAttempts = $this->playerData->ratingFreeThrowAttempts;
        $this->ratingFreeThrowPercentage = $this->playerData->ratingFreeThrowPercentage;
        $this->ratingThreePointAttempts = $this->playerData->ratingThreePointAttempts;
        $this->ratingThreePointPercentage = $this->playerData->ratingThreePointPercentage;
        $this->ratingOffensiveRebounds = $this->playerData->ratingOffensiveRebounds;
        $this->ratingDefensiveRebounds = $this->playerData->ratingDefensiveRebounds;
        $this->ratingAssists = $this->playerData->ratingAssists;
        $this->ratingSteals = $this->playerData->ratingSteals;
        $this->ratingTurnovers = $this->playerData->ratingTurnovers;
        $this->ratingBlocks = $this->playerData->ratingBlocks;
        $this->ratingFouls = $this->playerData->ratingFouls;
        $this->ratingOutsideOffense = $this->playerData->ratingOutsideOffense;
        $this->ratingOutsideDefense = $this->playerData->ratingOutsideDefense;
        $this->ratingDriveOffense = $this->playerData->ratingDriveOffense;
        $this->ratingDriveDefense = $this->playerData->ratingDriveDefense;
        $this->ratingPostOffense = $this->playerData->ratingPostOffense;
        $this->ratingPostDefense = $this->playerData->ratingPostDefense;
        $this->ratingTransitionOffense = $this->playerData->ratingTransitionOffense;
        $this->ratingTransitionDefense = $this->playerData->ratingTransitionDefense;
        $this->ratingClutch = $this->playerData->ratingClutch;
        $this->ratingConsistency = $this->playerData->ratingConsistency;
        $this->ratingTalent = $this->playerData->ratingTalent;
        $this->ratingSkill = $this->playerData->ratingSkill;
        $this->ratingIntangibles = $this->playerData->ratingIntangibles;

        $this->freeAgencyLoyalty = $this->playerData->freeAgencyLoyalty;
        $this->freeAgencyPlayingTime = $this->playerData->freeAgencyPlayingTime;
        $this->freeAgencyPlayForWinner = $this->playerData->freeAgencyPlayForWinner;
        $this->freeAgencyTradition = $this->playerData->freeAgencyTradition;
        $this->freeAgencySecurity = $this->playerData->freeAgencySecurity;

        $this->yearsOfExperience = $this->playerData->yearsOfExperience;
        $this->birdYears = $this->playerData->birdYears;
        $this->contractCurrentYear = $this->playerData->contractCurrentYear;
        $this->contractTotalYears = $this->playerData->contractTotalYears;
        $this->contractYear1Salary = $this->playerData->contractYear1Salary;
        $this->contractYear2Salary = $this->playerData->contractYear2Salary;
        $this->contractYear3Salary = $this->playerData->contractYear3Salary;
        $this->contractYear4Salary = $this->playerData->contractYear4Salary;
        $this->contractYear5Salary = $this->playerData->contractYear5Salary;
        $this->contractYear6Salary = $this->playerData->contractYear6Salary;
        $this->currentSeasonSalary = $this->contractCalculator->getCurrentSeasonSalary($this->playerData);
        $this->salaryJSB = $this->playerData->salaryJSB;
    
        $this->draftYear = $this->playerData->draftYear;
        $this->draftRound = $this->playerData->draftRound;
        $this->draftPickNumber = $this->playerData->draftPickNumber;
        $this->draftTeamOriginalName = $this->playerData->draftTeamOriginalName;
        $this->draftTeamCurrentName = $this->playerData->draftTeamCurrentName;
        $this->collegeName = $this->playerData->collegeName;
    
        $this->daysRemainingForInjury = $this->playerData->daysRemainingForInjury;
    
        $this->heightFeet = $this->playerData->heightFeet;
        $this->heightInches = $this->playerData->heightInches;
        $this->weightPounds = $this->playerData->weightPounds;
    
        $this->isRetired = $this->playerData->isRetired;
    
        $this->timeDroppedOnWaivers = $this->playerData->timeDroppedOnWaivers;

        $this->decoratedName = $this->decoratePlayerName();
    }

    public function decoratePlayerName()
    {
        return $this->nameDecorator->decoratePlayerName($this->playerData);
    }

    public function getCurrentSeasonSalary()
    {
        return $this->contractCalculator->getCurrentSeasonSalary($this->playerData);
    }

    public function getFreeAgencyDemands()
    {
        return $this->repository->getFreeAgencyDemands($this->name);
    }

    public function getInjuryReturnDate($rawLastSimEndDate)
    {
        return $this->injuryCalculator->getInjuryReturnDate($this->playerData, $rawLastSimEndDate);
    }

    public function getNextSeasonSalary()
    {
        return $this->contractCalculator->getNextSeasonSalary($this->playerData);
    }

    public function getLongBuyoutArray()
    {
        return $this->contractCalculator->getLongBuyoutArray($this->playerData);
    }

    public function getShortBuyoutArray()
    {
        return $this->contractCalculator->getShortBuyoutArray($this->playerData);
    }

    public function getRemainingContractArray()
    {
        return $this->contractCalculator->getRemainingContractArray($this->playerData);
    }

    public function getTotalRemainingSalary()
    {
        return $this->contractCalculator->getTotalRemainingSalary($this->playerData);
    }

    public function canRenegotiateContract()
    {
        return $this->contractValidator->canRenegotiateContract($this->playerData);
    }

    public function canRookieOption($seasonPhase)
    {
        return $this->contractValidator->canRookieOption($this->playerData, $seasonPhase);
    }

    public function wasRookieOptioned()
    {
        return $this->contractValidator->wasRookieOptioned($this->playerData);
    }
}