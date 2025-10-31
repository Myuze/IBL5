<?php

namespace Waivers;

/**
 * Processes waiver wire business logic
 */
class WaiversProcessor
{
    /**
     * Calculates veteran minimum salary based on years of experience
     * 
     * Salary tiers are determined by the NBA collective bargaining agreement:
     * - 10+ years: $103k (maximum veteran minimum)
     * - 9 years: $100k
     * - 8 years: $89k
     * - 7 years: $82k
     * - 6 years: $76k
     * - 5 years: $70k
     * - 4 years: $64k
     * - 3 years: $61k
     * - 0-2 years: $51k (rookie minimum)
     * 
     * @param int $experience Years of experience
     * @return int Veteran minimum salary in thousands
     */
    public function calculateVeteranMinimumSalary(int $experience): int
    {
        if ($experience > 9) {
            return 103;
        } elseif ($experience > 8) {
            return 100;
        } elseif ($experience > 7) {
            return 89;
        } elseif ($experience > 6) {
            return 82;
        } elseif ($experience > 5) {
            return 76;
        } elseif ($experience > 4) {
            return 70;
        } elseif ($experience > 3) {
            return 64;
        } elseif ($experience > 2) {
            return 61;
        } else {
            return 51;
        }
    }
    
    /**
     * Gets the display contract for a player
     * 
     * @param array $playerData Player data including contract info
     * @return string Formatted contract display
     */
    public function getPlayerContractDisplay(array $playerData): string
    {
        $currentSeasonSalary = (int) ($playerData['cy1'] ?? 0);
        
        if ($currentSeasonSalary == 0) {
            $experience = (int) ($playerData['exp'] ?? 0);
            return (string) $this->calculateVeteranMinimumSalary($experience);
        }
        
        $currentYear = (int) ($playerData['cy'] ?? 1);
        $totalYears = (int) ($playerData['cyt'] ?? 1);
        $contractParts = [];
        
        for ($year = $currentYear; $year <= $totalYears; $year++) {
            $contractYearField = "cy$year";
            if (isset($playerData[$contractYearField])) {
                $contractParts[] = (int) $playerData[$contractYearField];
            }
        }
        
        return implode(" ", $contractParts);
    }
    
    /**
     * Calculates wait time until a player clears waivers (24 hours)
     * 
     * @param int $dropTime Timestamp when player was dropped
     * @param int $currentTime Current timestamp
     * @return string Wait time display or empty if cleared
     */
    public function getWaiverWaitTime(int $dropTime, int $currentTime): string
    {
        $timeDiff = $currentTime - $dropTime;
        $waitPeriod = 86400; // 24 hours in seconds
        
        if ($timeDiff >= $waitPeriod) {
            return '';
        }
        
        $remainingTime = $waitPeriod - $timeDiff;
        $hours = floor($remainingTime / 3600);
        $minutes = floor(($remainingTime - $hours * 3600) / 60);
        $seconds = $remainingTime % 60;
        
        return "(Clears in $hours h, $minutes m, $seconds s)";
    }
    
    /**
     * Prepares contract data for a waiver signing
     * 
     * @param array $playerData Player data
     * @return array Contract data with cy1, cy, and final contract display
     */
    public function prepareContractData(array $playerData): array
    {
        $currentSeasonSalary = (int) ($playerData['cy1'] ?? 0);
        
        if ($currentSeasonSalary == 0) {
            $experience = (int) ($playerData['exp'] ?? 0);
            $vetMinSalary = $this->calculateVeteranMinimumSalary($experience);
            
            return [
                'cy1' => $vetMinSalary,
                'isNewContract' => true,
                'finalContract' => (string) $vetMinSalary
            ];
        }
        
        // Use existing contract
        $currentYear = (int) ($playerData['cy'] ?? 1);
        $totalYears = (int) ($playerData['cyt'] ?? 1);
        $contractParts = [];
        
        for ($year = $currentYear; $year <= $totalYears; $year++) {
            $contractYearField = "cy$year";
            if (isset($playerData[$contractYearField])) {
                $contractParts[] = (int) $playerData[$contractYearField];
            }
        }
        
        return [
            'isNewContract' => false,
            'finalContract' => implode(" ", $contractParts)
        ];
    }
}
