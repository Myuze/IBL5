<?php

namespace Extension;

/**
 * Extension Offer Evaluator Class
 * 
 * Handles offer evaluation logic including player preferences and modifiers.
 * Calculates whether a player will accept or reject an extension offer based on:
 * - Offer value relative to demands
 * - Team performance (wins/losses)
 * - Franchise tradition
 * - Player loyalty
 * - Playing time concerns (money committed at position)
 */
class ExtensionOfferEvaluator
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Calculates the total value, years, and average per year of an offer
     * 
     * @param array $offer Array with keys: year1, year2, year3, year4, year5
     * @return array ['total' => int, 'years' => int, 'averagePerYear' => float]
     */
    public function calculateOfferValue($offer)
    {
        $total = $offer['year1'] + $offer['year2'] + $offer['year3'] + $offer['year4'] + $offer['year5'];
        $years = 5;
        if ($offer['year5'] == 0) {
            $years = 4;
        }
        if ($offer['year4'] == 0) {
            $years = 3;
        }
        
        return [
            'total' => $total,
            'years' => $years,
            'averagePerYear' => $years > 0 ? $total / $years : 0
        ];
    }

    /**
     * Calculates the winner modifier based on team wins/losses and player preference
     * Formula: 0.000153 * (wins - losses) * (player_winner - 1)
     * 
     * @param array $teamFactors ['wins' => int, 'losses' => int]
     * @param array $playerPreferences ['winner' => int] (1-5 scale)
     * @return float Modifier contribution
     */
    public function calculateWinnerModifier($teamFactors, $playerPreferences)
    {
        $winDiff = $teamFactors['wins'] - $teamFactors['losses'];
        return 0.000153 * $winDiff * ($playerPreferences['winner'] - 1);
    }

    /**
     * Calculates the tradition modifier based on franchise history and player preference
     * Formula: 0.000153 * (tradition_wins - tradition_losses) * (player_tradition - 1)
     * 
     * @param array $teamFactors ['tradition_wins' => int, 'tradition_losses' => int]
     * @param array $playerPreferences ['tradition' => int] (1-5 scale)
     * @return float Modifier contribution
     */
    public function calculateTraditionModifier($teamFactors, $playerPreferences)
    {
        $tradDiff = $teamFactors['tradition_wins'] - $teamFactors['tradition_losses'];
        return 0.000153 * $tradDiff * ($playerPreferences['tradition'] - 1);
    }

    /**
     * Calculates the loyalty modifier based on player's loyalty rating
     * Formula: 0.025 * (player_loyalty - 1)
     * 
     * @param array $playerPreferences ['loyalty' => int] (1-5 scale)
     * @return float Modifier contribution
     */
    public function calculateLoyaltyModifier($playerPreferences)
    {
        return 0.025 * ($playerPreferences['loyalty'] - 1);
    }

    /**
     * Calculates the playing time modifier based on money committed at position
     * Formula: -(.0025 * money_committed / 100 - 0.025) * (player_playingtime - 1)
     * 
     * Note: This corrects a bug in the original code where $tf_millions was undefined.
     * Now properly uses money_committed_at_position from team info.
     * 
     * @param array $teamFactors ['money_committed_at_position' => int]
     * @param array $playerPreferences ['playing_time' => int] (1-5 scale)
     * @return float Modifier contribution
     */
    public function calculatePlayingTimeModifier($teamFactors, $playerPreferences)
    {
        $moneyCommitted = isset($teamFactors['money_committed_at_position']) 
            ? $teamFactors['money_committed_at_position'] 
            : 0;
        return -0.0025 * ($moneyCommitted / 100) * ($playerPreferences['playing_time'] - 1);
    }

    /**
     * Calculates the combined modifier from all factors
     * Base modifier is 1.0, then add all individual modifiers
     * 
     * @param array $teamFactors Team information
     * @param array $playerPreferences Player preferences
     * @return float Combined modifier (typically 0.8 to 1.2)
     */
    public function calculateCombinedModifier($teamFactors, $playerPreferences)
    {
        $modifier = 1.0;
        $modifier += $this->calculateWinnerModifier($teamFactors, $playerPreferences);
        $modifier += $this->calculateTraditionModifier($teamFactors, $playerPreferences);
        $modifier += $this->calculateLoyaltyModifier($playerPreferences);
        $modifier += $this->calculatePlayingTimeModifier($teamFactors, $playerPreferences);
        return $modifier;
    }

    /**
     * Evaluates whether a player will accept an extension offer
     * 
     * @param array $offer Offer array with year1-year5
     * @param array $demands Player's demands array with year1-year5
     * @param array $teamFactors Team information
     * @param array $playerPreferences Player preferences
     * @return array ['accepted' => bool, 'offerValue' => float, 'demandValue' => float, 'modifier' => float]
     */
    public function evaluateOffer($offer, $demands, $teamFactors, $playerPreferences)
    {
        $offerData = $this->calculateOfferValue($offer);
        $demandsData = $this->calculateOfferValue($demands);
        
        $modifier = $this->calculateCombinedModifier($teamFactors, $playerPreferences);
        
        $adjustedOfferValue = $offerData['averagePerYear'] * $modifier;
        $demandValue = $demandsData['averagePerYear'];
        
        return [
            'accepted' => $adjustedOfferValue >= $demandValue,
            'offerValue' => $adjustedOfferValue,
            'demandValue' => $demandValue,
            'modifier' => $modifier,
            'offerYears' => $offerData['years']
        ];
    }

    /**
     * Converts offer amount to millions for display
     * 
     * @param int $offerTotal Total offer amount
     * @return float Amount in millions
     */
    public function convertToMillions($offerTotal)
    {
        return $offerTotal / 100;
    }
    
    /**
     * Calculates player demands based on player value
     * (Helper method for testing)
     * 
     * @param int $playerValue Player's value
     * @return array Demands array with total and years
     */
    public function calculatePlayerDemands($playerValue)
    {
        return [
            'total' => $playerValue * 5,
            'years' => 5
        ];
    }
}
