<?php

class Shared
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getNumberOfTitles($teamname, $titleName)
    {
        $queryNumberOfTitles = $this->db->sql_query("SELECT COUNT(name)
        	FROM ibl_team_awards
        	WHERE name = '$teamname'
        	  AND Award LIKE '%$titleName%';");

        return $this->db->sql_result($queryNumberOfTitles, 0, 'COUNT(name)');
    }

    public function getCurrentOwnerOfDraftPick($draftYear, $draftRound, $teamNameOfDraftPickOrigin)
    {
        $queryCurrentOwnerOfDraftPick = $this->db->sql_query("SELECT ownerofpick
            FROM ibl_draft_picks
            WHERE year = '$draftYear'
              AND round = '$draftRound'
              AND teampick = '$teamNameOfDraftPickOrigin'
            LIMIT 1;");

        return $this->db->sql_result($queryCurrentOwnerOfDraftPick, 0, 'ownerofpick');
    }
    
    public function getPlayerIDFromPlayerName($playerName)
    {
        $queryPlayerIDFromPlayerName = $this->db->sql_query("SELECT pid
            FROM ibl_plr
            WHERE name = '$playerName'
            LIMIT 1;");

        return $this->db->sql_result($queryPlayerIDFromPlayerName, 0, 'pid');
    }

    public function getTeamnameFromTeamID($teamID)
    {
        $teamID = (int) $teamID; // Ensure teamID is an integer
        $queryTeamnameFromTeamID = $this->db->sql_query("SELECT team_name
            FROM ibl_team_info
            WHERE teamid = $teamID
            LIMIT 1;");

        return $this->db->sql_result($queryTeamnameFromTeamID, 0, 'team_name');
    }

    public function getTeamnameFromUsername($username)
    {
        if ($username) {
            $queryTeamnameFromUsername = $this->db->sql_query("SELECT user_ibl_team
                FROM nuke_users
                WHERE username = '$username'
                LIMIT 1;");

            return $this->db->sql_result($queryTeamnameFromUsername, 0, 'user_ibl_team');
        } else {
            return "Free Agents";
        }
    }

    public function getTidFromTeamname($teamname)
    {
        $queryTidFromTeamname = $this->db->sql_query("SELECT teamid
            FROM ibl_team_info
            WHERE team_name = '$teamname'
            LIMIT 1;");

        return (int) $this->db->sql_result($queryTidFromTeamname, 0, 'teamid'); // Ensure teamID is an integer
    }

    public function isFreeAgencyModuleActive()
    {
        $queryIsFreeAgencyModuleActive = $this->db->sql_query("SELECT title, active
            FROM nuke_modules
            WHERE title = 'Free_Agency'
            LIMIT 1");

        return $this->db->sql_result($queryIsFreeAgencyModuleActive, 0, "active");
    }

    public function resetSimContractExtensionAttempts()
    {
        echo '<p>Resetting sim contract extension attempts...<p>';

        $sqlQueryString = "UPDATE ibl_team_info SET Used_Extension_This_Chunk = 0;";
        if ($this->db->sql_query($sqlQueryString)) {
            \UI::displayDebugOutput($sqlQueryString, 'Reset Sim Contract Extension Attempts SQL Query');
            echo '<p>Sim contract extension attempts have been reset.<p>';
            return;
        } else {
            die('Invalid query: ' . $this->db->sql_error());
        }
    }
}
