<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$query1 = "SELECT * FROM ibl_plr";
$result1 = $db->sql_query($query1);
$num1 = $db->sql_numrows($result1);

$counter = 0;
$i = 0;

echo "<HTML><HEAD><TITLE>H.E.A.T. LEADERBOARD UPDATE (heatupdate.php and heatlbupdate.php combined)</TITLE></HEAD><BODY>";

echo "<p>H.E.A.T. LEADERBOARD UPDATE (heatupdate.php)<p>";

while ($i < $num1) {
    $playername = $db->sql_result($result1, $i, "name");
    $playerid = $db->sql_result($result1, $i, "pid");

    $query2 = "SELECT * FROM ibl_heat_stats WHERE name LIKE '$playername'";
    $result2 = $db->sql_query($query2);
    @$num2 = $db->sql_numrows($result2);

    $j = 0;
    $tot_games = 0;
    $tot_minutes = 0;
    $tot_fgm = 0;
    $tot_fga = 0;
    $tot_ftm = 0;
    $tot_fta = 0;
    $tot_tgm = 0;
    $tot_tga = 0;
    $tot_orb = 0;
    $tot_reb = 0;
    $tot_ast = 0;
    $tot_stl = 0;
    $tot_tvr = 0;
    $tot_blk = 0;
    $tot_pf = 0;
    $tot_pts = 0;

    while ($j < $num2) {

        $games = $db->sql_result($result2, $j, "games");
        $minutes = $db->sql_result($result2, $j, "minutes");
        $fgm = $db->sql_result($result2, $j, "fgm");
        $fga = $db->sql_result($result2, $j, "fga");
        $ftm = $db->sql_result($result2, $j, "ftm");
        $fta = $db->sql_result($result2, $j, "fta");
        $tgm = $db->sql_result($result2, $j, "tgm");
        $tga = $db->sql_result($result2, $j, "tga");
        $orb = $db->sql_result($result2, $j, "orb");
        $reb = $db->sql_result($result2, $j, "reb");
        $ast = $db->sql_result($result2, $j, "ast");
        $stl = $db->sql_result($result2, $j, "stl");
        $tvr = $db->sql_result($result2, $j, "tvr");
        $blk = $db->sql_result($result2, $j, "blk");
        $pf = $db->sql_result($result2, $j, "pf");
        $pts = $fgm + $fgm + $ftm + $tgm;

        $tot_games = $tot_games + $games;
        $tot_minutes = $tot_minutes + $minutes;
        $tot_fgm = $tot_fgm + $fgm;
        $tot_fga = $tot_fga + $fga;
        $tot_ftm = $tot_ftm + $ftm;
        $tot_fta = $tot_fta + $fta;
        $tot_tgm = $tot_tgm + $tgm;
        $tot_tga = $tot_tga + $tga;
        $tot_orb = $tot_orb + $orb;
        $tot_reb = $tot_reb + $reb;
        $tot_ast = $tot_ast + $ast;
        $tot_stl = $tot_stl + $stl;
        $tot_tvr = $tot_tvr + $tvr;
        $tot_blk = $tot_blk + $blk;
        $tot_pf = $tot_pf + $pf;
        $tot_pts = $tot_pts + $pts;

        $j++;
    }

    echo "Updating $playername's records... $tot_games total games.<br>";

    $query3 = "DELETE FROM ibl_heat_career_totals WHERE `name` = '$playername'";
    $result3 = $db->sql_query($query3);

    if ($tot_games > 0) {

        $query4 = "INSERT INTO ibl_heat_career_totals (`pid` , `name` , `games` , `minutes` , `fgm` , `fga` , `ftm` , `fta` , `tgm` , `tga` , `orb` , `reb` , `ast` , `stl` , `tvr` , `blk` , `pf` , `pts` ) VALUES ( '$playerid' ,  '$playername' ,  '$tot_games' , '$tot_minutes' , '$tot_fgm' , '$tot_fga' , '$tot_ftm' , '$tot_fta' , '$tot_tgm' , '$tot_tga' , '$tot_orb' , '$tot_reb' , '$tot_ast' , '$tot_stl' , '$tot_tvr' , '$tot_blk' , '$tot_pf' , '$tot_pts' ) ";
        $result4 = $db->sql_query($query4);
        $counter = $counter + 1;
    }

    $i++;
}

echo "Updated $counter records";

echo "<p>H.E.A.T. LEADERBOARD UPDATE (heatlbupdate.php)<p>";

$counter = 0;
$i = 0;

while ($i < $num1) {
    $playername = $db->sql_result($result1, $i, "name");
    $playerid = $db->sql_result($result1, $i, "pid");

    $query2 = "SELECT * FROM ibl_heat_career_totals WHERE name LIKE '$playername'";
    $result2 = $db->sql_query($query2);
    @$num2 = $db->sql_numrows($result2);

    $j = 0;
    $tot_games = 0;
    $tot_minutes = 0.0;
    $tot_fgm = 0;
    $tot_fga = 0;
    $tot_ftm = 0;
    $tot_fta = 0;
    $tot_tgm = 0;
    $tot_tga = 0;
    $tot_orb = 0;
    $tot_reb = 0;
    $tot_ast = 0;
    $tot_stl = 0;
    $tot_tvr = 0;
    $tot_blk = 0;
    $tot_pf = 0;
    $tot_pts = 0;

    while ($j < $num2) {

        $games = $db->sql_result($result2, $j, "games");
        $minutes = $db->sql_result($result2, $j, "minutes");
        $fgm = $db->sql_result($result2, $j, "fgm");
        $fga = $db->sql_result($result2, $j, "fga");
        $ftm = $db->sql_result($result2, $j, "ftm");
        $fta = $db->sql_result($result2, $j, "fta");
        $tgm = $db->sql_result($result2, $j, "tgm");
        $tga = $db->sql_result($result2, $j, "tga");
        $orb = $db->sql_result($result2, $j, "orb");
        $reb = $db->sql_result($result2, $j, "reb");
        $ast = $db->sql_result($result2, $j, "ast");
        $stl = $db->sql_result($result2, $j, "stl");
        $tvr = $db->sql_result($result2, $j, "tvr");
        $blk = $db->sql_result($result2, $j, "blk");
        $pf = $db->sql_result($result2, $j, "pf");
        $pts = $db->sql_result($result2, $j, "pts");

        $tot_games = $tot_games + $games;
        $avg_minutes = $tot_games ? $tot_minutes + $minutes / $tot_games : "0.00";
        $tot_fgm = $tot_games ? $tot_fgm + $fgm / $tot_games : "0.00";
        $tot_fga = $tot_games ? $tot_fga + $fga / $tot_games : "0.00";
        $tot_fgpct = $fga ? $fgm / $fga : "0.000";
        $tot_ftm = $tot_games ? $tot_ftm + $ftm / $tot_games : "0.00";
        $tot_fta = $tot_games ? $tot_fta + $fta / $tot_games : "0.00";
        $tot_ftpct = $fta ? $ftm / $fta : "0.000";
        $tot_tgm = $tot_games ? $tot_tgm + $tgm / $tot_games : "0.00";
        $tot_tga = $tot_games ? $tot_tga + $tga / $tot_games : "0.00";
        $tot_tpct = $tga ? $tgm / $tga : "0.000";
        $tot_orpg = $tot_games ? $tot_orb + $orb / $tot_games : "0.00";
        $tot_rpg = $tot_games ? $tot_reb + $reb / $tot_games : "0.00";
        $tot_apg = $tot_games ? $tot_ast + $ast / $tot_games : "0.00";
        $tot_spg = $tot_games ? $tot_stl + $stl / $tot_games : "0.00";
        $tot_tpg = $tot_games ? $tot_tvr + $tvr / $tot_games : "0.00";
        $tot_bpg = $tot_games ? $tot_blk + $blk / $tot_games : "0.00";
        $tot_fpg = $tot_games ? $tot_pf + $pf / $tot_games : "0.00";
        $tot_ppg = $tot_games ? $tot_pts + $pts / $tot_games : "0.00";

        $j++;
    }

    echo "Updating $playername's records... $tot_games total games.<br>";

    $query3 = "DELETE FROM ibl_heat_career_avgs WHERE `name` = '$playername'";
    $result3 = $db->sql_query($query3);

    if ($tot_games > 0) {

        $query4 = "INSERT INTO ibl_heat_career_avgs (`pid` , `name` , `games` , `minutes` , `fgm` , `fga` , `fgpct` , `ftm` , `fta` , `ftpct` , `tgm` , `tga` , `tpct` , `orb` , `reb` , `ast` , `stl` , `tvr` , `blk` , `pf` , `pts` ) VALUES ( '$playerid' , '$playername' , '$tot_games' , '$avg_minutes' , '$tot_fgm' , '$tot_fga' , '$tot_fgpct' , '$tot_ftm' , '$tot_fta' , '$tot_ftpct' , '$tot_tgm' , '$tot_tga' , '$tot_tpct' , '$tot_orpg' , '$tot_rpg' , '$tot_apg' , '$tot_spg' , '$tot_tpg' , '$tot_bpg' , '$tot_fpg' , '$tot_ppg' ) ";
        $result4 = $db->sql_query($query4);
        $counter = $counter + 1;
    }

    $i++;
}

echo "Updated $counter records</BODY></HTML>";
