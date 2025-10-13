<?php

class Trading_CashTransactionHandler
{
    protected $db;
    protected $sharedFunctions;

    public function __construct($db)
    {
        $this->db = $db;
        $this->sharedFunctions = new Shared($db);
    }

    /**
     * Generate a unique PID that doesn't exist in the database
     * @param int $pid Starting PID to check
     * @return int Available PID
     */
    public function generateUniquePid($pid)
    {
        $queryCheckIfPidExists = "SELECT 1 FROM ibl_plr WHERE pid = $pid";
        $resultCheckIfPidExists = $this->db->sql_query($queryCheckIfPidExists);
        $pidResult = $this->db->sql_result($resultCheckIfPidExists, 0);

        if ($pidResult == NULL) {
            return $pid;
        } else {
            $pid += 2;
            return $this->generateUniquePid($pid);
        }
    }

    /**
     * Calculate contract total years based on cash year data
     * @param array $cashYear Array of cash amounts by year (1-6)
     * @return int Total contract years
     */
    public function calculateContractTotalYears($cashYear)
    {
        if ($cashYear[6] != 0) {
            return 6;
        } elseif ($cashYear[5] != 0) {
            return 5;
        } elseif ($cashYear[4] != 0) {
            return 4;
        } elseif ($cashYear[3] != 0) {
            return 3;
        } elseif ($cashYear[2] != 0) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * Create cash transaction entries in the database
     * @param int $itemId Unique item ID for the transaction
     * @param string $offeringTeamName Offering team name
     * @param string $listeningTeamName Listening team name
     * @param array $cashYear Cash amounts by year
     * @return array Result with success status and trade line text
     */
    public function createCashTransaction($itemId, $offeringTeamName, $listeningTeamName, $cashYear)
    {
        $offeringTeamId = $this->sharedFunctions->getTidFromTeamname($offeringTeamName);
        $listeningTeamId = $this->sharedFunctions->getTidFromTeamname($listeningTeamName);
        
        $contractCurrentYear = 1;
        $contractTotalYears = $this->calculateContractTotalYears($cashYear);

        // Insert positive cash row (for offering team)
        $queryInsertPositiveCashRow = "INSERT INTO `ibl_plr` 
            (`ordinal`, 
            `pid`, 
            `name`, 
            `tid`, 
            `teamname`, 
            `exp`, 
            `cy`, 
            `cyt`, 
            `cy1`, 
            `cy2`, 
            `cy3`, 
            `cy4`, 
            `cy5`, 
            `cy6`) 
        VALUES
            ('100000',
            '$itemId',
            '| <B>Cash to $listeningTeamName</B>',
            '$offeringTeamId',
            '$offeringTeamName',
            '$contractCurrentYear',
            '$contractCurrentYear',
            '$contractTotalYears',
            '{$cashYear[1]}',
            '{$cashYear[2]}',
            '{$cashYear[3]}',
            '{$cashYear[4]}',
            '{$cashYear[5]}',
            '{$cashYear[6]}')";

        $resultInsertPositiveCashRow = $this->db->sql_query($queryInsertPositiveCashRow);

        $itemId++;

        // Insert negative cash row (for listening team)
        $queryInsertNegativeCashRow = "INSERT INTO `ibl_plr` 
            (`ordinal`,
            `pid`,
            `name`,
            `tid`,
            `teamname`,
            `exp`,
            `cy`,
            `cyt`,
            `cy1`,
            `cy2`,
            `cy3`,
            `cy4`,
            `cy5`,
            `cy6`)
        VALUES
            ('100000',
            '$itemId',
            '| <B>Cash from $offeringTeamName</B>',
            '$listeningTeamId',
            '$listeningTeamName',
            '$contractCurrentYear',
            '$contractCurrentYear',
            '$contractTotalYears',
            '-{$cashYear[1]}',
            '-{$cashYear[2]}',
            '-{$cashYear[3]}',
            '-{$cashYear[4]}',
            '-{$cashYear[5]}',
            '-{$cashYear[6]}')";

        $resultInsertNegativeCashRow = $this->db->sql_query($queryInsertNegativeCashRow);

        $success = $resultInsertPositiveCashRow && $resultInsertNegativeCashRow;
        $tradeLine = "";

        if ($success) {
            $tradeLine = "The $offeringTeamName send {$cashYear[1]} {$cashYear[2]} {$cashYear[3]} {$cashYear[4]} {$cashYear[5]} {$cashYear[6]} in cash to the $listeningTeamName.<br>";
        }

        return [
            'success' => $success,
            'tradeLine' => $tradeLine
        ];
    }

    /**
     * Insert cash trade data into ibl_trade_cash table
     * @param int $tradeOfferId Trade offer ID
     * @param string $offeringTeamName Offering team name
     * @param string $listeningTeamName Listening team name
     * @param array $cashAmounts Cash amounts by year (1-6)
     * @return bool Success status
     */
    public function insertCashTradeData($tradeOfferId, $offeringTeamName, $listeningTeamName, $cashAmounts)
    {
        // Ensure all cash year values are set
        for ($i = 1; $i <= 6; $i++) {
            $cashAmounts[$i] = $cashAmounts[$i] ?? 0;
        }

        $query = "INSERT INTO ibl_trade_cash
          ( `tradeOfferID`,
            `sendingTeam`,
            `receivingTeam`,
            `cy1`,
            `cy2`,
            `cy3`,
            `cy4`,
            `cy5`,
            `cy6` )
        VALUES    ( '$tradeOfferId',
            '$offeringTeamName',
            '$listeningTeamName',
            '{$cashAmounts[1]}',
            '{$cashAmounts[2]}',
            '{$cashAmounts[3]}',
            '{$cashAmounts[4]}',
            '{$cashAmounts[5]}',
            '{$cashAmounts[6]}' )";

        return $this->db->sql_query($query);
    }

    /**
     * Check if any cash is being sent in the trade
     * @param array $cashAmounts Cash amounts by year
     * @return bool True if any cash is being sent
     */
    public function hasCashInTrade($cashAmounts)
    {
        for ($i = 1; $i <= 6; $i++) {
            if (($cashAmounts[$i] ?? 0) != 0) {
                return true;
            }
        }
        return false;
    }
}