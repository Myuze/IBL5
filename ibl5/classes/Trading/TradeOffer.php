<?php

class Trading_TradeOffer
{
    protected $db;
    protected $sharedFunctions;
    protected $season;
    protected $cashHandler;
    protected $validator;

    public function __construct($db)
    {
        $this->db = $db;
        $this->sharedFunctions = new Shared($db);
        $this->season = new Season($db);
        $this->cashHandler = new Trading_CashTransactionHandler($db);
        $this->validator = new Trading_TradeValidator($db);
    }

    /**
     * Create a new trade offer
     * @param array $tradeData Trade data from form submission
     * @return array Result with success status and any errors
     */
    public function createTradeOffer($tradeData)
    {
        // Generate new trade offer ID
        $tradeOfferId = $this->generateTradeOfferId();
        
        // Validate cash amounts
        $cashValidation = $this->validator->validateMinimumCashAmounts(
            $tradeData['userSendsCash'], 
            $tradeData['partnerSendsCash']
        );
        
        if (!$cashValidation['valid']) {
            return ['success' => false, 'error' => $cashValidation['error']];
        }

        // Calculate and validate salary caps
        $capData = $this->calculateSalaryCapData($tradeData);
        $capValidation = $this->validator->validateSalaryCaps($capData);
        
        if (!$capValidation['valid']) {
            return [
                'success' => false, 
                'errors' => $capValidation['errors'],
                'capData' => $capValidation
            ];
        }

        // Process the trade offer creation
        $result = $this->insertTradeOfferData($tradeOfferId, $tradeData);
        
        if ($result['success']) {
            $this->sendTradeNotification($tradeData, $result['tradeText']);
        }

        return $result;
    }

    /**
     * Generate a new unique trade offer ID
     * @return int New trade offer ID
     */
    protected function generateTradeOfferId()
    {
        $query0 = "SELECT * FROM ibl_trade_autocounter ORDER BY `counter` DESC";
        $result0 = $this->db->sql_query($query0);
        $tradeOfferId = $this->db->sql_result($result0, 0, "counter") + 1;

        $query0a = "INSERT INTO ibl_trade_autocounter ( `counter` ) VALUES ( '$tradeOfferId')";
        $this->db->sql_query($query0a);

        return $tradeOfferId;
    }

    /**
     * Calculate salary cap data for both teams
     * @param array $tradeData Trade data
     * @return array Calculated cap data
     */
    protected function calculateSalaryCapData($tradeData)
    {
        $userCurrentSeasonCapTotal = 0;
        $partnerCurrentSeasonCapTotal = 0;
        $userCapSentToPartner = 0;
        $partnerCapSentToUser = 0;

        // Calculate user team salary data
        $switchCounter = $tradeData['switchCounter'];
        for ($j = 0; $j < $switchCounter; $j++) {
            $isChecked = $tradeData['check'][$j] ?? null;
            $salaryAmount = (int)($tradeData['contract'][$j] ?? 0);
            $userCurrentSeasonCapTotal += $salaryAmount;
            
            if ($isChecked == "on") {
                $userCapSentToPartner += $salaryAmount;
            }
        }

        // Calculate partner team salary data
        $fieldsCounter = $tradeData['fieldsCounter'];
        for ($j = $switchCounter; $j < $fieldsCounter; $j++) {
            $isChecked = $tradeData['check'][$j] ?? null;
            $salaryAmount = (int)($tradeData['contract'][$j] ?? 0);
            $partnerCurrentSeasonCapTotal += $salaryAmount;
            
            if ($isChecked == "on") {
                $partnerCapSentToUser += $salaryAmount;
            }
        }

        // Add cash considerations
        $cashConsiderations = $this->validator->getCurrentSeasonCashConsiderations(
            $tradeData['userSendsCash'], 
            $tradeData['partnerSendsCash']
        );

        $userCurrentSeasonCapTotal += $cashConsiderations['cashSentToThem'];
        $userCurrentSeasonCapTotal -= $cashConsiderations['cashSentToMe'];
        $partnerCurrentSeasonCapTotal += $cashConsiderations['cashSentToMe'];
        $partnerCurrentSeasonCapTotal -= $cashConsiderations['cashSentToThem'];

        return [
            'userCurrentSeasonCapTotal' => $userCurrentSeasonCapTotal,
            'partnerCurrentSeasonCapTotal' => $partnerCurrentSeasonCapTotal,
            'userCapSentToPartner' => $userCapSentToPartner,
            'partnerCapSentToUser' => $partnerCapSentToUser
        ];
    }

    /**
     * Insert trade offer data into database
     * @param int $tradeOfferId Trade offer ID
     * @param array $tradeData Trade data
     * @return array Result with success status and trade text
     */
    protected function insertTradeOfferData($tradeOfferId, $tradeData)
    {
        $tradeText = "";
        $offeringTeamName = $tradeData['offeringTeam'];
        $listeningTeamName = $tradeData['listeningTeam'];

        // Process offering team items
        $switchCounter = $tradeData['switchCounter'];
        for ($k = 0; $k < $switchCounter; $k++) {
            if (($tradeData['check'][$k] ?? null) == "on") {
                $result = $this->insertTradeItem(
                    $tradeOfferId,
                    $tradeData['index'][$k],
                    $tradeData['type'][$k],
                    $offeringTeamName,
                    $listeningTeamName,
                    $listeningTeamName
                );
                $tradeText .= $result['tradeText'];
            }
        }

        // Process offering team cash
        if ($this->cashHandler->hasCashInTrade($tradeData['userSendsCash'])) {
            $result = $this->insertCashTradeOffer(
                $tradeOfferId,
                $offeringTeamName,
                $listeningTeamName,
                $tradeData['userSendsCash'],
                $listeningTeamName  // Approval should always be the listening team
            );
            $tradeText .= $result['tradeText'];
        }

        // Process receiving team items
        $fieldsCounter = $tradeData['fieldsCounter'];
        for ($k = $switchCounter; $k < $fieldsCounter; $k++) {
            if (($tradeData['check'][$k] ?? null) == "on") {
                $result = $this->insertTradeItem(
                    $tradeOfferId,
                    $tradeData['index'][$k],
                    $tradeData['type'][$k],
                    $listeningTeamName,
                    $offeringTeamName,
                    $listeningTeamName
                );
                $tradeText .= $result['tradeText'];
            }
        }

        // Process receiving team cash
        if ($this->cashHandler->hasCashInTrade($tradeData['partnerSendsCash'])) {
            $result = $this->insertCashTradeOffer(
                $tradeOfferId,
                $listeningTeamName,
                $offeringTeamName,
                $tradeData['partnerSendsCash'],
                $listeningTeamName  // Approval should always be the listening team
            );
            $tradeText .= $result['tradeText'];
        }

        return [
            'success' => true,
            'tradeText' => $tradeText,
            'tradeOfferId' => $tradeOfferId
        ];
    }

    /**
     * Insert a single trade item (player or pick)
     * @param int $tradeOfferId Trade offer ID
     * @param int $itemId Item ID
     * @param int $assetType Asset type (0=pick, 1=player)
     * @param string $offeringTeamName Offering team name
     * @param string $listeningTeamName Listening team name
     * @param string $approvalTeamName Name of team that needs to approve
     * @return array Result
     */
    protected function insertTradeItem($tradeOfferId, $itemId, $assetType, $offeringTeamName, $listeningTeamName, $approvalTeamName)
    {
        $query = "INSERT INTO ibl_trade_info 
          ( `tradeofferid`, 
            `itemid`, 
            `itemtype`, 
            `from`, 
            `to`, 
            `approval` ) 
        VALUES        ( '$tradeOfferId', 
            '$itemId', 
            '$assetType', 
            '$offeringTeamName', 
            '$listeningTeamName', 
            '$approvalTeamName' )";
        
        $this->db->sql_query($query);

        $tradeText = "";
        if ($assetType == 0) {
            $tradeText = $this->getPickTradeText($itemId, $offeringTeamName, $listeningTeamName);
        } else {
            $tradeText = $this->getPlayerTradeText($itemId, $offeringTeamName, $listeningTeamName);
        }

        return ['tradeText' => $tradeText];
    }

    /**
     * Get trade text for a draft pick
     * @param int $pickId Pick ID
     * @param string $offeringTeamName Offering team name
     * @param string $listeningTeamName Listening team name
     * @return string Trade text
     */
    protected function getPickTradeText($pickId, $offeringTeamName, $listeningTeamName)
    {
        $sqlgetpick = "SELECT * FROM ibl_draft_picks WHERE pickid = '$pickId'";
        $resultgetpick = $this->db->sql_query($sqlgetpick);
        $rowsgetpick = $this->db->sql_fetchrow($resultgetpick);

        $pickTeam = $rowsgetpick['teampick'];
        $pickYear = $rowsgetpick['year'];
        $pickRound = $rowsgetpick['round'];
        $pickNotes = $rowsgetpick['notes'];

        $tradeText = "The $offeringTeamName send the $pickTeam $pickYear Round $pickRound draft pick to the $listeningTeamName.<br>";
        if ($pickNotes != NULL) {
            $tradeText .= "<i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $pickNotes . "</i><br>";
        }

        return $tradeText;
    }

    /**
     * Get trade text for a player
     * @param int $playerId Player ID
     * @param string $offeringTeamName Offering team name
     * @param string $listeningTeamName Listening team name
     * @return string Trade text
     */
    protected function getPlayerTradeText($playerId, $offeringTeamName, $listeningTeamName)
    {
        $sqlgetplyr = "SELECT * FROM ibl_plr WHERE pid = '$playerId'";
        $resultgetplyr = $this->db->sql_query($sqlgetplyr);
        $rowsgetplyr = $this->db->sql_fetchrow($resultgetplyr);

        $playerName = $rowsgetplyr['name'];
        $playerPosition = $rowsgetplyr['pos'];

        return "The $offeringTeamName send $playerPosition $playerName to the $listeningTeamName.<br>";
    }

    /**
     * Insert cash trade offer
     * @param int $tradeOfferId Trade offer ID
     * @param string $offeringTeamName Offering team name
     * @param string $listeningTeamName Listening team name
     * @param array $cashAmounts Cash amounts by year
     * @param string $approvalTeamName Team that needs to approve the trade (should always be the listening team of the overall trade)
     * @return array Result
     */
    protected function insertCashTradeOffer($tradeOfferId, $offeringTeamName, $listeningTeamName, $cashAmounts, $approvalTeamName)
    {
        // Insert cash data
        $this->cashHandler->insertCashTradeData($tradeOfferId, $offeringTeamName, $listeningTeamName, $cashAmounts);

        // Insert trade info record for cash
        $offeringTeamId = $this->sharedFunctions->getTidFromTeamname($offeringTeamName);
        $listeningTeamId = $this->sharedFunctions->getTidFromTeamname($listeningTeamName);

        $query = "INSERT INTO ibl_trade_info
          ( `tradeofferid`,
            `itemid`,
            `itemtype`,
            `from`,
            `to`,
            `approval` )
        VALUES    ( '$tradeOfferId',
            '$offeringTeamId" . "0" . "$listeningTeamId" . "0',
            'cash',
            '$offeringTeamName',
            '$listeningTeamName',
            '$approvalTeamName' )";
        
        $this->db->sql_query($query);

        $cashText = implode(' ', array_filter($cashAmounts));
        $tradeText = "The $offeringTeamName send $cashText in cash to the $listeningTeamName.<br>";

        return ['tradeText' => $tradeText];
    }

    /**
     * Send trade notification to receiving team
     * @param array $tradeData Trade data
     * @param string $tradeText Trade description text
     */
    protected function sendTradeNotification($tradeData, $tradeText)
    {
        $offeringTeamName = $tradeData['offeringTeam'];
        $listeningTeamName = $tradeData['listeningTeam'];

        $offeringUserDiscordID = Discord::getDiscordIDFromTeamname($this->db, $offeringTeamName);
        $receivingUserDiscordID = Discord::getDiscordIDFromTeamname($this->db, $listeningTeamName);

        $cleanTradeText = str_replace(['<br>', '&nbsp;', '<i>', '</i>'], ["\n", " ", "_", "_"], $tradeText);

        $discordDMmessage = 'New trade proposal from <@!' . $offeringUserDiscordID . '>!
' . $cleanTradeText . '
Go here to accept or decline: http://www.iblhoops.net/ibl5/modules.php?name=Trading&op=reviewtrade';

        $arrayContent = [
            'message' => $discordDMmessage,
            'receivingUserDiscordID' => $receivingUserDiscordID,
        ];

        // $response = Discord::sendCurlPOST('http://localhost:50000/discordDM', $arrayContent);
    }
}