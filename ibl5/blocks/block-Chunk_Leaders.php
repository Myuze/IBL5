<?php

if (!defined('BLOCK_FILE')) {
    Header("Location: ./index.php");
    die();
}

global $db;

$queryLastSimDates = $db->sql_query("SELECT * FROM ibl_sim_dates ORDER BY Sim DESC LIMIT 1");
$lastSimStartDate = $db->sql_result($queryLastSimDates, 0, "Start Date");
$lastSimEndDate = $db->sql_result($queryLastSimDates, 0, "End Date");

$querySimStatLeaders = "SELECT *
FROM (
    SELECT
        players.name,
        boxes.pid,
        players.teamname,
        players.tid,
        CAST(FORMAT((2 * SUM(boxes.game2GM) + SUM(boxes.gameFTM) + 3 * SUM(boxes.game3GM)) / COUNT(players.name), 1) AS DECIMAL(3,1)) AS stat_value,
        'Points' AS stat_type,
        ROW_NUMBER() OVER (ORDER BY (2 * SUM(boxes.game2GM) + SUM(boxes.gameFTM) + 3 * SUM(boxes.game3GM)) / COUNT(players.name) DESC) AS rn
    FROM ibl_box_scores boxes
    INNER JOIN ibl_plr players USING(pid)
    WHERE boxes.Date BETWEEN '$lastSimStartDate' AND '$lastSimEndDate'
    GROUP BY players.name, boxes.pid, players.teamname, players.tid

    UNION ALL

    SELECT
        players.name,
        boxes.pid,
        players.teamname,
        players.tid,
        CAST(FORMAT((SUM(boxes.gameORB) + SUM(boxes.gameDRB)) / COUNT(players.name), 1) AS DECIMAL(3,1)) AS stat_value,
        'Rebounds' AS stat_type,
        ROW_NUMBER() OVER (ORDER BY (SUM(boxes.gameORB) + SUM(boxes.gameDRB)) / COUNT(players.name) DESC) AS rn
    FROM ibl_box_scores boxes
    INNER JOIN ibl_plr players USING(pid)
    WHERE boxes.Date BETWEEN '$lastSimStartDate' AND '$lastSimEndDate'
    GROUP BY players.name, boxes.pid, players.teamname, players.tid

    UNION ALL

    SELECT
        players.name,
        boxes.pid,
        players.teamname,
        players.tid,
        CAST(FORMAT(SUM(boxes.gameAST) / COUNT(players.name), 1) AS DECIMAL(3,1)) AS stat_value,
        'Assists' AS stat_type,
        ROW_NUMBER() OVER (ORDER BY SUM(boxes.gameAST) / COUNT(players.name) DESC) AS rn
    FROM ibl_box_scores boxes
    INNER JOIN ibl_plr players USING(pid)
    WHERE boxes.Date BETWEEN '$lastSimStartDate' AND '$lastSimEndDate'
    GROUP BY players.name, boxes.pid, players.teamname, players.tid

    UNION ALL

    SELECT
        players.name,
        boxes.pid,
        players.teamname,
        players.tid,
        CAST(FORMAT(SUM(boxes.gameSTL) / COUNT(players.name), 1) AS DECIMAL(3,1)) AS stat_value,
        'Steals' AS stat_type,
        ROW_NUMBER() OVER (ORDER BY SUM(boxes.gameSTL) / COUNT(players.name) DESC) AS rn
    FROM ibl_box_scores boxes
    INNER JOIN ibl_plr players USING(pid)
    WHERE boxes.Date BETWEEN '$lastSimStartDate' AND '$lastSimEndDate'
    GROUP BY players.name, boxes.pid, players.teamname, players.tid

    UNION ALL

    SELECT
        players.name,
        boxes.pid,
        players.teamname,
        players.tid,
        CAST(FORMAT(SUM(boxes.gameBLK) / COUNT(players.name), 1) AS DECIMAL(3,1)) AS stat_value,
        'Blocks' AS stat_type,
        ROW_NUMBER() OVER (ORDER BY SUM(boxes.gameBLK) / COUNT(players.name) DESC) AS rn
    FROM ibl_box_scores boxes
    INNER JOIN ibl_plr players USING(pid)
    WHERE boxes.Date BETWEEN '$lastSimStartDate' AND '$lastSimEndDate'
    GROUP BY players.name, boxes.pid, players.teamname, players.tid
) t
WHERE rn <= 5
ORDER BY FIELD(stat_type, 'Points', 'Rebounds', 'Assists', 'Steals', 'Blocks'), rn;";
$resultSimStatLeaders = $db->sql_query($querySimStatLeaders);

$rows = $resultSimStatLeaders->fetch_all(MYSQLI_ASSOC);
$rowNumber = 0;

$content = "<center><a href=modules.php?name=Chunk_Stats&op=chunk>Sim Stats Search Engine</a></center><br>";
$content .= '<table style="border:1px solid #000066; margin: 0 auto;">
    <tr>';
for ($i = 1; $i <= 5; $i++) {
    $content .= '<td style="border:1px solid #000066;">
                <table>
                    <tr>
                        <td style="min-width:155px;" colspan=2>
                            <div style="text-align:center;">
                                <img src="./images/player/' . $rows[$rowNumber]['pid'] . '.jpg" height="90" width="65">
                                <img src="./images/logo/new' . $rows[$rowNumber]['tid'] . '.png" height="75" width="75">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="background-color:#000066; color:#ffffff; font-weight:bold;">
                            ' . $rows[$rowNumber]['stat_type'] . ' Per Game
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">
                            <a href="modules.php?name=Player&pa=showpage&pid=' . $rows[$rowNumber]['pid'] . '" style="color:#000066;">
                                ' . $rows[$rowNumber]['name'] . '
                            </a>
                            <br>
                            <a href="modules.php?name=Team&op=team&teamID=' . $rows[$rowNumber]['tid'] . '" style="color:#000066;">
                                ' . $rows[$rowNumber]['teamname'] . '
                            </a>
                        </td>
                        <td valign="top" style="font-weight:bold;">
                            ' . $rows[$rowNumber]['stat_value'] . '
                        </td>
                    </tr>';
    $rowNumber++;
    for ($j = 2; $j <= 5; $j++) {
        $content .= '<tr>
                        <td>
                            <a href="modules.php?name=Player&pa=showpage&pid=' . $rows[$rowNumber]['pid'] . '" style="color:#000066;">
                                ' . $rows[$rowNumber]['name'] . '
                            </a>
                            <br>
                            <a href="modules.php?name=Team&op=team&teamID=' . $rows[$rowNumber]['tid'] . '" style="color:#000066;">
                                ' . $rows[$rowNumber]['teamname'] . '
                            </a>
                        </td>
                        <td valign="top">
                            ' . $rows[$rowNumber]['stat_value'] . '
                        </td>
                    </tr>';
        $rowNumber++;
    }
    $content .= '</table>
            </td>';
}
$content .= '</tr>
</table>';

?>