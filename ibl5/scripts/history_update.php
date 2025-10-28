<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$queryi = "UPDATE ibl_team_history SET div_titles = (SELECT COUNT(*) FROM ibl_team_awards WHERE ibl_team_awards.Award like '%Div.%' and ibl_team_history.team_name = ibl_team_awards.name)";
$resulti = $db->sql_query($queryi);

$queryj = "UPDATE ibl_team_history SET conf_titles = (SELECT COUNT(*) FROM ibl_team_awards WHERE ibl_team_awards.Award like '%Conf.%' and ibl_team_history.team_name = ibl_team_awards.name)";
$resultj = $db->sql_query($queryj);

$queryk = "UPDATE ibl_team_history SET ibl_titles = (SELECT COUNT(*) FROM ibl_team_awards WHERE ibl_team_awards.Award like '%World%' and ibl_team_history.team_name = ibl_team_awards.name)";
$resultk = $db->sql_query($queryk);

$queryl = "UPDATE ibl_team_history SET heat_titles = (SELECT COUNT(*) FROM ibl_team_awards WHERE ibl_team_awards.Award like '%H.E.A.T.%' and ibl_team_history.team_name = ibl_team_awards.name)";
$resultl = $db->sql_query($queryl);

$querym = "UPDATE ibl_team_history SET playoffs = (SELECT COUNT(*) FROM ibl_playoff_results WHERE ibl_playoff_results.winner = ibl_team_history.team_name and ibl_playoff_results.round = '1' or ibl_playoff_results.loser = ibl_team_history.team_name and ibl_playoff_results.round = '1' )";
$resultm = $db->sql_query($querym);

echo "Franchise History update is complete!<br>";
