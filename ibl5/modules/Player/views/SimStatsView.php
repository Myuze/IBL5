<?php

require_once __DIR__ . '/BaseView.php';

class SimStatsView extends BaseView {
    public function render() {
        echo "<table border=1 cellspacing=0 class=\"sortable\" style='margin: 0 auto;'>
            <tr>
                <td colspan=16 style='font-weight:bold;text-align:center;background-color:#00c;color:#fff;'>Sim Averages</td>
            </tr>
            <tr style=\"font-weight: bold\">
                <th>sim</th>
                <th>g</th>
                <th>min</th>
                <th>FGP</th>
                <th>FTP</th>
                <th>3GP</th>
                <th>orb</th>
                <th>reb</th>
                <th>ast</th>
                <th>stl</th>
                <th>to</th>
                <th>blk</th>
                <th>pf</th>
                <th>pts</th>
            </tr>";

        $resultSimDates = $this->db->sql_query("SELECT *
            FROM ibl_sim_dates
            ORDER BY sim DESC LIMIT 20");
        while ($simDates = $this->db->sql_fetchrow($resultSimDates)) {
            $simNumber = $simDates['Sim'];
            $simStartDate = $simDates['Start Date'];
            $simEndDate = $simDates['End Date'];

            $resultPlayerSimBoxScores = $this->db->sql_query("SELECT *
                FROM ibl_box_scores
                WHERE pid = " . $this->player->playerID . "
                AND Date BETWEEN '$simStartDate' AND '$simEndDate'
                ORDER BY Date ASC");

            $numberOfGamesPlayedInSim = $this->db->sql_numrows($resultPlayerSimBoxScores);
            $simTotalMIN = $simTotal2GM = $simTotal2GA = $simTotalFTM = $simTotalFTA = $simTotal3GM = $simTotal3GA = 0;
            $simTotalORB = $simTotalDRB = $simTotalAST = $simTotalSTL = $simTotalTOV = $simTotalBLK = $simTotalPF = $simTotalPTS = 0;

            while ($row = $this->db->sql_fetch_assoc($resultPlayerSimBoxScores)) {
                $simTotalMIN += $row['gameMIN'];
                $simTotal2GM += $row['game2GM'];
                $simTotal2GA += $row['game2GA'];
                $simTotalFTM += $row['gameFTM'];
                $simTotalFTA += $row['gameFTA'];
                $simTotal3GM += $row['game3GM'];
                $simTotal3GA += $row['game3GA'];
                $simTotalORB += $row['gameORB'];
                $simTotalDRB += $row['gameDRB'];
                $simTotalAST += $row['gameAST'];
                $simTotalSTL += $row['gameSTL'];
                $simTotalTOV += $row['gameTOV'];
                $simTotalBLK += $row['gameBLK'];
                $simTotalPF += $row['gamePF'];
                $simTotalPTS += (2 * $row['game2GM']) + $row['gameFTM'] + (3 * $row['game3GM']);
            }

            $simAverageMIN = ($numberOfGamesPlayedInSim) ? $simTotalMIN / $numberOfGamesPlayedInSim : "0.0";
            $simAverageFGP = ($simTotal2GA + $simTotal3GA) ? ($simTotal2GM + $simTotal3GM) / ($simTotal2GA + $simTotal3GA) : "0.000";
            $simAverageFTP = ($simTotalFTA) ? $simTotalFTM / $simTotalFTA : "0.000";
            $simAverage3GP = ($simTotal3GA) ? $simTotal3GM / $simTotal3GA : "0.000";
            $simAverageORB = ($numberOfGamesPlayedInSim) ? $simTotalORB / $numberOfGamesPlayedInSim : "0.0";
            $simAverageREB = ($numberOfGamesPlayedInSim) ? ($simTotalORB + $simTotalDRB) / $numberOfGamesPlayedInSim : "0.0";
            $simAverageAST = ($numberOfGamesPlayedInSim) ? $simTotalAST / $numberOfGamesPlayedInSim : "0.0";
            $simAverageSTL = ($numberOfGamesPlayedInSim) ? $simTotalSTL / $numberOfGamesPlayedInSim : "0.0";
            $simAverageTOV = ($numberOfGamesPlayedInSim) ? $simTotalTOV / $numberOfGamesPlayedInSim : "0.0";
            $simAverageBLK = ($numberOfGamesPlayedInSim) ? $simTotalBLK / $numberOfGamesPlayedInSim : "0.0";
            $simAveragePF = ($numberOfGamesPlayedInSim) ? $simTotalPF / $numberOfGamesPlayedInSim : "0.0";
            $simAveragePTS = ($numberOfGamesPlayedInSim) ? $simTotalPTS / $numberOfGamesPlayedInSim : "0.0";

            echo "<td>$simNumber</td>
            <td>$numberOfGamesPlayedInSim</td><td>";
            printf('%01.1f', $simAverageMIN);
            echo "</td><td>";
            printf('%01.3f', $simAverageFGP);
            echo "</td><td>";
            printf('%01.3f', $simAverageFTP);
            echo "</td><td>";
            printf('%01.3f', $simAverage3GP);
            echo "</td><td>";
            printf('%01.1f', $simAverageORB);
            echo "</td><td>";
            printf('%01.1f', $simAverageREB);
            echo "</td><td>";
            printf('%01.1f', $simAverageAST);
            echo "</td><td>";
            printf('%01.1f', $simAverageSTL);
            echo "</td><td>";
            printf('%01.1f', $simAverageTOV);
            echo "</td><td>";
            printf('%01.1f', $simAverageBLK);
            echo "</td><td>";
            printf('%01.1f', $simAveragePF);
            echo "</td><td>";
            printf('%01.1f', $simAveragePTS);
            echo "</td></tr>";

            // TODO: Add Season Averages to the bottom of this table for easy comparison between sim and season stats
        }

        echo "</table>";
    }
}