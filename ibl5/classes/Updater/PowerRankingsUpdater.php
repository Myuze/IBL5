<?php
namespace Updater;

class PowerRankingsUpdater {
    private $db;
    private $season;

    public function __construct($db, $season) {
        $this->db = $db;
        $this->season = $season;
    }

    public function update() {
        echo '<p>Updating the ibl_power database table...<p>';

        $log = '';

        $queryTeams = "SELECT TeamID, Team, streak_type, streak
            FROM ibl_power
            WHERE TeamID
            BETWEEN 1 AND 32
            ORDER BY TeamID ASC";
        $resultTeams = $this->db->sql_query($queryTeams);
        $numTeams = $this->db->sql_numrows($resultTeams);

        for ($i = 0; $i < $numTeams; $i++) {
            $tid = $this->db->sql_result($resultTeams, $i, "TeamID");
            $teamName = $this->db->sql_result($resultTeams, $i, "Team");

            $month = $this->determineMonth();
            $queryGames = $this->buildGamesQuery($tid, $month);
            $resultGames = $this->db->sql_query($queryGames);
            $numGames = $this->db->sql_numrows($resultGames);

            $stats = $this->calculateTeamStats($resultGames, $numGames, $tid);
            $log .= $this->updateTeamStats($tid, $teamName, $stats);
        }

        \UI::displayDebugOutput($log, 'Power Rankings Update Log');

        // Reset the sim's Depth Chart sent status
        $query = "UPDATE ibl_team_history SET sim_depth = 'No Depth Chart'";
        $this->db->sql_query($query);

        echo '<p>The ibl_power table has been updated.<p><br>';
    }

    private function determineMonth() {
        if ($this->season->phase == "Preseason") {
            return \Season::IBL_PRESEASON_MONTH;
        } elseif ($this->season->phase == "HEAT") {
            return \Season::IBL_HEAT_MONTH;
        } else {
            return \Season::IBL_REGULAR_SEASON_STARTING_MONTH;
        }
    }

    private function buildGamesQuery($tid, $month) {
        return "SELECT Visitor, VScore, Home, HScore
            FROM ibl_schedule
            WHERE (Visitor = $tid OR Home = $tid)
            AND (VScore > 0 AND HScore > 0)
            AND Date BETWEEN '" . ($this->season->beginningYear) . "-$month-01' AND '" . $this->season->endingYear . "-05-30'
            ORDER BY Date ASC";
    }

    private function calculateTeamStats($resultGames, $numGames, $tid) {
        $stats = array(
            'wins' => 0, 'losses' => 0,
            'homeWins' => 0, 'homeLosses' => 0,
            'awayWins' => 0, 'awayLosses' => 0,
            'winPoints' => 0, 'lossPoints' => 0,
            'winsInLast10Games' => 0, 'lossesInLast10Games' => 0,
            'streak' => 0, 'streakType' => ''
        );

        for ($j = 0; $j < $numGames; $j++) {
            $game = array(
                'awayTeam' => $this->db->sql_result($resultGames, $j, "Visitor"),
                'awayScore' => $this->db->sql_result($resultGames, $j, "VScore"),
                'homeTeam' => $this->db->sql_result($resultGames, $j, "Home"),
                'homeScore' => $this->db->sql_result($resultGames, $j, "HScore")
            );

            if ($game['awayScore'] !== $game['homeScore']) {
                $this->updateGameStats($stats, $game, $j, $numGames, $tid);
            }
        }

        return $stats;
    }

    private function updateGameStats(&$stats, $game, $currentGame, $totalGames, $tid) {
        if ($tid == $game['awayTeam']) {
            $opponentTeam = $game['homeTeam'];
            $isWin = $game['awayScore'] > $game['homeScore'];
            $isHome = false;
        } else {
            $opponentTeam = $game['awayTeam'];
            $isWin = $game['homeScore'] > $game['awayScore'];
            $isHome = true;
        }

        $queryOpponentWinLoss = "SELECT win, loss FROM ibl_power WHERE TeamID = $opponentTeam";
        $resultOpponentWinLoss = $this->db->sql_query($queryOpponentWinLoss);
        $opponentWins = $this->db->sql_result($resultOpponentWinLoss, 0, "win");
        $opponentLosses = $this->db->sql_result($resultOpponentWinLoss, 0, "loss");

        if ($isWin) {
            $stats['wins']++;
            $stats['winPoints'] += $opponentWins;
            if ($isHome) {
                $stats['homeWins']++;
            } else {
                $stats['awayWins']++;
            }
            if ($currentGame >= $totalGames - 10) {
                $stats['winsInLast10Games']++;
            }
            $stats['streak'] = ($stats['streakType'] == "W") ? ++$stats['streak'] : 1;
            $stats['streakType'] = "W";
        } else {
            $stats['losses']++;
            $stats['lossPoints'] += $opponentLosses;
            if ($isHome) {
                $stats['homeLosses']++;
            } else {
                $stats['awayLosses']++;
            }
            if ($currentGame >= $totalGames - 10) {
                $stats['lossesInLast10Games']++;
            }
            $stats['streak'] = ($stats['streakType'] == "L") ? ++$stats['streak'] : 1;
            $stats['streakType'] = "L";
        }
    }

    private function updateTeamStats($tid, $teamName, $stats) {
        $log = '';

        $gb = ($stats['wins'] / 2) - ($stats['losses'] / 2);
        $stats['winPoints'] += $stats['wins'];
        $stats['lossPoints'] += $stats['losses'];
        $ranking = ($stats['winPoints'] + $stats['lossPoints']) 
            ? round(($stats['winPoints'] / ($stats['winPoints'] + $stats['lossPoints'])) * 100, 1) 
            : 0;

        // Update ibl_power
        $query = "UPDATE ibl_power SET
            win = {$stats['wins']},
            loss = {$stats['losses']},
            gb = $gb,
            home_win = {$stats['homeWins']},
            home_loss = {$stats['homeLosses']},
            road_win = {$stats['awayWins']},
            road_loss = {$stats['awayLosses']},
            last_win = {$stats['winsInLast10Games']},
            last_loss = {$stats['lossesInLast10Games']},
            streak_type = '{$stats['streakType']}',
            streak = {$stats['streak']},
            ranking = $ranking
            WHERE TeamID = $tid";
        
        $this->db->sql_query($query);

        $log .= "Updating $teamName: {$stats['wins']} wins, {$stats['losses']} losses, $gb games back, 
            {$stats['homeWins']} home wins, {$stats['homeLosses']} home losses, 
            {$stats['awayWins']} away wins, {$stats['awayLosses']} away losses, 
            streak = {$stats['streakType']}{$stats['streak']}, 
            last 10 = {$stats['winsInLast10Games']}-{$stats['lossesInLast10Games']}, 
            ranking score = $ranking<br>";

        $this->updateSeasonRecords($teamName);
        
        if ($this->season->phase == "HEAT" && $stats['wins'] != 0 && $stats['losses'] != 0) {
            $this->updateHeatRecords($teamName);
        }

        $this->updateHistoricalRecords();

        return $log;
    }

    private function updateSeasonRecords($teamName) {
        $query = "UPDATE ibl_team_win_loss a, ibl_power b
            SET a.wins = b.win,
                a.losses = b.loss
            WHERE a.currentname = b.Team 
            AND a.year = '{$this->season->endingYear}'";
        $this->db->sql_query($query);
    }

    private function updateHeatRecords($teamName) {
        $query = "UPDATE ibl_heat_win_loss a, ibl_power b
            SET a.wins = b.win,
                a.losses = b.loss
            WHERE a.currentname = b.Team 
            AND a.year = '{$this->season->beginningYear}'";
        
        if ($this->db->sql_query($query)) {
            echo $query . "<p>";
        } else {
            echo "<b>`ibl_heat_win_loss` update FAILED for $teamName! Have you <A HREF=\"leagueControlPanel.php\">inserted new database rows</A> for the new HEAT season?</b><p>";
        }
    }

    private function updateHistoricalRecords() {
        // Update total wins
        $query = "UPDATE ibl_team_history a
            SET totwins = (
                SELECT SUM(b.wins)
                FROM ibl_team_win_loss AS b
                WHERE a.team_name = b.currentname
            )
            WHERE a.team_name != 'Free Agents'";
        $this->db->sql_query($query);

        // Update total losses
        $query = "UPDATE ibl_team_history a
            SET totloss = (
                SELECT SUM(b.losses)
                FROM ibl_team_win_loss AS b
                WHERE a.team_name = b.currentname
            )
            WHERE a.team_name != 'Free Agents'";
        $this->db->sql_query($query);

        // Update win percentage
        $query = "UPDATE ibl_team_history a 
            SET winpct = a.totwins / (a.totwins + a.totloss)
            WHERE a.team_name != 'Free Agents'";
        $this->db->sql_query($query);
    }
}
