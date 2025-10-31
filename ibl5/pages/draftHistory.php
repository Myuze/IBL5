<?php

use Services\DatabaseService;

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$queryfirstyear = "SELECT draftyear FROM ibl_plr ORDER BY draftyear ASC";
$resultfirstyear = $db->sql_query($queryfirstyear);
$startyear = $db->sql_result($resultfirstyear, 0, "draftyear");

$querylastyear = "SELECT draftyear FROM ibl_plr ORDER BY draftyear DESC";
$resultlastyear = $db->sql_query($querylastyear);
$endyear = $db->sql_result($resultlastyear, 0, "draftyear");

$year = $_REQUEST['year'];

$query = "SELECT * FROM ibl_plr WHERE draftyear = '$year' AND draftround > 0 ORDER BY draftround, draftpickno ASC";
$result = $db->sql_query($query);
$num = $db->sql_numrows($result);

echo "<html><head><title>Overview of $year IBL Draft</title></head><body>
<style>th{ font-size: 9pt; font-family:Arial; color:white; background-color: navy}td      { text-align: Left; font-size: 9pt; font-family:Arial; color:black; }.tdp { font-weight: bold; text-align: Left; font-size: 9pt; color:black; } </style>
";

echo "<center><h2>$year Draft</h2>
";

$startyear = 1988; // magic number reasoning: we kept players' real life draft years intact, but IBLv5's first non-dispersal draft was held in 1988.

while ($startyear < $endyear + 1) {
    echo "<a href=\"draft.php?year=$startyear\">$startyear</a> |
";
    $startyear++;
}

if ($num == 0) {
    echo "<br><br> Please select a draft year.";
} else {
    echo "<table>
<th>ROUND</th><th>PICK</th><th>Player</th><th>Selected by</th><th>Pic</th><th>College</th></tr>
";

    $i = 0;

    while ($i < $num) {
        $draftedby = DatabaseService::safeHtmlOutput($db->sql_result($result, $i, "draftedby")); // Safely escape for HTML
        $name = DatabaseService::safeHtmlOutput($db->sql_result($result, $i, "name")); // Safely escape for HTML
        $pid = $db->sql_result($result, $i, "pid");
        $round = $db->sql_result($result, $i, "draftround");
        $draftpickno = $db->sql_result($result, $i, "draftpickno");
        $college = DatabaseService::safeHtmlOutput($db->sql_result($result, $i, "college")); // Safely escape for HTML

        if ($i % 2) {
            echo "<tr bgcolor=#ffffff>";
        } else {
            echo "<tr bgcolor=#e6e7e2>";
        }

        echo "<td>$round</td>
            <td>$draftpickno</td>
            <td><a href=\"../ibl5/modules.php?name=Player&pa=showpage&pid=$pid\">$name</a></td>
            <td>$draftedby</td>
            <td><img height=50 src=\"../ibl5/images/player/$pid.jpg\"></td>
            <td>$college</td>
        </tr>
";

        $i++;
    }
}

$db->sql_close();

echo "</table></center></body></html>";
