<?php

require_once __DIR__ . '/BaseView.php';

class OverviewView extends BaseView {
    private $season;
    private $sharedFunctions;

    public function __construct($db, Player $player, PlayerStats $playerStats, Season $season, Shared $sharedFunctions) {
        parent::__construct($db, $player, $playerStats);
        $this->season = $season;
        $this->sharedFunctions = $sharedFunctions;
    }

    public function render() {
        $allstarquery = $this->db->sql_query("SELECT * FROM ibl_awards WHERE name='" . $this->player->name . "' AND Award LIKE '%Conference All-Star'");
        $asg = $this->db->sql_numrows($allstarquery);

        $allstarquery2 = $this->db->sql_query("SELECT * FROM ibl_awards WHERE name='" . $this->player->name . "' AND Award LIKE 'Three-Point Contest%'");
        $threepointcontests = $this->db->sql_numrows($allstarquery2);

        $allstarquery3 = $this->db->sql_query("SELECT * FROM ibl_awards WHERE name='" . $this->player->name . "' AND Award LIKE 'Slam Dunk Competition%'");
        $dunkcontests = $this->db->sql_numrows($allstarquery3);
        
        $allstarquery4 = $this->db->sql_query("SELECT * FROM ibl_awards WHERE name='" . $this->player->name . "' AND Award LIKE 'Rookie-Sophomore Challenge'");
        $rooksoph = $this->db->sql_numrows($allstarquery4);

        echo "<table align=left cellspacing=1 cellpadding=0 border=1>
            <th colspan=2><center>All-Star Activity</center></th>
            <tr>
                <td><b>All Star Games:</b></td>
                <td>$asg</td>
            </tr>
            <tr>
                <td><b>Three-Point<br>Contests:</b></td>
                <td>$threepointcontests</td>
            </tr>
            <tr>
                <td><b>Slam Dunk<br>Competitions:</b></td>
                <td>$dunkcontests</td>
            </tr>
            <tr>
                <td><b>Rookie-Sophomore<br>Challenges:</b></td>
                <td>$rooksoph</td>
            </tr>
        </table>
        <center>
        <table>
            <tr align=center>
                <td><b>Talent</b></td>
                <td><b>Skill</b></td>
                <td><b>Intangibles</b></td>
                <td><b>Clutch</b></td>
                <td><b>Consistency</b></td>
            </tr>
            <tr align=center>
                <td>" . $this->player->ratingTalent . "</td>
                <td>" . $this->player->ratingSkill . "</td>
                <td>" . $this->player->ratingIntangibles . "</td>
                <td>" . $this->player->ratingClutch . "</td>
                <td>" . $this->player->ratingConsistency . "</td>
            </tr>
        </table>
        <table>
            <tr>
                <td><b>Loyalty</b></td>
                <td><b>Play for Winner</b></td>
                <td><b>Playing Time</b></td>
                <td><b>Security</b></td>
                <td><b>Tradition</b></td>
            </tr>
            <tr align=center>
                <td>" . $this->player->freeAgencyLoyalty . "</td>
                <td>" . $this->player->freeAgencyPlayForWinner . "</td>
                <td>" . $this->player->freeAgencyPlayingTime . "</td>
                <td>" . $this->player->freeAgencySecurity . "</td>
                <td>" . $this->player->freeAgencyTradition . "</td>
            </tr>
        </table>
        </center>";

        if ($this->season->phase == "Preseason") {
            $query = "SELECT * FROM ibl_box_scores WHERE Date BETWEEN '" . $this->season->beginningYear . "-" . Season::IBL_PRESEASON_MONTH . "-01' AND '" . $this->season->endingYear . "-07-01' AND pid = " . $this->player->playerID . " ORDER BY Date ASC";
        } elseif ($this->season->phase == "HEAT") {
            $query = "SELECT * FROM ibl_box_scores WHERE Date BETWEEN '" . $this->season->beginningYear . "-" . Season::IBL_HEAT_MONTH . "-01' AND '" . $this->season->endingYear . "-07-01' AND pid = " . $this->player->playerID . " ORDER BY Date ASC";
        } else {
            $query = "SELECT * FROM ibl_box_scores WHERE Date BETWEEN '" . $this->season->beginningYear . "-" . Season::IBL_REGULAR_SEASON_STARTING_MONTH . "-01' AND '" . $this->season->endingYear . "-07-01' AND pid = " . $this->player->playerID . " ORDER BY Date ASC";
        }
        $result = $this->db->sql_query($query);

        echo "<p>
            <H1><center>GAME LOG</center></H1>
            <p>
            <table class=\"sortable\" width=\"100%\">
                <tr>
                    <th>Date</th>
                    <th>Away</th>
                    <th>Home</th>
                    <th>MIN</th>
                    <th>PTS</th>
                    <th>FGM</th>
                    <th>FGA</th>
                    <th>FG%</th>
                    <th>FTM</th>
                    <th>FTA</th>
                    <th>FT%</th>
                    <th>3GM</th>
                    <th>3GA</th>
                    <th>3G%</th>
                    <th>ORB</th>
                    <th>DRB</th>
                    <th>REB</th>
                    <th>AST</th>
                    <th>STL</th>
                    <th>TO</th>
                    <th>BLK</th>
                    <th>PF</th>
                </tr>";

        echo "<style>
            td {}
            .gamelog {text-align: center;}
        </style>";

        while ($row = $this->db->sql_fetch_assoc($result)) {
            $fieldGoalPercentage = ($row['game2GA'] + $row['game3GA']) ? number_format(($row['game2GM'] + $row['game3GM']) / ($row['game2GA'] + $row['game3GA']), 3, '.', '') : "0.000";
            $freeThrowPercentage = ($row['gameFTA']) ? number_format($row['gameFTM'] / $row['gameFTA'], 3, '.', '') : "0.000";
            $threePointPercentage = ($row['game3GA']) ? number_format($row['game3GM'] / $row['game3GA'], 3, '.', '') : "0.000";

            echo "<tr>
                <td class=\"gamelog\">" . $row['Date'] . "</td>
                <td class=\"gamelog\">" . $this->sharedFunctions->getTeamnameFromTeamID($row['homeTID']) . "</td>
                <td class=\"gamelog\">" . $this->sharedFunctions->getTeamnameFromTeamID($row['visitorTID']) . "</td>
                <td class=\"gamelog\">" . $row['gameMIN'] . "</td>
                <td class=\"gamelog\">" . ((2 * $row['game2GM']) + (3 * $row['game3GM']) + $row['gameFTM']) . "</td>
                <td class=\"gamelog\">" . ($row['game2GM'] + $row['game3GM']) . "</td>
                <td class=\"gamelog\">" . ($row['game2GA'] + $row['game3GA']) . "</td>
                <td class=\"gamelog\">" . $fieldGoalPercentage . "</td>
                <td class=\"gamelog\">" . $row['gameFTM'] . "</td>
                <td class=\"gamelog\">" . $row['gameFTA'] . "</td>
                <td class=\"gamelog\">" . $freeThrowPercentage . "</td>
                <td class=\"gamelog\">" . $row['game3GM'] . "</td>
                <td class=\"gamelog\">" . $row['game3GA'] . "</td>
                <td class=\"gamelog\">" . $threePointPercentage . "</td>
                <td class=\"gamelog\">" . $row['gameORB'] . "</td>
                <td class=\"gamelog\">" . $row['gameDRB'] . "</td>
                <td class=\"gamelog\">" . ($row['gameORB'] + $row['gameDRB']) . "</td>
                <td class=\"gamelog\">" . $row['gameAST'] . "</td>
                <td class=\"gamelog\">" . $row['gameSTL'] . "</td>
                <td class=\"gamelog\">" . $row['gameTOV'] . "</td>
                <td class=\"gamelog\">" . $row['gameBLK'] . "</td>
                <td class=\"gamelog\">" . $row['gamePF'] . "</td>
            </tr>";
        }
        echo "</table>";
    }
}