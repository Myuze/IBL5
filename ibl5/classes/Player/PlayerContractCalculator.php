<?php

namespace Player;

/**
 * PlayerContractCalculator - Handles salary and contract calculations
 * 
 * This class follows the Single Responsibility Principle by focusing only on
 * contract and salary calculations. It contains no data persistence logic.
 */
class PlayerContractCalculator
{
    /**
     * Calculate the current season salary based on contract year
     */
    public function getCurrentSeasonSalary(PlayerData $playerData): int
    {
        if ("contractYear" . $playerData->contractCurrentYear . "Salary" == "contractYear0Salary") {
            $currentSeasonSalary = $playerData->contractYear1Salary;
        } elseif ("contractYear" . $playerData->contractCurrentYear . "Salary" == "contractYear7Salary") {
            $currentSeasonSalary = 0;
        } else {
            $currentSeasonSalary = $playerData->{"contractYear" . $playerData->contractCurrentYear . "Salary"};
        }
        return $currentSeasonSalary;
    }

    /**
     * Calculate the next season's salary
     */
    public function getNextSeasonSalary(PlayerData $playerData): int
    {
        $contractNextYear = $playerData->contractCurrentYear + 1;
        $nextSeasonSalary = $playerData->{"contractYear" . $contractNextYear . "Salary"};
        return $nextSeasonSalary;
    }

    /**
     * Get an array of remaining contract years and salaries
     */
    public function getRemainingContractArray(PlayerData $playerData): array
    {
        $contractCurrentYear = ($playerData->contractCurrentYear != 0) ? $playerData->contractCurrentYear : 1;
        $contractTotalYears = ($playerData->contractTotalYears != 0) ? $playerData->contractTotalYears : 1;

        $contractArray = array();
        $remainingContractYear = 1;
        for ($i = $contractCurrentYear; $i <= $contractTotalYears; $i++) {
            if ($playerData->{"contractYear" . $i . "Salary"} != 0) {
                $contractArray[$remainingContractYear] = $playerData->{"contractYear" . $i . "Salary"};
            }
            $remainingContractYear++;
        }

        $contractArray[1] = ($contractArray) ? $contractArray[1] : 0;
        return $contractArray;
    }

    /**
     * Calculate total remaining salary on the contract
     */
    public function getTotalRemainingSalary(PlayerData $playerData): int
    {
        $contractArray = $this->getRemainingContractArray($playerData);
        return array_sum($contractArray);
    }

    /**
     * Calculate long buyout terms (6 years)
     */
    public function getLongBuyoutArray(PlayerData $playerData): array
    {
        $totalRemainingSalary = $this->getTotalRemainingSalary($playerData);
        $oneSixthOfTotalRemainingSalary = round($totalRemainingSalary / 6);
        $longBuyoutArray[1] = $longBuyoutArray[2] = $longBuyoutArray[3] = $longBuyoutArray[4] = $longBuyoutArray[5] = $longBuyoutArray[6] = $oneSixthOfTotalRemainingSalary;
        return $longBuyoutArray;
    }

    /**
     * Calculate short buyout terms (2 years)
     */
    public function getShortBuyoutArray(PlayerData $playerData): array
    {
        $totalRemainingSalary = $this->getTotalRemainingSalary($playerData);
        $oneHalfOfTotalRemainingSalary = round($totalRemainingSalary / 2);
        $shortBuyoutArray[1] = $shortBuyoutArray[2] = $oneHalfOfTotalRemainingSalary;
        return $shortBuyoutArray;
    }
}
