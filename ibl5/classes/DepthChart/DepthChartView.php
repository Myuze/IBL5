<?php

namespace DepthChart;

use Services\DatabaseService;

/**
 * Renders depth chart views and forms
 */
class DepthChartView
{
    private $processor;

    public function __construct(DepthChartProcessor $processor)
    {
        $this->processor = $processor;
    }
    
    /**
     * Renders the team logo image for the given team ID.
     *
     * @param int $teamID The team ID used to locate the logo image.
     * @return void Echoes the HTML for the team logo image.
     */
    public function renderTeamLogo(int $teamID): void
    {
        echo "<center><img src=\"images/logo/$teamID.jpg\"></center><br>";
    }
    
    /**
     * Renders position depth select options
     * 
     * @param int $selectedValue Currently selected value
     * @return void Echoes HTML
     */
    public function renderPositionOptions(int $selectedValue): void
    {
        $options = [
            0 => 'No',
            1 => '1st',
            2 => '2nd',
            3 => '3rd',
            4 => '4th',
            5 => 'ok'
        ];
        
        foreach ($options as $value => $label) {
            $selected = ($selectedValue == $value) ? ' SELECTED' : '';
            echo "<option value=\"$value\"$selected>$label</option>";
        }
    }
    
    /**
     * Renders offensive/defensive focus options
     * 
     * @param int $selectedValue Currently selected value
     * @return void Echoes HTML
     */
    public function renderOffDefOptions(int $selectedValue): void
    {
        $options = [
            0 => 'Auto',
            1 => 'Outside',
            2 => 'Drive',
            3 => 'Post'
        ];
        
        foreach ($options as $value => $label) {
            $selected = ($selectedValue == $value) ? ' SELECTED' : '';
            echo "<option value=\"$value\"$selected>$label</option>";
        }
    }
    
    /**
     * Renders OI/DI/BH setting options
     * 
     * @param int $selectedValue Currently selected value
     * @return void Echoes HTML
     */
    public function renderSettingOptions(int $selectedValue): void
    {
        $options = [2, 1, 0, -1, -2];
        
        foreach ($options as $value) {
            $selected = ($selectedValue == $value) ? ' SELECTED' : '';
            $label = ($value == 0) ? '-' : $value;
            echo "<option value=\"$value\"$selected>$label</option>";
        }
    }
    
    /**
     * Renders active/inactive options
     * 
     * @param int $selectedValue Currently selected value (1 or 0)
     * @return void Echoes HTML
     */
    public function renderActiveOptions(int $selectedValue): void
    {
        if ($selectedValue == 1) {
            echo '<option value="1" SELECTED>Yes</option><option value="0">No</option>';
        } else {
            echo '<option value="1">Yes</option><option value="0" SELECTED>No</option>';
        }
    }
    
    /**
     * Renders minutes options based on stamina
     * 
     * @param int $selectedValue Currently selected value
     * @param int $staminaCap Stamina cap for player
     * @return void Echoes HTML
     */
    public function renderMinutesOptions(int $selectedValue, int $staminaCap): void
    {
        echo '<option value="0"' . ($selectedValue == 0 ? ' SELECTED' : '') . '>Auto</option>';
        
        for ($i = 1; $i <= $staminaCap; $i++) {
            $selected = ($selectedValue == $i) ? ' SELECTED' : '';
            echo "<option value=\"$i\"$selected>$i</option>";
        }
    }
    
    /**
     * Renders the depth chart form header
     * 
     * @param string $teamLogo Team name
     * @param int $teamID Team ID
     * @param array $slotNames Names of the 5 position slots
     * @return void Echoes HTML
     */
    public function renderFormHeader(string $teamLogo, int $teamID, array $slotNames): void
    {
        echo "<form name=\"Depth_Chart\" method=\"post\" action=\"modules.php?name=Depth_Chart_Entry&op=submit\">
            <input type=\"hidden\" name=\"Team_Name\" value=\"$teamLogo\">";
        
        echo "<p><center><table>
            <tr>
                <th colspan=14><center>DEPTH CHART ENTRY</center></th>
            </tr>
            <tr>
                <th>Pos</th>
                <th>Player</th>
                <th>{$slotNames[0]}</th>
                <th>{$slotNames[1]}</th>
                <th>{$slotNames[2]}</th>
                <th>{$slotNames[3]}</th>
                <th>{$slotNames[4]}</th>
                <th>active</th>
                <th>min</th>
                <th>OF</th>
                <th>DF</th>
                <th>OI</th>
                <th>DI</th>
                <th>BH</th>
            </tr>";
    }
    
    /**
     * Renders a player row in the depth chart form
     * 
     * @param array $player Player data from database
     * @param int $depthCount Row counter
     * @return void Echoes HTML
     */
    public function renderPlayerRow(array $player, int $depthCount): void
    {
        $player_pid = $player['pid'];
        $player_pos = $player['pos'];
        $player_name = $player['name'];
        $player_inj = $player['injured'];
        
        // Safely escape player name for HTML attribute and display
        $player_name_html = DatabaseService::safeHtmlOutput($player_name);
        
        $player_staminacap = $player['sta'] + 40;
        if ($player_staminacap > 40) {
            $player_staminacap = 40;
        }
        
        echo "<tr>
            <td>$player_pos</td>
            <td nowrap>
                <input type=\"hidden\" name=\"Injury$depthCount\" value=\"$player_inj\">
                <input type=\"hidden\" name=\"Name$depthCount\" value=\"$player_name_html\">
                <a href=\"./modules.php?name=Player&pa=showpage&pid=$player_pid\">$player_name_html</a>
            </td>";
        
        // Render each position slot - all players can play at all positions
        $positions = ['pg', 'sg', 'sf', 'pf', 'c'];
        foreach ($positions as $posKey) {
            $this->renderPositionCell($player, $posKey, $depthCount);
        }
        
        // Render active dropdown
        echo "<td><select name=\"active$depthCount\">";
        $this->renderActiveOptions($player['dc_active']);
        echo "</select></td>";
        
        // Render minutes dropdown
        echo "<td><select name=\"min$depthCount\">";
        $this->renderMinutesOptions($player['dc_minutes'], $player_staminacap);
        echo "</select></td>";
        
        // Render OF dropdown
        echo "<td><select name=\"OF$depthCount\">";
        $this->renderOffDefOptions($player['dc_of']);
        echo "</select></td>";
        
        // Render DF dropdown
        echo "<td><select name=\"DF$depthCount\">";
        $this->renderOffDefOptions($player['dc_df']);
        echo "</select></td>";
        
        // Render OI dropdown
        echo "<td><select name=\"OI$depthCount\">";
        $this->renderSettingOptions($player['dc_oi']);
        echo "</select></td>";
        
        // Render DI dropdown
        echo "<td><select name=\"DI$depthCount\">";
        $this->renderSettingOptions($player['dc_di']);
        echo "</select></td>";
        
        // Render BH dropdown
        echo "<td><select name=\"BH$depthCount\">";
        $this->renderSettingOptions($player['dc_bh']);
        echo "</select></td></tr>";
    }
    
    /**
     * Renders a position cell with dropdown
     * 
     * @param array $player Player data
     * @param string $posKey Position key (pg, sg, sf, pf, c)
     * @param int $depthCount Row counter
     * @return void Echoes HTML
     */
    private function renderPositionCell(array $player, string $posKey, int $depthCount): void
    {
        $fieldName = $posKey . $depthCount;
        $dcField = 'dc_' . strtoupper($posKey) . 'Depth';
        $currentValue = $player[$dcField];
        
        echo "<td><select name=\"$fieldName\">";
        $this->renderPositionOptions($currentValue);
        echo "</select></td>";
    }
    
    /**
     * Renders the form footer with submit button
     * 
     * @return void Echoes HTML
     */
    public function renderFormFooter(): void
    {
        echo "<tr>
            <th colspan=14><input type=\"radio\" checked> Submit Depth Chart? <input type=\"submit\" value=\"Submit\"></th>
        </tr></form></table></center>";
    }
    
    /**
     * Renders the submission result page
     * 
     * @param string $teamName Team name
     * @param array $playerData Player data
     * @param bool $success Whether submission was successful
     * @param string $errorHtml Error messages HTML (if any)
     * @return void Echoes HTML
     */
    public function renderSubmissionResult(string $teamName, array $playerData, bool $success, string $errorHtml = ''): void
    {
        if (!$success) {
            echo "<center><u>Your lineup has <b>not</b> been submitted:</u></center><br>";
            echo $errorHtml;
        } else {
            echo "<center><u>Your depth chart has been submitted and e-mailed successfully. Thank you.</u></center><p>";
        }
        
        // Display submitted data
        echo "$teamName Depth Chart Submission<br><table>";
        echo "<tr>
            <td><b>Name</td>";
        foreach (\JSB::PLAYER_POSITIONS as $position) {
            echo "<td><b>$position</td>";
        }
        echo "<td><b>Active</td>
            <td><b>Min</td>
            <td><b>OF</td>
            <td><b>DF</td>
            <td><b>OI</td>
            <td><b>DI</td>
            <td><b>BH</td>
        </tr>";
        
        foreach ($playerData as $player) {
            echo "<tr>
                <td>{$player['name']}</td>";
            foreach (\JSB::PLAYER_POSITIONS as $position) {
                $posKey = strtolower($position);
                echo "<td>{$player[$posKey]}</td>";
            }
            echo "<td>{$player['active']}</td>
                <td>{$player['min']}</td>
                <td>{$player['of']}</td>
                <td>{$player['df']}</td>
                <td>{$player['oi']}</td>
                <td>{$player['di']}</td>
                <td>{$player['bh']}</td>
            </tr>";
        }
        
        echo "</table>";
    }
}
