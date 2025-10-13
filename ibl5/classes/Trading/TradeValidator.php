<?php

class Trading_TradeValidator
{
    protected $db;
    protected $sharedFunctions;
    protected $season;

    public function __construct($db)
    {
        $this->db = $db;
        $this->sharedFunctions = new Shared($db);
        $this->season = new Season($db);
    }

    /**
     * Validate minimum cash amounts in a trade
     * @param array $userSendsCash Array of cash amounts sent by user team
     * @param array $partnerSendsCash Array of cash amounts sent by partner team
     * @return array with 'valid' boolean and 'error' message
     */
    public function validateMinimumCashAmounts($userSendsCash, $partnerSendsCash)
    {
        $filteredUserSendsCash = array_filter($userSendsCash);
        $filteredPartnerSendsCash = array_filter($partnerSendsCash);

        if (!empty($filteredUserSendsCash) && min($filteredUserSendsCash) < 100) {
            return [
                'valid' => false,
                'error' => 'This trade is illegal: the minimum amount of cash that your team can send in any one season is 100.'
            ];
        }

        if (!empty($filteredPartnerSendsCash) && min($filteredPartnerSendsCash) < 100) {
            return [
                'valid' => false,
                'error' => 'This trade is illegal: the minimum amount of cash that the other team can send in any one season is 100.'
            ];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Calculate post-trade salary cap totals for both teams
     * @param array $tradeData Array containing all trade information
     * @return array with calculated cap totals and validation result
     */
    public function validateSalaryCaps($tradeData)
    {
        $userCurrentSeasonCapTotal = $tradeData['userCurrentSeasonCapTotal'] ?? 0;
        $partnerCurrentSeasonCapTotal = $tradeData['partnerCurrentSeasonCapTotal'] ?? 0;
        $userCapSentToPartner = $tradeData['userCapSentToPartner'] ?? 0;
        $partnerCapSentToUser = $tradeData['partnerCapSentToUser'] ?? 0;

        $userPostTradeCapTotal = $userCurrentSeasonCapTotal - $userCapSentToPartner + $partnerCapSentToUser;
        $partnerPostTradeCapTotal = $partnerCurrentSeasonCapTotal - $partnerCapSentToUser + $userCapSentToPartner;

        $errors = [];

        if ($userPostTradeCapTotal > League::HARD_CAP_MAX) {
            $errors[] = 'This trade is illegal since it puts you over the hard cap.';
        }

        if ($partnerPostTradeCapTotal > League::HARD_CAP_MAX) {
            $errors[] = 'This trade is illegal since it puts other team over the hard cap.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'userPostTradeCapTotal' => $userPostTradeCapTotal,
            'partnerPostTradeCapTotal' => $partnerPostTradeCapTotal
        ];
    }

    /**
     * Check if a player can be traded (not waived, etc.)
     * @param int $playerId
     * @return bool
     */
    public function canPlayerBeTraded($playerId)
    {
        $result = $this->db->sql_query("SELECT ordinal, cy FROM ibl_plr WHERE pid = $playerId");
        $player = $this->db->sql_fetchrow($result);

        if (!$player || !is_array($player) || count($player) < 2) {
            return false;
        }

        // Extract ordinal and cy from the indexed array
        $ordinal = isset($player[0]) ? $player[0] : 99999; // Default to high ordinal if missing
        $cy = isset($player[1]) ? $player[1] : 0; // Default to 0 salary if missing

        // Player cannot be traded if they are waived (ordinal > JSB::WAIVERS_ORDINAL) or have 0 salary
        return $cy != 0 && $ordinal <= JSB::WAIVERS_ORDINAL;
    }

    /**
     * Get cash considerations for current season based on phase
     * @param array $userSendsCash Cash sent by user team
     * @param array $partnerSendsCash Cash sent by partner team
     * @return array Cash considerations for current season
     */
    public function getCurrentSeasonCashConsiderations($userSendsCash, $partnerSendsCash)
    {
        // If the current season phase shifts cap situations to next season, evaluate next season's cap limits.
        if (
            $this->season->phase == "Playoffs"
            || $this->season->phase == "Draft"
            || $this->season->phase == "Free Agency"
        ) {
            $cashSentToThemThisSeason = $userSendsCash[2] ?? 0;
            $cashSentToMeThisSeason = $partnerSendsCash[2] ?? 0;
        } else {
            $cashSentToThemThisSeason = $userSendsCash[1] ?? 0;
            $cashSentToMeThisSeason = $partnerSendsCash[1] ?? 0;
        }

        return [
            'cashSentToThem' => $cashSentToThemThisSeason,
            'cashSentToMe' => $cashSentToMeThisSeason
        ];
    }
}