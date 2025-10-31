<?php

namespace Player;

/**
 * PlayerData - Value object representing player information
 * 
 * This class is a simple data container following the Data Transfer Object pattern.
 * It holds player data without business logic, making it easy to serialize, cache, and test.
 */
class PlayerData
{
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
    }
}
