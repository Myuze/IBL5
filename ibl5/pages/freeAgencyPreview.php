<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';
$season = new Season($db);

$tid = $_REQUEST['tid'];
$yr = $_REQUEST['yr'];

if ($tid == null) {
    if ($season->endingYear != null) {
        // === CODE FOR FREE AGENTS

        echo "<html><head><title>Upcoming Free Agents List ($season->endingYear)</title></head><body>
            <style>th{ font-size: 9pt; font-family:Arial; color: white; background-color: navy}td      { text-align: Left; font-size: 9pt; font-family:Arial; color:black; }.tdp { font-weight: bold; text-align: Left; font-size: 9pt; color:black; } </style>
            <center><h2>Players Currently to be Free Agents at the end of the $season->endingYear Season</h2>
            <table border=1 cellspacing=1><tr><th colspan=33><center>Player Ratings</center></th></tr>
            <tr><th>Pos</th>
                <th>Player</th>
                <th>Team</th>
                <th>Age</th>
                <th>2ga</th>
                <th>2g%</th>
                <th>fta</th>
                <th>ft%</th>
                <th>3ga</th>
                <th>3g%</th>
                <th>orb</th>
                <th>drb</th>
                <th>ast</th>
                <th>stl</th>
                <th>to</th>
                <th>blk</th>
                <th>foul</th>
                <th>o-o</th>
                <th>d-o</th>
                <th>p-o</th>
                <th>t-o</th>
                <th>o-d</th>
                <th>d-d</th>
                <th>p-d</th>
                <th>t-d</th>
                <th>Loy</th>
                <th>PFW</th>
                <th>PT</th>
                <th>Sec</th>
                <th>Trad</th>
            </tr>";

        $query = "SELECT * FROM ibl_plr WHERE retired = 0 ORDER BY ordinal ASC";
        $result = $db->sql_query($query);
        $num = $db->sql_numrows($result);

        $i = 0;
        $j = 0;

        while ($i < $num) {
            $draftyear = $db->sql_result($result, $i, "draftyear");
            $exp = $db->sql_result($result, $i, "exp");
            $cy = $db->sql_result($result, $i, "cy");
            $cyt = $db->sql_result($result, $i, "cyt");

            $yearoffreeagency = $draftyear + $exp + $cyt - $cy;

            if ($yearoffreeagency == $season->endingYear) {
                $name = $db->sql_result($result, $i, "name");
                $team = $db->sql_result($result, $i, "teamname");
                $tid = $db->sql_result($result, $i, "tid");
                $pid = $db->sql_result($result, $i, "pid");
                $pos = $db->sql_result($result, $i, "pos");
                $age = $db->sql_result($result, $i, "age");

                $r_2ga = $db->sql_result($result, $i, "r_fga");
                $r_2gp = $db->sql_result($result, $i, "r_fgp");
                $r_fta = $db->sql_result($result, $i, "r_fta");
                $r_ftp = $db->sql_result($result, $i, "r_ftp");
                $r_3ga = $db->sql_result($result, $i, "r_tga");
                $r_3gp = $db->sql_result($result, $i, "r_tgp");
                $r_orb = $db->sql_result($result, $i, "r_orb");
                $r_drb = $db->sql_result($result, $i, "r_drb");
                $r_ast = $db->sql_result($result, $i, "r_ast");
                $r_stl = $db->sql_result($result, $i, "r_stl");
                $r_blk = $db->sql_result($result, $i, "r_blk");
                $r_tvr = $db->sql_result($result, $i, "r_to");
                $r_foul = $db->sql_result($result, $i, "r_foul");
                $r_totoff = $db->sql_result($result, $i, "oo") + $db->sql_result($result, $i, "do") + $db->sql_result($result, $i, "po") + $db->sql_result($result, $i, "to");
                $r_totdef = $db->sql_result($result, $i, "od") + $db->sql_result($result, $i, "dd") + $db->sql_result($result, $i, "pd") + $db->sql_result($result, $i, "td");
                $r_oo = $db->sql_result($result, $i, "oo");
                $r_do = $db->sql_result($result, $i, "do");
                $r_po = $db->sql_result($result, $i, "po");
                $r_to = $db->sql_result($result, $i, "to");
                $r_od = $db->sql_result($result, $i, "od");
                $r_dd = $db->sql_result($result, $i, "dd");
                $r_pd = $db->sql_result($result, $i, "pd");
                $r_td = $db->sql_result($result, $i, "td");
                $r_foul = $db->sql_result($result, $i, "r_foul");
                $loyalty = $db->sql_result($result, $i, "loyalty");
                $playForWinner = $db->sql_result($result, $i, "winner");
                $playingTime = $db->sql_result($result, $i, "playingTime");
                $security = $db->sql_result($result, $i, "security");
                $tradition = $db->sql_result($result, $i, "tradition");

                if ($j == 0) {
                    echo "      <tr bgcolor=#ffffff align=center>";
                    $j = 1;
                } else {
                    echo "      <tr bgcolor=#e6e7e2 align=center>";
                    $j = 0;
                }
                echo "<td>$pos</td>
                    <td><a href=\"modules.php?name=Player&pa=showpage&pid=$pid\">$name</a></td>
                    <td><a href=\"team.php?tid=$tid\">$team</a></td>
                    <td>$age</td>
                    <td>$r_2ga</td>
                    <td>$r_2gp</td>
                    <td>$r_fta</td>
                    <td>$r_ftp</td>
                    <td>$r_3ga</td>
                    <td>$r_3gp</td>
                    <td>$r_orb</td>
                    <td>$r_drb</td>
                    <td>$r_ast</td>
                    <td>$r_stl</td>
                    <td>$r_tvr</td>
                    <td>$r_blk</td>
                    <td>$r_foul</td>
                    <td>$r_oo</td>
                    <td>$r_do</td>
                    <td>$r_po</td>
                    <td>$r_to</td>
                    <td>$r_od</td>
                    <td>$r_dd</td>
                    <td>$r_pd</td>
                    <td>$r_td</td>
                    <td>$loyalty</td>
                    <td>$playForWinner</td>
                    <td>$playingTime</td>
                    <td>$security</td>
                    <td>$tradition</td>
                </tr>
                ";
            }

            $i++;

        }

        echo "
            </table>";
    }
}
