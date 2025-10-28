<?php
namespace Updater;

class StandingsUpdater {
    private $db;
    private $sharedFunctions;

    public function __construct($db, $sharedFunctions) {
        $this->db = $db;
        $this->sharedFunctions = $sharedFunctions;
    }

    private function extractWins($var) {
        $var = rtrim(substr($var, 0, 2), '-');
        return $var;
    }

    private function extractLosses($var) {
        $var = ltrim(substr($var, -2, 2), '-');
        return $var;
    }

    private function assignGroupingsFor($region) {
        if (in_array($region, array("Eastern", "Western"))) {
            $grouping = 'conference';
            $groupingGB = 'confGB';
            $groupingMagicNumber = 'confMagicNumber';
        }
        if (in_array($region, array("Atlantic", "Central", "Midwest", "Pacific"))) {
            $grouping = 'division';
            $groupingGB = 'divGB';
            $groupingMagicNumber = 'divMagicNumber';
        }
        return array($grouping, $groupingGB, $groupingMagicNumber);
    }

    public function update() {
        echo '<p>Updating the ibl_standings database table...<p>';
        if ($this->db->sql_query('TRUNCATE TABLE ibl_standings')) {
            echo 'TRUNCATE TABLE ibl_standings<p>';
        }

        $this->extractStandingsValues();
        
        $this->updateMagicNumbers('Eastern');
        $this->updateMagicNumbers('Western');
        $this->updateMagicNumbers('Atlantic');
        $this->updateMagicNumbers('Central');
        $this->updateMagicNumbers('Midwest');
        $this->updateMagicNumbers('Pacific');
        
        echo '<p>Magic numbers for all teams have been updated.<p>';
        echo '<p>The ibl_standings table has been updated.<p><br>';
    }

    protected function extractStandingsValues() {
        echo '<p>Updating the conference standings for all teams...<p>';

        $standingsFilePath = $_SERVER['DOCUMENT_ROOT'] . '/ibl5/ibl/IBL/Standings.htm';
        $standings = new \DOMDocument();
        $standings->loadHTMLFile($standingsFilePath);
        $standings->preserveWhiteSpace = false;

        $getRows = $standings->getElementsByTagName('tr');
        $rowsByConference = $getRows->item(0)->childNodes->item(0)->childNodes->item(0)->childNodes;
        $rowsByDivision = $getRows->item(0)->childNodes->item(1)->childNodes->item(0)->childNodes;

        \UI::displayDebugOutput($this->processConferenceRows($rowsByConference), 'Conference Standings');
        \UI::displayDebugOutput($this->processDivisionRows($rowsByDivision), 'Division Standings');
    }

    private function processConferenceRows($rowsByConference) {
        $log = '';

        foreach ($rowsByConference as $row) {
            if (!is_null($row->childNodes)) {
                $teamName = $row->childNodes->item(0)->nodeValue;
                if (in_array($teamName, array("Eastern", "Western"))) {
                    $conference = $teamName;
                }
                if (!in_array($teamName, array("Eastern", "Western", "team", ""))) {
                    $log .= $this->processTeamStandings($row, $conference);
                }
            }
        }

        return $log;
    }

    private function processTeamStandings($row, $conference) {
        $log = '';

        $teamID = $this->sharedFunctions->getTidFromTeamname($row->childNodes->item(0)->nodeValue); // This function now returns an integer
        $leagueRecord = $row->childNodes->item(1)->nodeValue;
        $pct = $row->childNodes->item(2)->nodeValue;
        $confGB = $row->childNodes->item(3)->nodeValue;
        $confRecord = $row->childNodes->item(4)->nodeValue;
        $divRecord = $row->childNodes->item(5)->nodeValue;
        $homeRecord = $row->childNodes->item(6)->nodeValue;
        $awayRecord = $row->childNodes->item(7)->nodeValue;

        $confWins = $this->extractWins($confRecord);
        $confLosses = $this->extractLosses($confRecord);
        $divWins = $this->extractWins($divRecord);
        $divLosses = $this->extractLosses($divRecord);
        $homeWins = $this->extractWins($homeRecord);
        $homeLosses = $this->extractLosses($homeRecord);
        $awayWins = $this->extractWins($awayRecord);
        $awayLosses = $this->extractLosses($awayRecord);

        $gamesUnplayed = 82 - $homeWins - $homeLosses - $awayWins - $awayLosses;

        $sqlQueryString = "INSERT INTO ibl_standings (
            tid,
            team_name,
            leagueRecord,
            pct,
            gamesUnplayed,
            conference,
            confGB,
            confRecord,
            divRecord,
            homeRecord,
            awayRecord,
            confWins,
            confLosses,
            divWins,
            divLosses,
            homeWins,
            homeLosses,
            awayWins,
            awayLosses
        ) VALUES (
            '$teamID',
            '" . rtrim($row->childNodes->item(0)->nodeValue) . "',
            '$leagueRecord',
            '$pct',
            '$gamesUnplayed',
            '$conference',
            '$confGB',
            '$confRecord',
            '$divRecord',
            '$homeRecord',
            '$awayRecord',
            '$confWins',
            '$confLosses',
            '$divWins',
            '$divLosses',
            '$homeWins',
            '$homeLosses',
            '$awayWins',
            '$awayLosses'
        )";

        if ($this->db->sql_query($sqlQueryString)) {
            $log .= $sqlQueryString . '<br>';
        }
        return $log;
    }

    private function processDivisionRows($rowsByDivision) {
        $log = '';

        foreach ($rowsByDivision as $row) {
            if (!is_null($row->childNodes)) {
                $teamName = $row->childNodes->item(0)->nodeValue;
                
                if (in_array($teamName, array("Atlantic", "Central", "Midwest", "Pacific"))) {
                    $division = $teamName;
                }
                if (!in_array($teamName, array("Atlantic", "Central", "Midwest", "Pacific", "team", ""))) {
                    $log .= $this->updateTeamDivision($row, $division);
                }
            }
        }

        return $log;
    }

    private function updateTeamDivision($row, $division) {
        $log = '';
        
        $teamName = $row->childNodes->item(0)->nodeValue;
        $teamID = $this->sharedFunctions->getTidFromTeamname($teamName);
        $divGB = $row->childNodes->item(3)->nodeValue;

        $sqlQueryString = "UPDATE ibl_standings 
            SET division = '$division',
                divGB = '$divGB'
            WHERE tid = '$teamID'";

        if ($this->db->sql_query($sqlQueryString)) {
            $log .= $sqlQueryString . '<br>';
        }
        return $log;
    }

    private function updateMagicNumbers($region) {
        echo "<p>Updating the magic numbers for the $region...<br>";
        list($grouping, $groupingGB, $groupingMagicNumber) = $this->assignGroupingsFor($region);

        $query = "SELECT tid, team_name, homeWins, homeLosses, awayWins, awayLosses
            FROM ibl_standings
            WHERE $grouping = '$region'
            ORDER BY pct DESC";
        
        $result = $this->db->sql_query($query);
        $limit = $this->db->sql_numrows($result);

        $log = '';

        for ($i = 0; $i < $limit; $i++) {
            $teamID = $this->db->sql_result($result, $i, 0);
            $teamName = $this->db->sql_result($result, $i, 1);
            $teamTotalWins = $this->db->sql_result($result, $i, 2) + $this->db->sql_result($result, $i, 4);
            
            if ($i + 1 != $limit) {
                $belowTeamTotalLosses = $this->db->sql_result($result, $i + 1, 3) + $this->db->sql_result($result, $i + 1, 5);
            } else {
                $belowTeamTotalLosses = 0;
            }
            
            $magicNumber = 82 + 1 - $teamTotalWins - $belowTeamTotalLosses;

            $log .= $this->updateTeamMagicNumber($teamID, $teamName, $magicNumber, $groupingMagicNumber);
        }

        \UI::displayDebugOutput($log, "$region Magic Number Update Log");

        $this->checkIfRegionIsClinched($region);
        if ($grouping == 'conference') {
            $this->checkIfPlayoffsClinched($region);
        }
    }

    private function updateTeamMagicNumber($teamID, $teamName, $magicNumber, $groupingMagicNumber) {
        $log = '';

        $sqlQueryString = "UPDATE ibl_standings 
            SET $groupingMagicNumber = '$magicNumber'
            WHERE tid = '$teamID'";

        if ($this->db->sql_query($sqlQueryString)) {
            $log .= $sqlQueryString . '<br>';
        }

        return $log;
    }

    private function checkIfRegionIsClinched($region) {
        list($grouping, $groupingGB, $groupingMagicNumber) = $this->assignGroupingsFor($region);
        echo "<p>Checking if the $region $grouping has been clinched...<br>";

        $queryWinningestTeam = "SELECT team_name, homeWins + awayWins AS wins
            FROM ibl_standings
            WHERE $grouping = '$region'
            ORDER BY wins DESC
            LIMIT 1;";
        
        $resultWinningestTeam = $this->db->sql_query($queryWinningestTeam);
        $winningestTeamName = $this->db->sql_result($resultWinningestTeam, 0, "team_name");
        $winningestTeamWins = $this->db->sql_result($resultWinningestTeam, 0, "wins");

        $queryLeastLosingestTeam = "SELECT homeLosses + awayLosses AS losses
            FROM ibl_standings
            WHERE $grouping = '$region'
                AND team_name != '$winningestTeamName'
            ORDER BY losses ASC
            LIMIT 1;";
        
        $resultLeastLosingestTeam = $this->db->sql_query($queryLeastLosingestTeam);
        $leastLosingestTeamLosses = $this->db->sql_result($resultLeastLosingestTeam, 0, "losses");

        $magicNumber = 82 + 1 - $winningestTeamWins - $leastLosingestTeamLosses;

        if ($magicNumber <= 0) {
            $querySetTeamToClinched = "UPDATE ibl_standings
                SET clinched" . ucfirst($grouping) . " = 1
                WHERE team_name = '$winningestTeamName';";

            if ($this->db->sql_query($querySetTeamToClinched)) {
                echo "The $winningestTeamName have clinched the $region $grouping!";
            }
        }
    }

    private function checkIfPlayoffsClinched($conference) {
        echo "<p>Checking if any teams have clinched playoff spots in the $conference Conference...<br>";

        $queryEightWinningestTeams = "SELECT team_name, homeWins + awayWins AS wins
            FROM ibl_standings
            WHERE conference = '$conference'
            ORDER BY wins DESC
            LIMIT 8;";
        
        $resultEightWinningestTeams = $this->db->sql_query($queryEightWinningestTeams);

        $querySixLosingestTeams = "SELECT homeLosses + awayLosses AS losses
            FROM ibl_standings
            WHERE conference = '$conference'
            ORDER BY losses DESC
            LIMIT 6;";
        
        $resultSixLosingestTeams = $this->db->sql_query($querySixLosingestTeams);

        for ($i = 0; $i < 8; $i++) {
            $contendingTeamName = $this->db->sql_result($resultEightWinningestTeams, $i, "team_name");
            $contendingTeamWins = $this->db->sql_result($resultEightWinningestTeams, $i, "wins");
            $teamsEliminated = 0;

            for ($j = 0; $j < 6; $j++) {
                $bottomTeamLosses = $this->db->sql_result($resultSixLosingestTeams, $j, "losses");
                $magicNumber = 82 + 1 - $contendingTeamWins - $bottomTeamLosses;

                if ($magicNumber <= 0) {
                    $teamsEliminated++;
                }
            }

            if ($teamsEliminated == 6) {
                $querySetTeamToClinched = "UPDATE ibl_standings
                    SET clinchedPlayoffs = 1
                    WHERE team_name = '$contendingTeamName';";

                if ($this->db->sql_query($querySetTeamToClinched)) {
                    echo "The $contendingTeamName have clinched a playoff spot!<br>";
                }
            }
        }
    }
}
