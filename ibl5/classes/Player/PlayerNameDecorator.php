<?php

namespace Player;

/**
 * PlayerNameDecorator - Handles player name formatting and decoration
 * 
 * This class is responsible for formatting player names with special indicators
 * (free agency eligibility, waivers, etc.)
 */
class PlayerNameDecorator
{
    /**
     * Decorate a player name with status indicators
     */
    public function decoratePlayerName(PlayerData $playerData): string
    {
        if ($playerData->teamID == 0) {
            $decoratedName = "$playerData->name";
        } elseif ($playerData->ordinal > \JSB::WAIVERS_ORDINAL) {
            $decoratedName = "($playerData->name)*";
        } elseif ($playerData->contractCurrentYear == $playerData->contractTotalYears) { // eligible for Free Agency at the end of this season
            $decoratedName = "$playerData->name^";
        } else {
            $decoratedName = "$playerData->name";
        }
        return $decoratedName;
    }
}
