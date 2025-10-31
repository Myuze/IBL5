<?php
/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2005 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!defined('BLOCK_FILE')) {
    Header("Location: ../index.php");
    die();
}

use Services\DatabaseService;

global $db;

$max_chunk_query = "SELECT MAX(chunk) as maxchunk FROM ibl_plr_chunk WHERE active = 1";
$max_chunk_result = $db->sql_query($max_chunk_query);
$row = $db->sql_fetch_assoc($max_chunk_result);

//$query2="SELECT * FROM ibl_plr_chunk WHERE chunk = $row[maxchunk] ORDER BY pid ASC";
//$result2 = $db->sql_query($query2);
//$new_name=$db->sql_result($result2,0,"name");

$pg = all_chunk("PG", $row);
$sg = all_chunk("SG", $row);
$sf = all_chunk("SF", $row);
$pf = all_chunk("PF", $row);
$c = all_chunk("C", $row);

$content = $content . "<center><table><tr>";
$content = $content . $pg . $sg . $sf . $pf . $c . "</tr></table>";
/*
$content = $content."<table><tr><td><table><tr><td bgcolor=#660000><b><font color=#ffffff>Name:</td><td bgcolor=#660000><b><font color=#ffffff>Points</td></tr><tr><td><b><a href=modules.php?name=Player&pa=showpage&pid=$pid1>$name1</a><br>$teamname1</td><td>$ppg1</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pid2>$name2</a><br>$teamname2</td><td>$ppg2</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pid3>$name3</a><br>$teamname3</td><td>$ppg3</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pid4>$name4</a><br>$teamname4</td><td>$ppg4</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pid5>$name5</a><br>$teamname5</td><td>$ppg5</td></tr>";
$content = $content."<tr><td><img src=\"../images/player/$pid1.jpg\"></td></tr></table></td>";

$content = $content."<td><table><tr><td bgcolor=#660000><b><font color=#ffffff>Name:</td><td bgcolor=#660000><b><font color=#ffffff>Rebounds</td></tr><tr><td><b><a href=modules.php?name=Player&pa=showpage&pid=$pidreb1>$name_reb1</a><br>$teamname_reb1</td><td>$reb1</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidreb2>$name_reb2</a><br>$teamname_reb2</td><td>$reb2</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidreb3>$name_reb3</a><br>$teamname_reb3</td><td>$reb3</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidreb4>$name_reb4</a><br>$teamname_reb4</td><td>$reb4</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidreb5>$name_reb5</a><br>$teamname_reb5</td><td>$reb5</td></tr>";
$content = $content."<tr><td><img src=\"../images/player/$pidreb1.jpg\"></td></tr></table></td>";

$content = $content."<td><table><tr><td bgcolor=#660000><b><font color=#ffffff>Name:</td><td bgcolor=#660000><b><font color=#ffffff>Assists</td></tr><tr><td><b><a href=modules.php?name=Player&pa=showpage&pid=$pidast1>$name_ast1</a><br>$teamname_ast1</td><td>$ast1</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidast2>$name_ast2</a><br>$teamname_ast2</td><td>$ast2</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidast3>$name_ast3</a><br>$teamname_ast3</td><td>$ast3</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidast4>$name_ast4</a><br>$teamname_ast4</td><td>$ast4</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidast5>$name_ast5</a><br>$teamname_ast5</td><td>$ast5</td></tr>";
$content = $content."<tr><td><img src=\"../images/player/$pidast1.jpg\"></td></tr></table></td>";

$content = $content."<td><table><tr><td bgcolor=#660000><b><font color=#ffffff>Name:</td><td bgcolor=#660000><b><font color=#ffffff>Steals</td></tr><tr><td><b><a href=modules.php?name=Player&pa=showpage&pid=$pidstl1>$name_stl1</a><br>$teamname_stl1</td><td>$stl1</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidstl2>$name_stl2</a><br>$teamname_stl2</td><td>$stl2</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidstl3>$name_stl3</a><br>$teamname_stl3</td><td>$stl3</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidstl4>$name_stl4</a><br>$teamname_stl4</td><td>$stl4</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidstl5>$name_stl5</a><br>$teamname_stl5</td><td>$stl5</td></tr>";
$content = $content."<tr><td><img src=\"../images/player/$pidstl1.jpg\"></td></tr></table></td>";

$content = $content."<td><table><tr><td bgcolor=#660000><b><font color=#ffffff>Name:</td><td bgcolor=#660000><b><font color=#ffffff>Blocks</td></tr><tr><td><b><a href=modules.php?name=Player&pa=showpage&pid=$pidblk1>$name_blk1</a><br>$teamname_blk1</td><td>$blk1</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidblk2>$name_blk2</a><br>$teamname_blk2</td><td>$blk2</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidblk3>$name_blk3</a><br>$teamname_blk3</td><td>$blk3</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidblk4>$name_blk4</a><br>$teamname_blk4</td><td>$blk4</td></tr>";
$content = $content."<tr><td><a href=modules.php?name=Player&pa=showpage&pid=$pidblk5>$name_blk5</a><br>$teamname_blk5</td><td>$blk5</td></tr>";
$content = $content."<tr><td><img src=\"../images/player/$pidblk1.jpg\"></td></tr></table></td></tr></table>";
 */
function all_chunk($pos, $row)
{
    global $db;

    $querypoc = "SELECT * FROM ibl_plr_chunk WHERE chunk = $row[maxchunk] ORDER BY qa DESC";
    $resultpoc = $db->sql_query($querypoc);
    $pospoc = $db->sql_result($resultpoc, 0, "pos");

    $query = "SELECT * FROM ibl_plr_chunk WHERE chunk = $row[maxchunk] AND pos = '$pos' ORDER BY qa DESC";
    $result = $db->sql_query($query);
    $name = DatabaseService::safeHtmlOutput($db->sql_result($result, 0, "name")); // Safely escape for HTML
    $pid = $db->sql_result($result, 0, "pid");
    $tid = $db->sql_result($result, 0, "tid");
    $teamname = $db->sql_result($result, 0, "teamname");

    $stats_gm = $db->sql_result($result, 0, "stats_gm");
    $stats_fgm = $db->sql_result($result, 0, "stats_fgm");
    $stats_fga = $db->sql_result($result, 0, "stats_fga");
    $stats_ftm = $db->sql_result($result, 0, "stats_ftm");
    $stats_fta = $db->sql_result($result, 0, "stats_fta");
    $stats_tgm = $db->sql_result($result, 0, "stats_3gm");
    $stats_tga = $db->sql_result($result, 0, "stats_3ga");

    $stats_orb = $db->sql_result($result, 0, "stats_orb");
    $stats_drb = $db->sql_result($result, 0, "stats_drb");

    $stats_ast = $db->sql_result($result, 0, "stats_ast");

    $stats_stl = $db->sql_result($result, 0, "stats_stl");

    $stats_blk = $db->sql_result($result, 0, "stats_blk");

    $stats_reb = $stats_orb + $stats_drb;
    $stats_pts = 2 * $stats_fgm + $stats_ftm + $stats_tgm;

    @$stats_ppg = ($stats_pts / $stats_gm);
    @$stats_reb = ($stats_reb / $stats_gm);
    @$stats_ast = ($stats_ast / $stats_gm);
    @$stats_stl = ($stats_stl / $stats_gm);
    @$stats_blk = ($stats_blk / $stats_gm);

    $stats_ppg = round($stats_ppg, 2);
    $stats_reb = round($stats_reb, 2);
    $stats_ast = round($stats_ast, 2);
    $stats_stl = round($stats_stl, 2);
    $stats_blk = round($stats_blk, 2);
    $stats_ppg = sprintf('%04.2f', $stats_ppg);
    $stats_reb = sprintf('%04.2f', $stats_reb);
    $stats_ast = sprintf('%04.2f', $stats_ast);
    $stats_stl = sprintf('%04.2f', $stats_stl);
    $stats_blk = sprintf('%04.2f', $stats_blk);

    if ($pospoc == $pos) {
        $all_chunk_player = "<td><table border=3 bordercolor=#FFD700><tbody><tr><td colspan=2><img src=\"../images/player/$pid.jpg\"> <img width=65 height=90 src=\"../images/logo/new$tid.gif\"></td>
	<tr><td colspan=2><b><a href=modules.php?name=Player&pa=showpage&pid=$pid><font color=#006666>$name</font></a></td></tr>
	<tr><td bgcolor=#006666 colspan=2><b><center><font color=#ffffff>$pos</font></center></b></td></tr>
	<td><font color=#006666>Points:</font></td><td align=right><font color=#006666>$stats_ppg</font></td></tr>
	<td><font color=#006666>Rebounds:</font></td><td align=right><font color=#006666>$stats_reb</font></td></tr>
	<td><font color=#006666>Assists:</font></td><td align=right><font color=#006666>$stats_ast</font></td></tr>
	<td><font color=#006666>Steals:</font></td><td align=right><font color=#006666>$stats_stl</font></td></tr>
	<td><font color=#006666>Blocks:</font></td><td align=right><font color=#006666>$stats_blk</font></td></tr></table></td>";
    } else {
        $all_chunk_player = "<td><table border=1 bordercolor=#006666><tbody><tr><td colspan=2><img src=\"../images/player/$pid.jpg\"> <img width=65 height=90 src=\"../images/logo/new$tid.gif\"></td>
	<tr><td colspan=2><b><a href=modules.php?name=Player&pa=showpage&pid=$pid><font color=#006666>$name</font></a></td></tr>
	<tr><td bgcolor=#006666 colspan=2><b><center><font color=#ffffff>$pos</font></center></b></td></tr>
	<td><font color=#006666>Points:</font></td><td align=right><font color=#006666>$stats_ppg</font></td></tr>
	<td><font color=#006666>Rebounds:</font></td><td align=right><font color=#006666>$stats_reb</font></td></tr>
	<td><font color=#006666>Assists:</font></td><td align=right><font color=#006666>$stats_ast</font></td></tr>
	<td><font color=#006666>Steals:</font></td><td align=right><font color=#006666>$stats_stl</font></td></tr>
	<td><font color=#006666>Blocks:</font></td><td align=right><font color=#006666>$stats_blk</font></td></tr></table></td>";
    }

    return $all_chunk_player;
}
