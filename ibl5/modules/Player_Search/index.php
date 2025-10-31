<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2007 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die("You can't access this file directly...");
}

use Services\DatabaseService;

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

$pagetitle = "- $module_name";

global $db;

Nuke\Header::header();
OpenTable();
UI::playerMenu();

// ============== GET POST DATA

$pos = $_POST['pos'];
$age = $_POST['age'];
$form_submitted_check = $_POST['submitted'];
$search_name = $_POST['search_name'];
$college = $_POST['college'];
$exp = $_POST['exp'];
$bird = $_POST['bird'];
$exp_max = $_POST['exp_max'];
$bird_max = $_POST['bird_max'];

$r_fga = $_POST['r_fga'];
$r_fgp = $_POST['r_fgp'];
$r_fta = $_POST['r_fta'];
$r_ftp = $_POST['r_ftp'];
$r_tga = $_POST['r_tga'];
$r_tgp = $_POST['r_tgp'];
$r_orb = $_POST['r_orb'];
$r_drb = $_POST['r_drb'];
$r_ast = $_POST['r_ast'];
$r_stl = $_POST['r_stl'];
$r_blk = $_POST['r_blk'];
$r_to = $_POST['r_to'];
$r_foul = $_POST['r_foul'];

$Stamina = $_POST['sta'];
$Clutch = $_POST['Clutch'];
$Consistency = $_POST['Consistency'];
$talent = $_POST['talent'];
$skill = $_POST['skill'];
$intangibles = $_POST['intangibles'];

$active = $_POST['active'];

$oo = $_POST['oo'];
$do = $_POST['do'];
$po = $_POST['po'];
$to = $_POST['to'];
$od = $_POST['od'];
$dd = $_POST['dd'];
$pd = $_POST['pd'];
$td = $_POST['td'];

// ========= SEARCH PARAMETERS

echo "Please enter your search parameters (Age is less than or equal to the age entered; all other fields are greater than or equal to the amount entered).<br>
    Partial matches on a name or college are okay and are <b>not</b> case sensitive (e.g., entering \"Dard\" will match with \"Darden\" and \"Bedard\").<br>
    <br>
    Warning: Searches that may return a lot of players may take a long time to load!

    <form name=\"Search\" method=\"post\" action=\"modules.php?name=Player_Search\">
    <table border=1>
        <tr>
            <td>Position: <select name=\"pos\"><option value=\"\">-</option>";

$pos_array = array('PG', 'SG', 'SF', 'PF', 'C');

foreach ($pos_array as $key => $value) {
    echo "<option value=\"$value\"";
    if ($pos == $value) {
        echo ' SELECTED';
    }
    echo ">$value</option>";
}

echo "</select></td>
    <td>Age: <input type=\"text\" name=\"age\" size=\"2\" value=\"$age\"></td>
    <td>Stamina: <input type=\"text\" name=\"sta\" size=\"2\" value=\"$Stamina\"></td>
    <td>Talent: <input type=\"text\" name=\"talent\" size=\"1\" value=\"$talent\"></td>
    <td>Skill: <input type=\"text\" name=\"skill\" size=\"1\" value=\"$skill\"></td>
    <td>Intangibles: <input type=\"text\" name=\"intangibles\" size=\"1\" value=\"$intangibles\"></td>
    <td>clutch: <input type=\"text\" name=\"Clutch\" size=\"1\" value=\"$Clutch\"></td>
    <td>Consistency: <input type=\"text\" name=\"Consistency\" size=\"1\" value=\"$Consistency\"></td>
    <td>College: <input type=\"text\" name=\"college\" size=\"16\" value=\"$college\"></td>
</tr>
<tr>
    <td colspan=8>Include Retired Players in search? <select name=\"active\">";

if ($active == '1') {
    echo "<option value=\"1\" SELECTED>Yes</option>";
} else {
    echo "<option value=\"1\">Yes</option>";
}
if ($active == '0') {
    echo "<option value=\"0\" SELECTED>No</option>";
} else {
    echo "<option value=\"0\">No</option>";
}

echo "</td></tr>
        <tr>
            <td colspan=2>Minimum Years In League: <input type=\"text\" name=\"exp\" size=\"2\" value=\"$exp\"></td>
            <td colspan=2>Maximum Years In League: <input type=\"text\" name=\"exp_max\" size=\"2\" value=\"$exp\"></td>
            <td colspan=2>Minimum Bird Years: <input type=\"text\" name=\"bird\" size=\"2\" value=\"$bird\"></td>
            <td colspan=2>Maximum Bird Years: <input type=\"text\" name=\"bird_max\" size=\"2\" value=\"$bird\"></td>
        </tr>
    </table>
    <table border=1>
        <tr>
            <td>2ga: <input type=\"text\" name=\"r_fga\" size=\"2\" value=\"$r_fga\"></td>
            <td>2gp: <input type=\"text\" name=\"r_fgp\" size=\"2\" value=\"$r_fgp\"></td>
            <td>fta: <input type=\"text\" name=\"r_fta\" size=\"2\" value=\"$r_fta\"></td>
            <td>ftp: <input type=\"text\" name=\"r_ftp\" size=\"2\" value=\"$r_ftp\"></td>
            <td>3ga: <input type=\"text\" name=\"r_tga\" size=\"2\" value=\"$r_tga\"></td>
            <td>3gp: <input type=\"text\" name=\"r_tgp\" size=\"2\" value=\"$r_tgp\"></td>
            <td>orb: <input type=\"text\" name=\"r_orb\" size=\"2\" value=\"$r_orb\"></td>
            <td>drb: <input type=\"text\" name=\"r_drb\" size=\"2\" value=\"$r_drb\"></td>
            <td>ast: <input type=\"text\" name=\"r_ast\" size=\"2\" value=\"$r_ast\"></td>
            <td>stl: <input type=\"text\" name=\"r_stl\" size=\"2\" value=\"$r_stl\"></td>
            <td>blk: <input type=\"text\" name=\"r_blk\" size=\"2\" value=\"$r_blk\"></td>
            <td>tvr: <input type=\"text\" name=\"r_to\" size=\"2\" value=\"$r_to\"></td>
            <td>foul: <input type=\"text\" name=\"r_foul\" size=\"2\" value=\"$r_foul\"></td>
        </tr>
    </table>
    <table border=1>
        <tr>
            <td>NAME: <input type=\"text\" name=\"search_name\" size=\"32\" value=\"$search_name\"></td>
            <td>oo: <input type=\"text\" name=\"oo\" size=\"1\" value=\"$oo\"></td>
            <td>do: <input type=\"text\" name=\"do\" size=\"1\" value=\"$do\"></td>
            <td>po: <input type=\"text\" name=\"po\" size=\"1\" value=\"$po\"></td>
            <td>to: <input type=\"text\" name=\"to\" size=\"1\" value=\"$to\"></td>
            <td>od: <input type=\"text\" name=\"od\" size=\"1\" value=\"$od\"></td>
            <td>dd: <input type=\"text\" name=\"dd\" size=\"1\" value=\"$dd\"></td>
            <td>pd: <input type=\"text\" name=\"pd\" size=\"1\" value=\"$pd\"></td>
            <td>td: <input type=\"text\" name=\"td\" size=\"1\" value=\"$td\"></td>
        </tr>
    </table>

    <input type=\"hidden\" name=\"submitted\" value=\"1\">
    <input type=\"submit\" value=\"Search for Player!\">
</form>";

// ========= SET QUERY BASED ON SEARCH PARAMETERS

$query = "SELECT * FROM ibl_plr WHERE pid > '0'";

if ($active == 0) {
    $query .= " AND retired = '0'";
}
if ($search_name != null) {
    $query .= " AND name LIKE '%$search_name%'";
}
if ($college != null) {
    $query .= " AND college LIKE '%$college%'";
}
if ($pos != null) {
    $query .= " AND pos = '$pos'";
}
if ($age != null) {
    $query .= " AND age <= '$age'";
}

if ($Clutch != null) {
    $query .= " AND Clutch >= '$Clutch'";
}
if ($Stamina != null) {
    $query .= " AND sta >= '$Stamina'";
}
if ($Consistency != null) {
    $query .= " AND Consistency >= '$Consistency'";
}

if ($oo != null) {
    $query .= " AND oo >= '$oo'";
}
if ($do != null) {
    $query .= " AND do >= '$do'";
}
if ($po != null) {
    $query .= " AND po >= '$po'";
}
if ($to != null) {
    $query .= " AND `to` >= '$to'";
}

if ($od != null) {
    $query .= " AND od >= '$od'";
}
if ($dd != null) {
    $query .= " AND dd >= '$dd'";
}
if ($pd != null) {
    $query .= " AND pd >= '$pd'";
}
if ($td != null) {
    $query .= " AND td >= '$td'";
}

if ($exp != null) {
    $query .= " AND exp >= '$exp'";
}
if ($bird != null) {
    $query .= " AND bird >= '$bird'";
}

if ($exp_max != null) {
    $query .= " AND exp <= '$exp_max'";
}
if ($bird != null) {
    $query .= " AND bird <= '$bird_max'";
}

if ($talent != null) {
    $query .= " AND talent >= '$talent'";
}
if ($skill != null) {
    $query .= " AND skill >= '$skill'";
}
if ($intangibles != null) {
    $query .= " AND intangibles >= '$intangibles'";
}

if ($coach != null) {
    $query .= " AND coach >= '$coach'";
}
if ($loyalty != null) {
    $query .= " AND loyalty >= '$loyalty'";
}
if ($playingTime != null) {
    $query .= " AND playingTime >= '$playingTime'";
}
if ($winner != null) {
    $query .= " AND winner >= '$winner'";
}
if ($tradition != null) {
    $query .= " AND tradition >= '$tradition'";
}
if ($security != null) {
    $query .= " AND security >= '$security'";
}

if ($exp != null) {
    $query .= " AND exp >= '$exp'";
}
if ($bird != null) {
    $query .= " AND bird >= '$bird'";
}

if ($r_fga != null) {
    $query .= " AND r_fga >= '$r_fga'";
}
if ($r_fgp != null) {
    $query .= " AND r_fgp >= '$r_fgp'";
}
if ($r_fta != null) {
    $query .= " AND r_fta >= '$r_fta'";
}
if ($r_ftp != null) {
    $query .= " AND r_ftp >= '$r_ftp'";
}
if ($r_tga != null) {
    $query .= " AND r_tga >= '$r_tga'";
}
if ($r_tgp != null) {
    $query .= " AND r_tgp >= '$r_tgp'";

}
if ($r_orb != null) {
    $query .= " AND r_orb >= '$r_orb'";
}
if ($r_drb != null) {
    $query .= " AND r_drb >= '$r_drb'";
}
if ($r_ast != null) {
    $query .= " AND r_ast >= '$r_ast'";
}
if ($r_stl != null) {
    $query .= " AND r_stl >= '$r_stl'";
}
if ($r_to != null) {
    $query .= " AND r_to >= '$r_to'";
}
if ($r_blk != null) {
    $query .= " AND r_blk >= '$r_blk'";
}
if ($r_foul != null) {
    $query .= " AND r_foul >= '$r_foul'";
}

$query .= " ORDER BY retired, ordinal ASC";

// =============== EXECUTE QUERY

if ($form_submitted_check == 1) {
    $result = $db->sql_query($query);
    @$num = $db->sql_numrows($result);
}

echo "<table class=\"sortable\" border=1 cellpadding=0 cellspacing=0>
    <tr>
        <th>Pos</th>
        <th>Player</th>
        <th>Age</th>
        <th>Stamina</th>
        <th>Team</th>
        <th>Exp</th>
        <th>Bird</th>
        <th>2ga</th>
        <th>2gp</th>
        <th>fta</th>
        <th>ftp</th>
        <th>3ga</th>
        <th>3gp</th>
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
        <th>Talent</th>
        <th>Skill</th>
        <th>Intangibles</th>
        <th>Clutch</th>
        <th>consistency</th>
        <th>College</th>
    </tr>";

// ========== FILL PLAYING RATINGS

if ($form_submitted_check == 1) {
    $i = 0;

    while ($i < $num) {
        $retired = $db->sql_result($result, $i, "retired");
        $name = DatabaseService::safeHtmlOutput($db->sql_result($result, $i, "name")); // Safely escape for HTML
        $pos = $db->sql_result($result, $i, "pos");
        $pid = $db->sql_result($result, $i, "pid");
        $tid = $db->sql_result($result, $i, "tid");
        $age = $db->sql_result($result, $i, "age");
        $teamname = DatabaseService::safeHtmlOutput($db->sql_result($result, $i, "teamname")); // Safely escape for HTML
        $college = DatabaseService::safeHtmlOutput($db->sql_result($result, $i, "college")); // Safely escape for HTML
        $exp = $db->sql_result($result, $i, "exp");
        $bird = $db->sql_result($result, $i, "bird");

        $r_sta = $db->sql_result($result, $i, "sta");
        $r_fga = $db->sql_result($result, $i, "r_fga");
        $r_fgp = $db->sql_result($result, $i, "r_fgp");
        $r_fta = $db->sql_result($result, $i, "r_fta");
        $r_ftp = $db->sql_result($result, $i, "r_ftp");
        $r_tga = $db->sql_result($result, $i, "r_tga");
        $r_tgp = $db->sql_result($result, $i, "r_tgp");
        $r_orb = $db->sql_result($result, $i, "r_orb");
        $r_drb = $db->sql_result($result, $i, "r_drb");
        $r_ast = $db->sql_result($result, $i, "r_ast");
        $r_stl = $db->sql_result($result, $i, "r_stl");
        $r_tvr = $db->sql_result($result, $i, "r_to");
        $r_blk = $db->sql_result($result, $i, "r_blk");
        $r_foul = $db->sql_result($result, $i, "r_foul");
        $oo = $db->sql_result($result, $i, "oo");
        $do = $db->sql_result($result, $i, "do");
        $po = $db->sql_result($result, $i, "po");
        $to = $db->sql_result($result, $i, "to");
        $od = $db->sql_result($result, $i, "od");
        $dd = $db->sql_result($result, $i, "dd");
        $pd = $db->sql_result($result, $i, "pd");
        $td = $db->sql_result($result, $i, "td");

        $Clutch = $db->sql_result($result, $i, "Clutch");
        $Consistency = $db->sql_result($result, $i, "Consistency");
        $talent = $db->sql_result($result, $i, "talent");
        $skill = $db->sql_result($result, $i, "skill");
        $intangibles = $db->sql_result($result, $i, "intangibles");

        if ($i % 2) {
            echo "<tr bgcolor=#ffffff>";
        } else {
            echo "<tr bgcolor=#e6e7e2>";
        }

        $i++;

        if ($retired == 1) {
            echo "<td><center>$pos</center></td>
                <td><center><a href=\"modules.php?name=Player&pa=showpage&pid=$pid\">$name</a></center></td>
                <td colspan=30><center> --- Retired --- </center></td>
                <td>$college</td>
            </tr>";
        } else {
            echo "<td><center>$pos</center></td>
                <td><center><a href=\"modules.php?name=Player&pa=showpage&pid=$pid\">$name</a></center></td>
                <td><center>$age</center></td>
                <td><center>$r_sta</center></td>
                <td><center><a href=\"team.php?tid=$tid\">$teamname</a></center></td>
                <td><center>$exp</center></td>
                <td><center>$bird</center></td>
                <td><center>$r_fga</center></td>
                <td><center>$r_fgp</center></td>
                <td><center>$r_fta</center></td>
                <td><center>$r_ftp</center></td>
                <td><center>$r_tga</center></td>
                <td><center>$r_tgp</center></td>
                <td><center>$r_orb</center></td>
                <td><center>$r_drb</center></td>
                <td><center>$r_ast</center></td>
                <td><center>$r_stl</center></td>
                <td><center>$r_tvr</center></td>
                <td><center>$r_blk</center></td>
                <td><center>$r_foul</center></td>
                <td><center>$oo</center></td>
                <td><center>$do</center></td>
                <td><center>$po</center></td>
                <td><center>$to</center></td>
                <td><center>$od</center></td>
                <td><center>$dd</center></td>
                <td><center>$pd</center></td>
                <td><center>$td</center></td>
                <td><center>$talent</center></td>
                <td><center>$skill</center></td>
                <td><center>$intangibles</center></td>
                <td><center>$Clutch</center></td>
                <td><center>$Consistency</center></td>
                <td>$college</td>
            </tr>";
        }
    } // Matches up with form submitted check variable
}

echo "</table></center>";

CloseTable();
Nuke\Footer::footer();