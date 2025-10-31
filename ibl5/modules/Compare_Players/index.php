<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2002 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!mb_eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

function userinfo($username, $bypass = 0, $hid = 0, $url = 0)
{
    global $user, $prefix, $user_prefix, $db;
    $sharedFunctions = new Shared($db);

    $sql = "SELECT * FROM " . $prefix . "_bbconfig";
    $result = $db->sql_query($sql);
    while ($row = $db->sql_fetchrow($result)) {
        $board_config[$row['config_name']] = $row['config_value'];
    }
    $sql2 = "SELECT * FROM " . $user_prefix . "_users WHERE username = '$username'";
    $result2 = $db->sql_query($sql2);
    $userinfo = $db->sql_fetchrow($result2);
    if (!$bypass) {
        cookiedecode($user);
    }

    $teamlogo = $userinfo['user_ibl_team'];
    $tid = $sharedFunctions->getTidFromTeamname($teamlogo);

    Nuke\Header::header();
    OpenTable();
    UI::displaytopmenu($db, $tid);

    comparePlayers();

    CloseTable();
    Nuke\Footer::footer();
}

function comparePlayers()
{
    if (!isset($_POST['Player1'])) {
        echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	  <link rel="stylesheet" href="/resources/demos/style.css">
	  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	  <script>
	  $( function() {
		var availableTags = [';

        foreach (getPlayerNamesArray() as $name) {
            // Strip SQL escaping and JSON-encode for JavaScript safety
            $safeName = json_encode(stripslashes($name), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            echo "$safeName,
			  ";
        }

        echo '""];
		$( "#Player1" ).autocomplete({
		  source: availableTags
		});
		$( "#Player2" ).autocomplete({
		  source: availableTags
		});
	  } );
	  </script>';

        echo '<FORM action="modules.php?name=Compare_Players" method="POST">
			<div class="ui-widget">
				<label for="Player1">Player 1: </label>
				<input id="Player1" type="text" name="Player1"><br>
				<label for="Player2">Player 2: </label>
				<input id="Player2" type="text" name="Player2"><br>
	  		</div>
			<INPUT type="submit" value="Compare">
		</FORM>';
    } else {
        $player1Array = getPlayerInfoArrayFromName($_POST['Player1']);
        $player2Array = getPlayerInfoArrayFromName($_POST['Player2']);

        echo "<table border=1 cellspacing=0 align=center class=\"sortable\">
			<caption>
				<center><b>Current Ratings</b></center>
			</caption>
			<colgroup>
				<col span=3>
				<col span=6 style=\"background-color: #ddd\">
				<col span=7>
				<col span=4 style=\"background-color: #ddd\">
				<col span=4>
			</colgroup>
			<thead>
				<tr>
					<th>Pos</th>
					<th>Player</th>
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
					<th>tvr</th>
					<th>blk</th>
					<th>foul</th>
					<th>oo</th>
					<th>do</th>
					<th>po</th>
					<th>to</th>
					<th>od</th>
					<th>dd</th>
					<th>pd</th>
					<th>td</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>$player1Array[pos]</th>
					<th>$player1Array[name]</th>
					<th>$player1Array[age]</th>
					<th>$player1Array[r_fga]</th>
					<th>$player1Array[r_fgp]</th>
					<th>$player1Array[r_fta]</th>
					<th>$player1Array[r_ftp]</th>
					<th>$player1Array[r_tga]</th>
					<th>$player1Array[r_tgp]</th>
					<th>$player1Array[r_orb]</th>
					<th>$player1Array[r_drb]</th>
					<th>$player1Array[r_ast]</th>
					<th>$player1Array[r_stl]</th>
					<th>$player1Array[r_to]</th>
					<th>$player1Array[r_blk]</th>
					<th>$player1Array[r_foul]</th>
					<th>$player1Array[oo]</th>
					<th>$player1Array[do]</th>
					<th>$player1Array[po]</th>
					<th>$player1Array[to]</th>
					<th>$player1Array[od]</th>
					<th>$player1Array[dd]</th>
					<th>$player1Array[pd]</th>
					<th>$player1Array[td]</th>
				</tr>
				<tr>
					<th>$player2Array[pos]</th>
					<th>$player2Array[name]</th>
					<th>$player2Array[age]</th>
					<th>$player2Array[r_fga]</th>
					<th>$player2Array[r_fgp]</th>
					<th>$player2Array[r_fta]</th>
					<th>$player2Array[r_ftp]</th>
					<th>$player2Array[r_tga]</th>
					<th>$player2Array[r_tgp]</th>
					<th>$player2Array[r_orb]</th>
					<th>$player2Array[r_drb]</th>
					<th>$player2Array[r_ast]</th>
					<th>$player2Array[r_stl]</th>
					<th>$player2Array[r_to]</th>
					<th>$player2Array[r_blk]</th>
					<th>$player2Array[r_foul]</th>
					<th>$player2Array[oo]</th>
					<th>$player2Array[do]</th>
					<th>$player2Array[po]</th>
					<th>$player2Array[to]</th>
					<th>$player2Array[od]</th>
					<th>$player2Array[dd]</th>
					<th>$player2Array[pd]</th>
					<th>$player2Array[td]</th>
				</tr>
			</tbody>
		</table>

		<p>

		<table border=1 cellspacing=0 align=center class=\"sortable\">
			<caption>
				<center><b>Current Season Stats</b></center>
			</caption>
			<colgroup>
				<col span=5>
				<col span=6 style=\"background-color: #ddd\">
				<col span=8>
			</colgroup>
			<thead>
				<tr>
					<th>Pos</th>
					<th>Player</th>
					<th>g</th>
					<th>gs</th>
					<th>min</th>
					<th>fgm</th>
					<th>fga</th>
					<th>ftm</th>
					<th>fta</th>
					<th>3gm</th>
					<th>3ga</th>
					<th>orb</th>
					<th>reb</th>
					<th>ast</th>
					<th>stl</th>
					<th>to</th>
					<th>blk</th>
					<th>pf</th>
					<th>pts</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>$player1Array[pos]</th>
					<th>$player1Array[name]</th>
					<th>$player1Array[stats_gm]</th>
					<th>$player1Array[stats_gs]</th>
					<th>$player1Array[stats_min]</th>
					<th>$player1Array[stats_fgm]</th>
					<th>$player1Array[stats_fga]</th>
					<th>$player1Array[stats_ftm]</th>
					<th>$player1Array[stats_fta]</th>
					<th>$player1Array[stats_3gm]</th>
					<th>$player1Array[stats_3ga]</th>
					<th>$player1Array[stats_orb]</th>
					<th>$player1Array[stats_drb]</th>
					<th>$player1Array[stats_ast]</th>
					<th>$player1Array[stats_stl]</th>
					<th>$player1Array[stats_to]</th>
					<th>$player1Array[stats_blk]</th>
					<th>$player1Array[stats_pf]</th>
					<th>" . (2 * $player1Array['stats_fgm'] + $player1Array['stats_ftm'] + $player1Array['stats_tgm']) . "</th>
				</tr>
				<tr>
					<th>$player2Array[pos]</th>
					<th>$player2Array[name]</th>
					<th>$player2Array[stats_gm]</th>
					<th>$player2Array[stats_gs]</th>
					<th>$player2Array[stats_min]</th>
					<th>$player2Array[stats_fgm]</th>
					<th>$player2Array[stats_fga]</th>
					<th>$player2Array[stats_ftm]</th>
					<th>$player2Array[stats_fta]</th>
					<th>$player2Array[stats_3gm]</th>
					<th>$player2Array[stats_3ga]</th>
					<th>$player2Array[stats_orb]</th>
					<th>$player2Array[stats_drb]</th>
					<th>$player2Array[stats_ast]</th>
					<th>$player2Array[stats_stl]</th>
					<th>$player2Array[stats_to]</th>
					<th>$player2Array[stats_blk]</th>
					<th>$player2Array[stats_pf]</th>
					<th>" . (2 * $player2Array['stats_fgm'] + $player2Array['stats_ftm'] + $player2Array['stats_tgm']) . "</th>
				</tr>
			</tbody>
		</table>

		<p>

		<table border=1 cellspacing=0 align=center class=\"sortable\">
			<caption>
				<center><b>Career Stats</b></center>
			</caption>
			<colgroup>
				<col span=4>
				<col span=6 style=\"background-color: #ddd\">
				<col span=8>
			</colgroup>
			<thead>
				<tr>
					<th>Pos</th>
					<th>Player</th>
					<th>g</th>
					<th>min</th>
					<th>fgm</th>
					<th>fga</th>
					<th>ftm</th>
					<th>fta</th>
					<th>3gm</th>
					<th>3ga</th>
					<th>orb</th>
					<th>drb</th>
					<th>reb</th>
					<th>ast</th>
					<th>stl</th>
					<th>to</th>
					<th>blk</th>
					<th>pf</th>
					<th>pts</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>$player1Array[pos]</th>
					<th>$player1Array[name]</th>
					<th>$player1Array[car_gm]</th>
					<th>$player1Array[car_min]</th>
					<th>$player1Array[car_fgm]</th>
					<th>$player1Array[car_fga]</th>
					<th>$player1Array[car_ftm]</th>
					<th>$player1Array[car_fta]</th>
					<th>$player1Array[car_tgm]</th>
					<th>$player1Array[car_tga]</th>
					<th>$player1Array[car_orb]</th>
					<th>$player1Array[car_drb]</th>
					<th>$player1Array[car_reb]</th>
					<th>$player1Array[car_ast]</th>
					<th>$player1Array[car_stl]</th>
					<th>$player1Array[car_to]</th>
					<th>$player1Array[car_blk]</th>
					<th>$player1Array[car_pf]</th>
					<th>$player1Array[car_pts]</th>
				</tr>
				<tr>
					<th>$player2Array[pos]</th>
					<th>$player2Array[name]</th>
					<th>$player2Array[car_gm]</th>
					<th>$player2Array[car_min]</th>
					<th>$player2Array[car_fgm]</th>
					<th>$player2Array[car_fga]</th>
					<th>$player2Array[car_ftm]</th>
					<th>$player2Array[car_fta]</th>
					<th>$player2Array[car_tgm]</th>
					<th>$player2Array[car_tga]</th>
					<th>$player2Array[car_orb]</th>
					<th>$player2Array[car_drb]</th>
					<th>$player2Array[car_reb]</th>
					<th>$player2Array[car_ast]</th>
					<th>$player2Array[car_stl]</th>
					<th>$player2Array[car_to]</th>
					<th>$player2Array[car_blk]</th>
					<th>$player2Array[car_pf]</th>
					<th>$player2Array[car_pts]</th>
				</tr>
			</tbody>
		</table>";
    }

}

function getPlayerNamesArray()
{
    global $db;

    $query = "SELECT name
		FROM ibl_plr
		WHERE ordinal != 0
		ORDER BY name ASC;";
    $result = $db->sql_query($query);
    $numRows = $db->sql_numrows($result);
    $i = 0;
    while ($i < $numRows) {
        $array[$i] = $db->sql_result($result, $i, "name");
        $i++;
    }
    return $array;
}

function getPlayerInfoArrayFromName($playerName)
{
    global $db;

    $query = "SELECT *
		FROM ibl_plr
		WHERE name = '$playerName'
		LIMIT 1;";
    $result = $db->sql_query($query);
    $array = $db->sql_fetch_assoc($result);
    return $array;
}

function main($user)
{
    global $stop;
    if (!is_user($user)) {
        Nuke\Header::header();
        OpenTable();
        echo "<center><font class=\"title\"><b>" . ($stop ? _LOGININCOR : _USERREGLOGIN) . "</b></font></center>";
        CloseTable();
        echo "<br>";
        if (!is_user($user)) {
            OpenTable();
            loginbox();
            CloseTable();
        }
        Nuke\Footer::footer();
    } elseif (is_user($user)) {
        global $cookie;
        cookiedecode($user);
        userinfo($cookie[1]);
    }
}

switch ($op) {
    default:
        main($user);
        break;
}
