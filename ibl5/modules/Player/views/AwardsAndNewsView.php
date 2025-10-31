<?php

use Services\DatabaseService;

require_once __DIR__ . '/BaseView.php';

class AwardsAndNewsView extends BaseView {
    public function render() {
        $escapedName = DatabaseService::escapeString($this->db, $this->player->name);
        $awardsquery = $this->db->sql_query("SELECT * FROM ibl_awards WHERE name='" . $escapedName . "' ORDER BY year ASC");

        echo "<table border=1 cellspacing=0 cellpadding=0 valign=top style='margin: 0 auto;'>
            <tr>
                <td bgcolor=#0000cc align=center><b><font color=#ffffff>AWARDS</font></b></td>
            </tr>";

        while ($awardsrow = $this->db->sql_fetchrow($awardsquery)) {
            $award_year = stripslashes(check_html($awardsrow['year'], "nohtml"));
            $award_type = stripslashes(check_html($awardsrow['Award'], "nohtml"));

            echo "<tr>
                <td>$award_year $award_type</td>
            </tr>";
        }

        echo "<tr>
            <td bgcolor=#0000cc align=center><b><font color=#ffffff>ARTICLES MENTIONING THIS PLAYER</font></b></td>
        </tr>
        <tr>
            <td>";

        $escapedPlayerName = DatabaseService::escapeString($this->db, $this->player->name);
        $query = "SELECT *
            FROM nuke_stories
            WHERE (hometext LIKE '%$escapedPlayerName%' OR bodytext LIKE '%$escapedPlayerName%')
                AND (hometext NOT LIKE '%$escapedPlayerName II%' OR bodytext NOT LIKE '%$escapedPlayerName II%')
                ORDER BY time DESC;";
        $result = $this->db->sql_query($query);
        $num = $this->db->sql_numrows($result);

        echo "<small>";        
        $i = 0;
        while ($i < $num) {
            $sid = $this->db->sql_result($result, $i, "sid");
            $title = DatabaseService::safeHtmlOutput($this->db->sql_result($result, $i, "title"));
            $time = DatabaseService::safeHtmlOutput($this->db->sql_result($result, $i, "time"));

            echo "* <a href=\"modules.php?name=News&file=article&sid=$sid&mode=&order=0&thold=0\">$title</a> ($time)<br>";

            $i++;
        }
        echo "</small>
            </td></tr></table>";
    }
}