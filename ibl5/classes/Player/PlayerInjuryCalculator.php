<?php

namespace Player;

/**
 * PlayerInjuryCalculator - Handles injury-related date calculations
 * 
 * This class encapsulates injury-related calculations, making the logic easy to test.
 */
class PlayerInjuryCalculator
{
    /**
     * Calculate when an injured player will return
     */
    public function getInjuryReturnDate(PlayerData $playerData, string $rawLastSimEndDate): string
    {
        if ($playerData->daysRemainingForInjury <= 0) {
            return "";
        } else {
            $properLastSimEndDate = date_create($rawLastSimEndDate);
            $injuryDateString = $playerData->daysRemainingForInjury + 1 . ' days';
            $injuryReturnDate = date_add($properLastSimEndDate, date_interval_create_from_date_string($injuryDateString));
            return $injuryReturnDate->format('Y-m-d');
        }
    }
}
