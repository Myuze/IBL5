<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$arrayStatNames = array(
    'POINTS',
    'REBOUNDS',
    'ASSISTS',
    'STEALS',
    'BLOCKS',
    'TURNOVERS',
    'Field Goals Made',
    'Free Throws Made',
    'Three Pointers Made',
);

$arrayStatQueries = array(
    '(`game2GM`*2) + `gameFTM` + (`game3GM`*3)',
    '(`gameORB` + `gameDRB`)',
    '`gameAST`',
    '`gameSTL`',
    '`gameBLK`',
    '`gameTOV`',
    '(`game2GM` + `game3GM`)',
    '`gameFTM`',
    '`game3GM`',
);

function seasonHighTable($queryForStat, $statName, $playerOrTeam)
{
    global $db;

    if ($playerOrTeam == 'player') {
        $isPlayer = 'pid != 0';
    } elseif ($playerOrTeam == 'team') {
        $isPlayer = 'pid = 0';
    }

    $query = "SELECT `name`, `date`, " . $queryForStat . " AS `" . $statName . "`
        FROM ibl_box_scores
        WHERE " . $isPlayer . "
        ORDER BY `" . $statName . "` DESC, date ASC LIMIT 15;";
    $result = $db->sql_query($query);
    $numRows = $db->sql_numrows($result);

    echo "\t\t\t<table border=1>\n";
    echo "\t\t\t\t<th colspan=4 align=center>$statName</th>\n";
    $i = 0;
    while ($i < $numRows) {
        echo "\t\t\t\t<tr>\n";
        echo "\t\t\t\t\t<td align=center>\n";
        echo "\t\t\t\t\t\t" . ($i + 1) . "\n";
        echo "\t\t\t\t\t</td>\n";
        $j = 0;
        while ($j < 3) {
            echo "\t\t\t\t\t<td>\n";
            echo "\t\t\t\t\t\t" . $db->sql_result($result, $i, $j) . "\n";
            echo "\t\t\t\t\t</td>\n";
            $j++;
        }
        echo "\t\t\t\t</tr>\n";
        $i++;
    }
    echo "\t\t\t</table>\n";
}

function nextTableColumn()
{
    echo "\t\t</td>\n";
    echo "\t\t<td align=center>\n";
}

function startTableRow()
{
    echo "\t<tr>\n";
    echo "\t\t<td align=center>\n";
}

function endTableRow()
{
    echo "\t\t</td>\n";
    echo "\t</tr>\n";
}

echo "<html><head><title>Season Stat Leaders</title></head>\n\n";
echo "<body>\n\n";

echo "<H1>Players' Season Highs<H1>\n\n";
$playerOrTeam = 'player';

echo "<table cellpadding=5>\n";

startTableRow();
seasonHighTable(reset($arrayStatQueries), reset($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
endTableRow();

startTableRow();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
endTableRow();

startTableRow();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
endTableRow();

echo "</table>\n\n";

echo "<H1>Teams' Season Highs</H1>\n\n";
$playerOrTeam = 'team';

echo "<table cellpadding=5>\n";

startTableRow();
seasonHighTable(reset($arrayStatQueries), reset($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
endTableRow();

startTableRow();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
endTableRow();

startTableRow();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
nextTableColumn();
seasonHighTable(next($arrayStatQueries), next($arrayStatNames), $playerOrTeam);
endTableRow();

echo "</table>\n\n";

echo "</body>\n";
echo "</html>";
