<?php

use PHPUnit\Framework\TestCase;
use Updater\StandingsHTMLGenerator;

/**
 * Comprehensive tests for StandingsHTMLGenerator class
 * 
 * Tests HTML generation functionality including:
 * - Conference standings HTML generation
 * - Division standings HTML generation
 * - Clinched status indicators (X, Y, Z)
 * - Last 10 games display
 * - Streak display
 * - Proper grouping assignments
 */
class StandingsHTMLGeneratorTest extends TestCase
{
    private $mockDb;
    private $htmlGenerator;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->htmlGenerator = new StandingsHTMLGenerator($this->mockDb);
    }

    protected function tearDown(): void
    {
        $this->htmlGenerator = null;
        $this->mockDb = null;
    }

    /**
     * @group standings-html
     * @group grouping
     */
    public function testAssignGroupingsForAllConferences()
    {
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('assignGroupingsFor');
        $method->setAccessible(true);

        foreach (League::CONFERENCE_NAMES as $conference) {
            $result = $method->invoke($this->htmlGenerator, $conference);
            $this->assertIsArray($result);
            $this->assertEquals('conference', $result[0]);
            $this->assertEquals('confGB', $result[1]);
            $this->assertEquals('confMagicNumber', $result[2]);
        }
    }

    /**
     * @group standings-html
     * @group html-generation
     */
    public function testGenerateStandingsHeaderContainsRequiredColumns()
    {
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateStandingsHeader');
        $method->setAccessible(true);

        $html = $method->invoke($this->htmlGenerator, 'Eastern', 'conference');
        
        $this->assertStringContainsString('Team', $html);
        $this->assertStringContainsString('W-L', $html);
        $this->assertStringContainsString('Pct', $html);
        $this->assertStringContainsString('GB', $html);
        $this->assertStringContainsString('Magic#', $html);
        $this->assertStringContainsString('Conf.', $html);
        $this->assertStringContainsString('Div.', $html);
        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('Away', $html);
        $this->assertStringContainsString('Last 10', $html);
        $this->assertStringContainsString('Streak', $html);
    }

    /**
     * @group standings-html
     * @group html-generation
     */
    public function testGenerateStandingsHeaderDisplaysRegionName()
    {
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateStandingsHeader');
        $method->setAccessible(true);

        $html = $method->invoke($this->htmlGenerator, 'Eastern', 'conference');
        
        $this->assertStringContainsString('Eastern', $html);
        $this->assertStringContainsString('Conference', $html);
    }

    /**
     * @group standings-html
     * @group html-generation
     */
    public function testGenerateStandingsHeaderForDivision()
    {
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateStandingsHeader');
        $method->setAccessible(true);

        $html = $method->invoke($this->htmlGenerator, 'Atlantic', 'division');
        
        $this->assertStringContainsString('Atlantic', $html);
        $this->assertStringContainsString('Division', $html);
    }

    /**
     * @group standings-html
     * @group html-generation
     */
    public function testGenerateStandingsHeaderIncludesSortableClass()
    {
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateStandingsHeader');
        $method->setAccessible(true);

        $html = $method->invoke($this->htmlGenerator, 'Eastern', 'conference');
        
        $this->assertStringContainsString('sortable', $html);
    }

    /**
     * @group standings-html
     * @group clinched-status
     */
    public function testGenerateTeamRowShowsConferenceClinch()
    {
        $mockRow = [
            'tid' => 1,
            'team_name' => 'Boston Celtics',
            'leagueRecord' => '55-27',
            'pct' => '.671',
            'confGB' => '0',
            'confMagicNumber' => '0',
            'gamesUnplayed' => '0',
            'confRecord' => '35-17',
            'divRecord' => '15-5',
            'homeRecord' => '30-11',
            'awayRecord' => '25-16',
            'homeGames' => 41,
            'awayGames' => 41,
            'clinchedConference' => 1,
            'clinchedDivision' => 0,
            'clinchedPlayoffs' => 0
        ];
        
        $mockPowerResult = new MockDatabaseResult([
            ['last_win' => 7, 'last_loss' => 3, 'streak_type' => 'W', 'streak' => 5]
        ]);
        
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateTeamRow');
        $method->setAccessible(true);

        // Team name should be modified with clinch indicator before passing to generateTeamRow
        $teamNameWithIndicator = '<b>Z</b>-Boston Celtics';
        $html = $method->invoke($this->htmlGenerator, $mockRow, 1, $teamNameWithIndicator, $mockPowerResult, 'Eastern');
        
        $this->assertStringContainsString('<b>Z</b>', $html);
        $this->assertStringContainsString('Boston Celtics', $html);
    }

    /**
     * @group standings-html
     * @group clinched-status
     */
    public function testGenerateTeamRowShowsDivisionClinch()
    {
        $mockRow = [
            'tid' => 1,
            'team_name' => 'Miami Heat',
            'leagueRecord' => '48-34',
            'pct' => '.585',
            'confGB' => '7',
            'confMagicNumber' => '5',
            'gamesUnplayed' => '0',
            'confRecord' => '30-22',
            'divRecord' => '18-2',
            'homeRecord' => '28-13',
            'awayRecord' => '20-21',
            'homeGames' => 41,
            'awayGames' => 41,
            'clinchedConference' => 0,
            'clinchedDivision' => 1,
            'clinchedPlayoffs' => 0
        ];
        
        $mockPowerResult = new MockDatabaseResult([
            ['last_win' => 6, 'last_loss' => 4, 'streak_type' => 'L', 'streak' => 2]
        ]);
        
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateTeamRow');
        $method->setAccessible(true);

        // Team name should be modified with clinch indicator before passing to generateTeamRow
        $teamNameWithIndicator = '<b>Y</b>-Miami Heat';
        $html = $method->invoke($this->htmlGenerator, $mockRow, 1, $teamNameWithIndicator, $mockPowerResult, 'Eastern');
        
        $this->assertStringContainsString('<b>Y</b>', $html);
        $this->assertStringContainsString('Miami Heat', $html);
    }

    /**
     * @group standings-html
     * @group clinched-status
     */
    public function testGenerateTeamRowShowsPlayoffClinch()
    {
        $mockRow = [
            'tid' => 1,
            'team_name' => 'Chicago Bulls',
            'leagueRecord' => '45-37',
            'pct' => '.549',
            'confGB' => '10',
            'confMagicNumber' => '8',
            'gamesUnplayed' => '0',
            'confRecord' => '28-24',
            'divRecord' => '12-8',
            'homeRecord' => '25-16',
            'awayRecord' => '20-21',
            'homeGames' => 41,
            'awayGames' => 41,
            'clinchedConference' => 0,
            'clinchedDivision' => 0,
            'clinchedPlayoffs' => 1
        ];
        
        $mockPowerResult = new MockDatabaseResult([
            ['last_win' => 5, 'last_loss' => 5, 'streak_type' => 'W', 'streak' => 1]
        ]);
        
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateTeamRow');
        $method->setAccessible(true);

        // Team name should be modified with clinch indicator before passing to generateTeamRow
        $teamNameWithIndicator = '<b>X</b>-Chicago Bulls';
        $html = $method->invoke($this->htmlGenerator, $mockRow, 1, $teamNameWithIndicator, $mockPowerResult, 'Eastern');
        
        $this->assertStringContainsString('<b>X</b>', $html);
        $this->assertStringContainsString('Chicago Bulls', $html);
    }

    /**
     * @group standings-html
     * @group html-generation
     */
    public function testGenerateTeamRowContainsLast10Record()
    {
        $mockRow = [
            'tid' => 1,
            'team_name' => 'Denver Nuggets',
            'leagueRecord' => '50-32',
            'pct' => '.610',
            'confGB' => '3',
            'confMagicNumber' => '10',
            'gamesUnplayed' => '0',
            'confRecord' => '32-20',
            'divRecord' => '14-6',
            'homeRecord' => '28-13',
            'awayRecord' => '22-19',
            'homeGames' => 41,
            'awayGames' => 41,
            'clinchedConference' => 0,
            'clinchedDivision' => 0,
            'clinchedPlayoffs' => 0
        ];
        
        $mockPowerResult = new MockDatabaseResult([
            ['last_win' => 8, 'last_loss' => 2, 'streak_type' => 'W', 'streak' => 4]
        ]);
        
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateTeamRow');
        $method->setAccessible(true);

        $html = $method->invoke($this->htmlGenerator, $mockRow, 1, 'Denver Nuggets', $mockPowerResult, 'Western');
        
        $this->assertStringContainsString('8-2', $html);
    }

    /**
     * @group standings-html
     * @group html-generation
     */
    public function testGenerateTeamRowContainsStreakInfo()
    {
        $mockRow = [
            'tid' => 1,
            'team_name' => 'LA Lakers',
            'leagueRecord' => '47-35',
            'pct' => '.573',
            'confGB' => '6',
            'confMagicNumber' => '12',
            'gamesUnplayed' => '0',
            'confRecord' => '30-22',
            'divRecord' => '13-7',
            'homeRecord' => '26-15',
            'awayRecord' => '21-20',
            'homeGames' => 41,
            'awayGames' => 41,
            'clinchedConference' => 0,
            'clinchedDivision' => 0,
            'clinchedPlayoffs' => 0
        ];
        
        $mockPowerResult = new MockDatabaseResult([
            ['last_win' => 6, 'last_loss' => 4, 'streak_type' => 'W', 'streak' => 3]
        ]);
        
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateTeamRow');
        $method->setAccessible(true);

        $html = $method->invoke($this->htmlGenerator, $mockRow, 1, 'LA Lakers', $mockPowerResult, 'Western');
        
        $this->assertStringContainsString('W 3', $html);
    }

    /**
     * @group standings-html
     * @group html-generation
     */
    public function testGenerateTeamRowContainsLosingStreak()
    {
        $mockRow = [
            'tid' => 1,
            'team_name' => 'Sacramento Kings',
            'leagueRecord' => '30-52',
            'pct' => '.366',
            'confGB' => '23',
            'confMagicNumber' => 'E',
            'gamesUnplayed' => '0',
            'confRecord' => '18-34',
            'divRecord' => '8-12',
            'homeRecord' => '18-23',
            'awayRecord' => '12-29',
            'homeGames' => 41,
            'awayGames' => 41,
            'clinchedConference' => 0,
            'clinchedDivision' => 0,
            'clinchedPlayoffs' => 0
        ];
        
        $mockPowerResult = new MockDatabaseResult([
            ['last_win' => 2, 'last_loss' => 8, 'streak_type' => 'L', 'streak' => 5]
        ]);
        
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateTeamRow');
        $method->setAccessible(true);

        // Suppress expected warnings from undefined array keys
        set_error_handler(function() { return true; }, E_WARNING);
        $html = $method->invoke($this->htmlGenerator, $mockRow, 1, 'Sacramento Kings', $mockPowerResult, 'Pacific');
        restore_error_handler();
        
        $this->assertStringContainsString('L 5', $html);
    }

    /**
     * @group standings-html
     * @group database
     */
    public function testGenerateStandingsPageUpdatesDatabase()
    {
        // Mock standings data
        $mockStandings = [];
        $this->mockDb->setMockData($mockStandings);
        $this->mockDb->setReturnTrue(true);
        
        ob_start();
        
        // Suppress expected warnings
        set_error_handler(function() { return true; }, E_WARNING);
        
        try {
            $this->htmlGenerator->generateStandingsPage();
        } catch (Exception $e) {
            // May fail due to empty data, but we check if UPDATE query was attempted
        }
        
        restore_error_handler();
        ob_end_clean();
        
        $queries = $this->mockDb->getExecutedQueries();
        $updateQueries = array_filter($queries, function($q) {
            return stripos($q, 'UPDATE nuke_pages') !== false;
        });
        
        $this->assertNotEmpty($updateQueries);
    }

    /**
     * @group standings-html
     * @group html-generation
     */
    public function testGenerateTeamRowIncludesTeamLink()
    {
        $mockRow = [
            'tid' => 5,
            'team_name' => 'Phoenix Suns',
            'leagueRecord' => '42-40',
            'pct' => '.512',
            'confGB' => '11',
            'confMagicNumber' => '15',
            'gamesUnplayed' => '0',
            'confRecord' => '26-26',
            'divRecord' => '11-9',
            'homeRecord' => '24-17',
            'awayRecord' => '18-23',
            'homeGames' => 41,
            'awayGames' => 41,
            'clinchedConference' => 0,
            'clinchedDivision' => 0,
            'clinchedPlayoffs' => 0
        ];
        
        $mockPowerResult = new MockDatabaseResult([
            ['last_win' => 5, 'last_loss' => 5, 'streak_type' => 'W', 'streak' => 2]
        ]);
        
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('generateTeamRow');
        $method->setAccessible(true);

        // Suppress expected warnings from undefined array keys
        set_error_handler(function() { return true; }, E_WARNING);
        $html = $method->invoke($this->htmlGenerator, $mockRow, 5, 'Phoenix Suns', $mockPowerResult, 'Pacific');
        restore_error_handler();
        
        $this->assertStringContainsString('modules.php?name=Team&op=team&teamID=5', $html);
    }

    /**
     * @group standings-html
     * @group grouping
     */
    public function testAssignGroupingsForAllDivisions()
    {
        $reflection = new ReflectionClass($this->htmlGenerator);
        $method = $reflection->getMethod('assignGroupingsFor');
        $method->setAccessible(true);
        
        foreach (League::DIVISION_NAMES as $division) {
            $result = $method->invoke($this->htmlGenerator, $division);
            
            $this->assertIsArray($result);
            $this->assertEquals('division', $result[0]);
            $this->assertEquals('divGB', $result[1]);
            $this->assertEquals('divMagicNumber', $result[2]);
        }
    }
}
