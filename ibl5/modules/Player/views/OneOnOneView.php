<?php

use Services\DatabaseService;

require_once __DIR__ . '/BaseView.php';

class OneOnOneView extends BaseView {
    public function render() {
        echo "<table style='margin: 0 auto;'>
            <tr>
                <td bgcolor=#0000cc align=center><b><font color=#ffffff>ONE-ON-ONE RESULTS</font></b></td>
            </tr>
            <tr>
                <td>";

        $escapedPlayerName = DatabaseService::escapeString($this->db, str_replace("%20", " ", $this->player->name));

        $query = "SELECT * FROM ibl_one_on_one WHERE winner = '$escapedPlayerName' ORDER BY gameid ASC";
        $result = $this->db->sql_query($query);
        $num = $this->db->sql_numrows($result);

        $wins = $losses = 0;

        $i = 0;
        while ($i < $num) {
            $gameid = $this->db->sql_result($result, $i, "gameid");
            $winner = DatabaseService::safeHtmlOutput($this->db->sql_result($result, $i, "winner"));
            $loser = DatabaseService::safeHtmlOutput($this->db->sql_result($result, $i, "loser"));
            $winscore = $this->db->sql_result($result, $i, "winscore");
            $lossscore = $this->db->sql_result($result, $i, "lossscore");

            echo "* def. $loser, $winscore-$lossscore (# $gameid)<br>";

            $wins++;
            $i++;
        }

        $query = "SELECT * FROM ibl_one_on_one WHERE loser = '$escapedPlayerName' ORDER BY gameid ASC";
        $result = $this->db->sql_query($query);
        $num = $this->db->sql_numrows($result);
        $i = 0;

        while ($i < $num) {
            $gameid = $this->db->sql_result($result, $i, "gameid");
            $winner = DatabaseService::safeHtmlOutput($this->db->sql_result($result, $i, "winner"));
            $loser = DatabaseService::safeHtmlOutput($this->db->sql_result($result, $i, "loser"));
            $winscore = $this->db->sql_result($result, $i, "winscore");
            $lossscore = $this->db->sql_result($result, $i, "lossscore");

            echo "* lost to $winner, $lossscore-$winscore (# $gameid)<br>";

            $losses++;
            $i++;
        }

        echo "<b><center>Record: $wins - $losses</center></b><br>
            </table>";
    }
}